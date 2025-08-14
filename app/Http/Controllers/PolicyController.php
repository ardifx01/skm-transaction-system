<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'no_blanko' => 'required|string|unique:policies,no_blanko',
            'no_policy' => 'required|string',
            'consignee' => 'required|string',
            'no_bl' => 'required|string',
            'shipping_carrier' => 'required|string',
            'insured_value' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $policy = Policy::create(array_merge($request->all(), [
            'user_id' => Auth::id(),
            'status' => 'pending_verification', // Status awal setelah submit
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

        // print_r($policy->user_id);

        // Hanya pemilik polis dengan status 'draft' yang bisa mengedit
        // if (Auth::user()->id !== $policy->user_id || $policy->status !== 'draft') {
        //     return abort(403);
        // }
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policy->update($request->all());

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

    public function verify(Request $request, Policy $policy)
{
    $this->authorize('verify-polis');

    $policy->update([
        'status' => $request->action === 'approve' ? 'verified' : 'rejected',
        'verification_reason' => $request->reason ?? null,
        'updated_by' => Auth::id(),
    ]);

    return redirect()->route('policies.show', $policy->id)
                     ->with('success', 'Policy updated successfully');
}

public function confirmPayment(Policy $policy)
{
    $this->authorize('confirm-payment');

    $policy->update([
        'status' => 'paid',
        'updated_by' => Auth::id(),
    ]);

    return redirect()->route('policies.show', $policy->id)
                     ->with('success', 'Payment confirmed');
}

public function sendMail(Policy $policy)
{
    // Cek permission user
    $this->authorize('send-polis-mail');

    // Logika kirim email
    \Mail::to($policy->user->email)->send(new \App\Mail\PolicyNotification($policy));

    return back()->with('success', 'Email sent successfully!');
}


}
