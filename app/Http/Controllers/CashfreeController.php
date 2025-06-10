<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashfreeController extends Controller
{
    
    public function cashfreeshowCheckout(Request $request)
    {
        $json = base64_decode($request['enc']);
        $data = json_decode($json, true);

        return view('cashfree.checkout', compact('data'));
    }

    public function cashfreenetbanking()
    {
        return view('cashfree.netbanking'); 
    }

    public function cashfreeupi()
    {
        return view('cashfree.upi'); 
    }

    public function cashfreeupimobile()
    {
        return view('cashfree.upimobile'); 
    }

    public function paymentSessionId(Request $request)
    {
        // dd($request);
        $orderId = 'order_' . uniqid();
        $customerId = 'customer_' . uniqid();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-version' => '2022-09-01',
            'x-client-id' => '241099f71f66873add50050ec0990142', // Replace with live key in production
            'x-client-secret' => 'dac5f8581e04422817a017b186387f2e203b06f4'
        ])->post('https://sandbox.cashfree.com/pg/orders', [
            'order_id' => $orderId,
            'order_amount' => $request['amount'],
            'order_currency' => 'INR',
            'order_note' => '#12345',
            'customer_details' => [
                'customer_id' => $customerId,
                'customer_email' => $request['email'],
                'customer_phone' => $request['mobile']
            ],
            'order_meta' => [
                'return_url' => url('/cashfree/redirect') . '?order_id={order_id}&txStatus={txStatus}'
            ]



        ]);

        $responseData = $response->json();
        \Log::info('Cashfree API Response:', $responseData);

        if (isset($responseData['payment_session_id'])) {
            return response()->json([
                'payment_session_id' => $responseData['payment_session_id'],
                'order_id' => $orderId,
            ]);
        } else {
            return response()->json(['error' => 'Payment session ID not found in the response.']);
        }
    }

    public function checkStatus($orderId)
    {
        // Call Cashfree API to get the order status
        $response = Http::withHeaders([
            'x-api-version' => '2022-09-01',
            'x-client-id' => '241099f71f66873add50050ec0990142',  // Use your actual client ID
            'x-client-secret' => 'dac5f8581e04422817a017b186387f2e203b06f4'  // Use your actual client secret
        ])->get("https://sandbox.cashfree.com/pg/orders/{$orderId}");

        // Check if the response is successful
        if ($response->successful()) {
            $orderStatus = $response->json('order_status');  // Can be PAID, FAILED, etc.
            return response()->json(['status' => $orderStatus]);
        }

        // Return an error response if the API call fails
        return response()->json(['status' => 'UNKNOWN'], 500);
    }

    public function handleRedirect(Request $request)
    {
        $orderId = $request->input('order_id') ?? $request->input('orderId');
        \Log::info('Cashfree Redirect Received:', ['order_id' => $orderId]);

        if (!$orderId) {
            return redirect('/cashfree/failed')->with('message', 'Order ID missing from Cashfree redirect.');
        }

        // Fetch the order details
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-version' => '2022-09-01',
            'x-client-id' => '241099f71f66873add50050ec0990142',
            'x-client-secret' => 'dac5f8581e04422817a017b186387f2e203b06f4'
        ])->get("https://sandbox.cashfree.com/pg/orders/{$orderId}");

        $data = $response->json();
        \Log::info('Cashfree Order Status:', $data);

        $orderStatus = strtoupper($data['order_status'] ?? 'UNKNOWN');

        $paymentStatus = null;

        // Fetch payment attempt status
        if (isset($data['payments']['url'])) {
            $paymentDetails = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => '2022-09-01',
                'x-client-id' => '241099f71f66873add50050ec0990142',
                'x-client-secret' => 'dac5f8581e04422817a017b186387f2e203b06f4'
            ])->get($data['payments']['url']);

            $paymentAttempts = $paymentDetails->json();
            \Log::info('Cashfree Payment Attempt Details:', $paymentAttempts);

            if (is_array($paymentAttempts) && count($paymentAttempts)) {
                $paymentStatus = strtoupper($paymentAttempts[0]['payment_status'] ?? 'UNKNOWN');
            }
        }

        // Decide redirection based on payment status (more reliable)
        switch ($paymentStatus) {
            case 'SUCCESS':
            case 'PAID':
                return redirect('/cashfree/success')->with([
                    'message' => 'Payment successful!',
                    'order_id' => $orderId,
                    'amount' => $data['order_amount'] ?? null,
                    'transaction_id' => $paymentAttempts[0]['cf_payment_id'] ?? null,
                    'payment_mode' => $paymentAttempts[0]['payment_method']['card']['card_network'] ?? null
                ]);

            case 'FAILED':
            case 'CANCELLED':
                return redirect('/cashfree/failed')->with([
                    'message' => 'Payment failed or was cancelled.',
                    'order_id' => $orderId,
                    'amount' => $data['order_amount'] ?? null,
                    'transaction_id' => $paymentAttempts[0]['cf_payment_id'] ?? null,
                    'payment_mode' => $paymentAttempts[0]['payment_method']['card']['card_network'] ?? null,
                    'status' => $paymentStatus
                ]);
                // return redirect('/cashfree/failed')->with([
                //     'message' => 'Payment failed or was cancelled.',
                //     'order_id' => $orderId
                // ]);

            case 'PENDING':
                return redirect('/cashfree/pending')->with([
                    'message' => 'Payment is pending. Please wait or try again later.',
                    'order_id' => $orderId,
                    'amount' => $data['order_amount'] ?? null,
                    'transaction_id' => $paymentAttempts[0]['cf_payment_id'] ?? null,
                    'payment_mode' => $paymentAttempts[0]['payment_method']['card']['card_network'] ?? null,
                    'status' => $paymentStatus
                ]);
                // return redirect('/cashfree/pending')->with([
                //     'message' => 'Payment is pending. Please wait or try again later.',
                //     'order_id' => $orderId
                // ]);
        }

        // Fallback if no clear status
        return redirect('/cashfree/failed')->with([
            'message' => "Unable to determine payment status. Please contact support.",
            'order_id' => $orderId
        ]);
    }

    public function success()
    {
        return view('cashfree.success', [
            'order_id' => session('order_id'),
            'transaction_id' => session('transaction_id'), // If you want to pass it
            'amount' => session('amount'),
            'payment_mode' => session('payment_mode'),     // If you pass it
            'status' => 'PAID', // or session('status') if you're passing it
            'message' => session('message')
        ]);
    }


    public function failed()
    {
        return view('cashfree.failed', [
            'order_id' => session('order_id'),
            'transaction_id' => session('transaction_id'),
            'amount' => session('amount'),
            'payment_mode' => session('payment_mode'),
            'status' => session('status'),
            'message' => session('message')
        ]);
    }


    public function pending()
    {
        return view('cashfree.pending', [
            'order_id' => session('order_id'),
            'transaction_id' => session('transaction_id'),
            'amount' => session('amount'),
            'payment_mode' => session('payment_mode'),
            'status' => session('status'),
            'message' => session('message')
        ]);
    }

















}
