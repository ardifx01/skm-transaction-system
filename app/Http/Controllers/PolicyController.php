<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MultiSheetReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class PolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Policy::query();
        $user = Auth::user();

        // Tampilkan hanya polis milik user jika rolenya customer
        if ($user->hasRole('customer')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('search')) {
            $query->where('no_policy', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $policies = $query->paginate(10);
        
        if ($request->ajax()) {
            return view('policies._table', compact('policies'))->render();
        }

        return view('policies.index', compact('policies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('policies.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_blanko' => 'required|string|unique:policies,no_blanko',
            'no_policy' => 'required|string',
            'consignee' => 'required|string',
            'no_bl' => 'required|string',
            'shipping_carrier' => 'required|string',
            'insured_value' => 'required|numeric',
            'currency' => 'required|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'transhipment_at' => 'nullable|string',
            'value_at' => 'nullable|string',
            'interest_insured' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $policy = Policy::create(array_merge($request->all(), [
            'user_id' => Auth::id(),
            'status' => 'pending_verification', // Status awal setelah submit
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        return response()->json(['success' => 'Polis berhasil diinput!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Policy $policy)
    {
        // Tampilkan detail polis untuk admin atau customer pemilik
        $user = Auth::user();
        if ($user->hasRole('customer') && $policy->user_id !== $user->id) {
            return abort(403);
        }

        return view('policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Policy $policy)
{
    $user = Auth::user();

    // 🔹 Admin bisa edit semua polis
    if ($user->hasRole('admin')) {
        return view('policies.form', compact('policy'));
    }

    // 🔹 User biasa hanya bisa edit miliknya sendiri dan status tertentu
    if ($user->id === $policy->user_id && $policy->status === 'pending_verification') {
        return view('policies.form', compact('policy'));
    }

    // 🔹 Selain itu, forbidden
    abort(403, 'You are not authorized to edit this policy.');
}

    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Policy $policy)
    {
        $validator = Validator::make($request->all(), [
            'no_blanko' => 'required|string|unique:policies,no_blanko,' . $policy->id,
            'no_policy' => 'required|string',
            'consignee' => 'required|string',
            'no_bl' => 'required|string',
            'shipping_carrier' => 'required|string',
            'insured_value' => 'required|numeric',
            'currency' => 'required|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'transhipment_at' => 'nullable|string',
            'value_at' => 'nullable|string',
            'interest_insured' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policy->update(array_merge($request->all(), [
            'updated_by' => Auth::id(),
        ]));

        return response()->json(['success' => 'Polis berhasil diperbarui!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Policy $policy)
    {
        // Hanya pemilik polis atau admin yang bisa menghapus
        if (Auth::user()->id !== $policy->user_id && !Auth::user()->hasRole('admin')) {
            return abort(403);
        }

        $policy->delete();
        return response()->json(['success' => 'Polis berhasil dihapus!']);
    }
    
    /**
     * Handle policy verification (for Admin).
     */
    public function verify(Request $request, Policy $policy)
    {
        // Hanya admin yang bisa memverifikasi
        $this->authorize('policies-edit');

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:verified,rejected',
            'no_policy' => 'required_if:status,verified|string',
            'certificate_no' => 'required_if:status,verified|string',
            'date_of_issue' => 'required_if:status,verified|date',
            'vessel_reg' => 'required_if:status,verified|array',
            'vessel_reg.*' => 'required_if:status,verified|string',
            'sailing_date' => 'required_if:status,verified|date',
            'premium_price' => 'required_if:status,verified|numeric',
            'value_at' => 'required_if:status,verified|string',
            'verification_reason' => 'required_if:status,rejected|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'transhipment_at' => 'nullable|string',
            'interest_insured' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $data = $request->except('vessel_reg');
        
        // Mengubah array vessel_reg menjadi string dengan pemisah koma
        if ($request->has('vessel_reg')) {
            $data['vessel_reg'] = implode(', ', $request->input('vessel_reg', []));
        }

        $policy->update(array_merge($data, [
            'updated_by' => Auth::id(),
        ]));

        return response()->json(['success' => 'Policy verified successfully!']);
    }

    /**
     * Handle payment confirmation (for Admin).
     */
    public function confirmPayment(Policy $policy)
    {
        // Hanya admin yang bisa mengkonfirmasi pembayaran
        $this->authorize('policies-approve-payment');

        $policy->update([
            'status' => 'paid',
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => 'Payment confirmed!']);
    }

    /**
     * Handle payment proof upload (for Customer).
     */
    public function uploadPayment(Request $request, Policy $policy)
    {
        // Hanya pemilik polis yang bisa mengunggah bukti bayar
        $this->authorize('policies-upload-payment', $policy);

        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|file|mimes:jpeg,png,pdf|max:2048', // Maks 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('public/payment_proofs');
            
            $policy->update([
                'payment_proof' => str_replace('public/', '', $path),
                'status' => 'pending_payment',
                'updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['success' => 'Payment proof uploaded successfully!']);
    }

        //  public function exportExcel(Policy $policy)
        // {
        //     // Eager load relasi 'details' dan 'user' di awal
        //     $policy->load('details', 'user');

        //     // --- Data for the "Polis VIDE" sheet ---
        //     $polisVideData = [
        //         'certificate_no' => $policy->certificate_no ?? '-',
        //         'date_of_issue' => $policy->date_of_issue ?? '-',
        //         'the_assured' => $policy->assured_name ?? '-',
        //         'consignee' => $policy->consignee ?? '-',
        //         'consignee_address' => $policy->consignee_address ?? '-',
        //         'transhipment_at' => $policy->transhipment_at ?? '-',
        //         'from' => $policy->from ?? '-',
        //         'to' => $policy->to ?? '-',
        //         'shipping_carrier' => $policy->shipping_carrier ?? '-',
        //         'vessel_reg' => $policy->vessel_reg ?? '-',
        //         'sailing_date' => $policy->sailing_date ?? '-',
        //         'currency' => $policy->currency ?? '-',
        //         'insured_value' => $policy->insured_value ?? 0,
        //         'interest_insured' => $policy->interest_insured ?? '-',
        //     ];

        //     // --- Data for the "Daftar Laporan VIDE" sheet ---
        //     $laporanVideCollection = $policy->details ?? collect();
        //     $laporanVideData = $laporanVideCollection->map(function ($detail) {
        //         return [
        //             'no_blanko' => $detail->no_blanko ?? '-',
        //             'no_polis' => $detail->no_policy ?? '-',
        //             'consignee' => $detail->consignee ?? '-',
        //             'no_bl' => $detail->no_bl ?? '-',
        //             'alat_pengangkut' => $detail->shipping_carrier ?? '-',
        //             'nilai_pertanggungan' => $detail->insured_value ?? 0,
        //         ];
        //     });

        //     // Data header untuk Laporan VIDEI (sesuai permintaan user)
        //     $laporanVideReportData = [
        //         'tgl_ambil' => now()->format('d M Y'),
        //         'no_registrasi' => 'MC ' . ($policy->no_blanko ?? '-') . ' - MC ' . ($policy->no_policy ?? '-'),
        //     ];

        //     // --- Data for the "Kwitansi" sheet ---
        //     $policy->load('user');
        //     $kwitansiData = [
        //         'user_name' => $policy->user->name ?? '-',
        //         'premium_price_in_words' => $policy->premium_price_in_words ?? '-',
        //         'no_policy' => $policy->no_policy ?? '-',
        //         'premium_price' => $policy->premium_price ?? 0,
        //     ];

        //     // Gabungkan semua
        //     $allData = [
        //         'polis_videi' => $polisVideData,
        //         'laporan_videi_data' => $laporanVideData,
        //         'laporan_videi_report_data' => $laporanVideReportData,
        //         'kwitansi' => $kwitansiData,
        //     ];

        //     $filename = 'policy_report_' . $policy->id . '.xlsx';
        //     return Excel::download(new MultiSheetReportExport($allData), $filename);
        // }

        public function exportExcel(Policy $policy)
{
    // Eager load relasi 'user'
    $policy->load('user');

    // --- Data untuk sheet "Polis VIDE" ---
    $polisVideData = [
        'certificate_no'   => $policy->certificate_no ?? '-',
        'date_of_issue'    => $policy->date_of_issue ?? '-',
        'the_assured'      => $policy->consignee ?? '-',
        'consignee'        => $policy->consignee ?? '-',
        'consignee_address'=> '-',
        'transhipment_at'  => $policy->transhipment_at ?? '-',
        'from'             => $policy->from ?? '-',
        'to'               => $policy->to ?? '-',
        'shipping_carrier' => $policy->shipping_carrier ?? '-',
        'vessel_reg'       => $policy->vessel_reg ?? '-',
        'sailing_date'     => $policy->sailing_date ?? '-',
        'currency'         => $policy->currency ?? '-',
        'insured_value'    => $policy->insured_value ?? 0,
        'interest_insured' => $policy->interest_insured ?? '-',
    ];

    // --- Data untuk sheet "Daftar Laporan VIDE" ---
    $laporanVideCollection = collect([$policy]);
    $laporanVideData = $laporanVideCollection->map(function ($detail) {
        return [
            'no_blanko'          => $detail->no_blanko ?? '-',
            'no_polis'           => $detail->no_policy ?? '-',
            'consignee'          => $detail->consignee ?? '-',
            'no_bl'              => $detail->no_bl ?? '-',
            'alat_pengangkut'    => $detail->shipping_carrier ?? '-',
            'nilai_pertanggungan'=> $detail->insured_value ?? 0,
        ];
    });

    // --- Data header untuk "Laporan VIDE" ---
    $laporanVideReportData = [
        'tgl_ambil'     => now()->format('d M Y'),
        'no_registrasi' => 'MC ' . ($policy->no_blanko ?? '-') . ' - MC ' . ($policy->no_policy ?? '-'),
    ];

    // --- Data untuk sheet "Kwitansi" ---
    $premium = $policy->premium_price ?? 0;
    $kwitansiData = [
        'user_name'              => $policy->user->name ?? '-',
        'premium_price_in_words' => $premium > 0 ? strtoupper($this->terbilang($premium)) . " RUPIAH" : '-',
        'no_policy'              => $policy->no_policy ?? '-',
        'premium_price'          => $premium,
    ];

    // Gabungkan semua data
    $allData = [
        'polis_videi'              => $polisVideData,
        'laporan_videi_data'       => $laporanVideData,
        'laporan_videi_report_data'=> $laporanVideReportData,
        'kwitansi'                 => $kwitansiData,
    ];

    $filename = 'policy_report_' . $policy->id . '.xlsx';
    return Excel::download(new MultiSheetReportExport($allData), $filename);
}

/**
 * Convert angka ke tulisan terbilang (Bahasa Indonesia)
 */
private function terbilang($number)
{
    $number = abs($number);
    $words = ["", "SATU", "DUA", "TIGA", "EMPAT", "LIMA", "ENAM", "TUJUH", "DELAPAN", "SEMBILAN", "SEPULUH", "SEBELAS"];
    $temp = "";

    if ($number < 12) {
        $temp = " " . $words[$number];
    } else if ($number < 20) {
        $temp = $this->terbilang($number - 10) . " BELAS";
    } else if ($number < 100) {
        $temp = $this->terbilang(intval($number / 10)) . " PULUH" . $this->terbilang($number % 10);
    } else if ($number < 200) {
        $temp = " SERATUS" . $this->terbilang($number - 100);
    } else if ($number < 1000) {
        $temp = $this->terbilang(intval($number / 100)) . " RATUS" . $this->terbilang($number % 100);
    } else if ($number < 2000) {
        $temp = " SERIBU" . $this->terbilang($number - 1000);
    } else if ($number < 1000000) {
        $temp = $this->terbilang(intval($number / 1000)) . " RIBU" . $this->terbilang($number % 1000);
    } else if ($number < 1000000000) {
        $temp = $this->terbilang(intval($number / 1000000)) . " JUTA" . $this->terbilang($number % 1000000);
    } else if ($number < 1000000000000) {
        $temp = $this->terbilang(intval($number / 1000000000)) . " MILYAR" . $this->terbilang($number % 1000000000);
    } else {
        $temp = "TERLALU BESAR";
    }

    return trim($temp);
}


 public function exportPdf(Policy $policy)
    {
        // Eager load relasi user
        $policy->load('user');

        // --- Data untuk halaman Polis VIDEI ---
        $polisVideData = [
            'certificate_no'   => $policy->certificate_no ?? '-',
            'date_of_issue'    => $policy->date_of_issue ?? '-',
            'the_assured'      => $policy->consignee ?? '-',
            'consignee'        => $policy->consignee ?? '-',
            'transhipment_at'  => $policy->transhipment_at ?? '-',
            'from'             => $policy->from ?? '-',
            'to'               => $policy->to ?? '-',
            'shipping_carrier' => $policy->shipping_carrier ?? '-',
            'vessel_reg'       => $policy->vessel_reg ?? '-',
            'sailing_date'     => $policy->sailing_date ?? '-',
            'currency'         => $policy->currency ?? '-',
            'insured_value'    => $policy->insured_value ?? 0,
            'interest_insured' => $policy->interest_insured ?? '-',
        ];

        // --- Data untuk halaman Laporan VIDEI ---
        $laporanVideCollection = collect([$policy]);
        $laporanVideData = $laporanVideCollection->map(function ($detail) {
            return [
                'no_blanko'          => $detail->no_blanko ?? '-',
                'no_polis'           => $detail->no_policy ?? '-',
                'consignee'          => $detail->consignee ?? '-',
                'no_bl'              => $detail->no_bl ?? '-',
                'alat_pengangkut'    => $detail->shipping_carrier ?? '-',
                'nilai_pertanggungan'=> $detail->insured_value ?? 0,
            ];
        });

        $laporanVideReportData = [
            'tgl_ambil'     => now()->format('d M Y'),
            'no_registrasi' => 'MC ' . ($policy->no_blanko ?? '-') . ' - MC ' . ($policy->no_policy ?? '-'),
        ];

        // --- Data untuk halaman Invoice/Kwitansi ---
        $premium = $policy->premium_price ?? 0;
        $kwitansiData = [
            'user_name'              => $policy->user->name ?? '-',
            'premium_price_in_words' => $premium > 0 ? strtoupper($this->terbilang($premium)) . " RUPIAH" : '-',
            'no_policy'              => $policy->no_policy ?? '-',
            'premium_price'          => $premium,
        ];

        // Render HTML 3 halaman
        $html = view('pdf.pdf_master_view', compact(
            'polisVideData',
            'laporanVideData',
            'laporanVideReportData',
            'kwitansiData'
        ))->render();
        // Generate PDF
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->download('policy_' . $policy->id . '.pdf');
    }

}
