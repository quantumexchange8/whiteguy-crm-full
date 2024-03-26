<?php

namespace App\Http\Controllers;

use App\Models\PaymentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PaymentSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the flashed messages from the session
        $errors = $request->session()->get('errors');
        $errorMsg = $request->session()->get('errorMsg');

        // Clear the flashed messages from the session
        $request->session()->forget('errors');
        $request->session()->forget('errorMsg');
        $request->session()->save();

        if (isset($errorMsg)) {
            return Inertia::render('CRM/PaymentSubmissions/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }

        return Inertia::render('CRM/PaymentSubmissions/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getPaymentSubmissions(Request $request)
    {
        $data = PaymentSubmission::with(['user', 'paymentMethod:id,title', 'user.site'])->get();

        // dd($data);
        // foreach ($data as $payment) {
        //     // Handle client_stage attribute
        //     if (!is_null($payment->client_stage) && $payment->client_stage !== '') {
        //         $payment->client_stage = $client_stages[$payment->client_stage - 1];
        //     }
        // }

        return response()->json($data);
    }
}
