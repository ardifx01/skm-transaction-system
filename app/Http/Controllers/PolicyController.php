<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MultiSheetReportExport;

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
        // Hanya pemilik polis dengan status 'draft' yang bisa mengedit
        if (Auth::user()->id !== $policy->user_id || $policy->status !== 'draft') {
            return abort(403);
        }
        return view('policies.form', compact('policy'));
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
        $this->authorize('edit-policies');

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
        $this->authorize('confirm-payment');

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
        $this->authorize('upload-payment-proof', $policy);

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

public function exportExcel(Request $request)
    {
        $policyId = $request->query('policy');

        // Cek apakah ID ada
        if (!$policyId) {
            return redirect()->back()->with('error', 'Policy ID is missing.');
        }
        
        // Cari model Policy berdasarkan ID
        $policy = Policy::find($policyId);
        
        // Cek apakah model Policy ditemukan
        if (!$policy) {
            return redirect()->back()->with('error', 'Policy not found.');
        }

        // --- Data for the "Polis VIDE" sheet ---
        // You need to ensure every key here exists in your Policy model
        $polisVideData = [
            'certificate_no' => $policy->certificate_no ?? '-',
            'date_of_issue' => $policy->date_of_issue ?? '-',
            'the_assured' => $policy->assured_name ?? '-',
            'consignee' => $policy->consignee ?? '-',
            'consignee_address' => $policy->consignee_address ?? '-',
            'transhipment_at' => $policy->transhipment_at ?? '-',
            'from' => $policy->from ?? '-',
            'to' => $policy->to ?? '-',
            'shipping_carrier' => $policy->shipping_carrier ?? '-',
            'vessel_reg' => $policy->vessel_reg ?? '-',
            'sailing_date' => $policy->sailing_date ?? '-',
            'currency' => $policy->currency ?? '-',
            'insured_value' => $policy->insured_value ?? 0,
            'interest_insured' => $policy->interest_insured ?? '-',
        ];

        // --- Data for the "Daftar Laporan VIDE" sheet ---
        // Asumsi ada relasi `details` di model Policy yang mengembalikan Collection
        $laporanVideCollection = $policy->details ?? collect();
        
        $laporanVideData = $laporanVideCollection->map(function ($detail) {
            return [
                $detail->no_blanko ?? '-',
                $detail->no_polis ?? '-',
                $detail->consignee_name ?? '-',
                $detail->no_bl ?? '-',
                $detail->alat_pengangkut ?? '-',
                $detail->nilai_penanggungan ?? 0,
            ];
        });

        // --- Data for the "Kwitansi" sheet ---
        // Make sure the `user` relationship is loaded.
        $policy->load('user');
        $kwitansiData = [
            'user_name' => $policy->user->name ?? '-',
            'premium_price_in_words' => $policy->premium_price_in_words ?? '-',
            'no_policy' => $policy->no_policy ?? '-',
            'premium_price' => $policy->premium_price ?? 0,
        ];

        // The data passed to MultiSheetReportExport
        $allData = [
            'polis_vide' => $polisVideData,
            'laporan_vide' => $laporanVideData,
            'kwitansi' => $kwitansiData,
        ];

        $filename = 'policy_report_' . $policy->id . '.xlsx';
        return Excel::download(new MultiSheetReportExport($allData), $filename);
    }
}
