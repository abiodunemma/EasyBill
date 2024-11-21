<?php
 namespace App\Http\Controllers;

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Http;

 class PaystackController extends Controller
 {

     public function initializePayment(Request $request)
     {

         $request->validate([
             'email' => 'required|email',
             'amount' => 'required|numeric',
         ]);

         $email = $request->email;
         $amount = $request->amount * 100;


         $response = Http::withHeaders([
             'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
         ])->post(env('PAYSTACK_PAYMENT_URL') . '/transaction/initialize', [
             'email' => $email,
             'amount' => $amount,
             'callback_url' => env('PAYSTACK_CALLBACK_URL')
         ]);


         $data = $response->json();


         if ($data['status'] && isset($data['data']['authorization_url'])) {

             return response()->json([
                 'status' => true,
                 'authorization_url' => $data['data']['authorization_url'],
                 'reference' => $data['data']['reference'],
             ]);
         } else {
             
             return response()->json([
                 'status' => false,
                 'message' => 'Failed to initialize payment.',
             ], 400);
         }
     }


     public function handlePaymentCallback(Request $request)
     {
         $reference = $request->input('reference');


         $response = Http::withHeaders([
             'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
         ])->get(env('PAYSTACK_PAYMENT_URL') . '/transaction/verify/' . $reference);

         $data = $response->json();


         if ($data['status'] && isset($data['data']['status']) && $data['data']['status'] == 'success') {

             return response()->json([
                 'status' => true,
                 'message' => 'Payment successful!',
                 'data' => $data['data'],
             ]);
         } else {

             return response()->json([
                 'status' => false,
                 'message' => 'Payment failed.',
             ], 400);
         }
     }
 }
