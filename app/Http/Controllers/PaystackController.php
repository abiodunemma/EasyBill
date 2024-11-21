<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaystackController extends Controller
{
    public function initializePayment(Request $request)
    {
        $amount = $request->input('amount');
        $email = $request->input('email');

        $publicKey = env('PAYSTACK_PUBLIC_KEY');
        $secretKey = env('PAYSTACK_SECRET_KEY');
        $callbackUrl = env('PAYSTACK_CALLBACK_URL');

        // Make a request to Paystack to initialize the payment
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => $email,
            'amount' => $amount,
            'callback_url' => $callbackUrl,
        ]);

        $data = $response->json();

        if ($data['status'] === true) {
            return redirect($data['data']['authorization_url']);
        }

        return back()->with('error', 'Payment initialization failed');
    }


    public function handlePaymentCallback(Request $request)
    {
        $reference = $request->input('reference');
        $secretKey = env('PAYSTACK_SECRET_KEY');


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
        ])->get('https://api.paystack.co/transaction/verify/' . $reference);

        $data = $response->json();

        if ($data['status'] === true && $data['data']['status'] === 'success') {
    
            return response()->json([
                'status' => 'success',
                'message' => 'Payment was successful!',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Payment verification failed!',
        ]);
    }
}
