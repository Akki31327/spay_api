<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;


class UnlimitController extends Controller
{
    //

    public function UnlimitCheckout(Request $request)
    {
        $json = base64_decode($request['enc']);
        $data = json_decode($json, true);

        // return view('unlimiy.checkout', compact('data'));

        // $data = [
        //     'full_name' => 'test',
        //     'email' => 'test@test.com',
        //     'amount' => '100'
        // ];
        return view('unlimit.checkout', compact('data'));
        // return view('unlimit.checkout');
    }

    public function access_token()
    {
        $curl = curl_init();

       curl_setopt_array($curl, array(
            CURLOPT_URL => env('UNLIMIT_UAT_URL'), // <-- from .env
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type'     => 'password',
                'terminal_code'  => env('UNLIMIT_UAT_TERMINAL_CODE'),
                'password'       => env('UNLIMIT_UAT_PASSWORD'),
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
          
          $response = curl_exec($curl);
        // dd($response);
        $data =json_decode($response, true);
        curl_close($curl);
        return $data['access_token'] ?? null;
        // echo $response;
    }

    public function Generate_QR(Request $request)
    {
        // Ensure the request is JSON
        if (!$request->isJson()) {
            return response()->json(['error' => 'Invalid content type. Expected application/json.'], 400);
        }

        // Get access token (replace with your method)
        $access_token = $this->access_token();

        if (!$access_token) {
            return response()->json(['error' => 'Failed to authenticate'], 401);
        }

        // Validate incoming JSON data
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email',
                'name' => 'nullable|string',
                'mobile' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        // Prepare data
        $request_id = (string) \Str::uuid();
        $merchant_order_id = uniqid('order_');
        $request_time = gmdate("Y-m-d\TH:i:s\Z");

        $payload = [
            "request" => [
                "id" => $request_id,
                "time" => $request_time,
            ],
            "merchant_order" => [
                "id" => $merchant_order_id,
                "description" => "UPI Payment - " . ($validated['name'] ?? 'Customer'),
            ],
            "payment_method" => "upi",
            "payment_data" => [
                "amount" => (string) $validated['amount'],
                "currency" => "INR",
            ],
            "customer" => [
                "email" => $validated['email'],
                "name" => $validated['name'] ?? '',
                "phone" => $validated['mobile'] ?? '',
            ],
        ];

        // Send request to external API
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://sandbox.in.unlimit.com/api/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ],
        ]);

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $get_QR = json_decode($response, true);

        // Log full response for debugging
        \Log::info('Unlimit API Response', [
            'status' => $http_status,
            'response' => $get_QR
        ]);

        // Return QR data or an error message
        return response()->json([
            'redirect_url' => $get_QR['payment_data']['extended_data']['app_redirect_url'] ?? null,
            'qr_code_content' => $get_QR['payment_data']['extended_data']['qr_code_content'] ?? null,
            'payment_data' => $get_QR['payment_data'] ?? null,
            'http_status' => $http_status
        ]);
    }

    public function UPI_Payment(Request $request)
    {
        // Access token for authorization
        $access_token = $this->access_token();

        // Check for access token authentication
        if (!$access_token) {
            return response()->json(['error' => 'Failed to authenticate'], 401);
        }

        // Validate incoming data, including amount, upi_id, etc.
        $validated = $request->validate([
            'upi_id' => 'required|string',
            'amount' => 'required|numeric|min:1',   // Validate amount to be numeric and >= 1
            'currency' => 'nullable|string',        // Optionally validate currency
            'email' => 'nullable|email',            // Validate email if provided
            'name' => 'nullable|string',            // Validate name if provided
            'mobile' => 'nullable|string',          // Validate mobile number if provided
        ]);

        // Default currency if not provided
        $currency = $validated['currency'] ?? 'INR';  // Default currency is INR

        // Prepare the payload for the Unlimit API
        $payload = [
            "request" => [
                "id" => (string) \Str::uuid(),
                "time" => gmdate("Y-m-d\TH:i:s\Z"),
            ],
            "merchant_order" => [
                "id" => uniqid('order_'),
                "description" => "Payment for " . ($validated['name'] ?? 'Customer'),
            ],
            "payment_method" => "vpaupi",
            "payment_data" => [
                "amount" => (string) $validated['amount'], // Send the dynamic amount
                "currency" => $currency,                   // Send the dynamic currency
            ],
            "customer" => [
                "email" => $validated['email'] ?? 'unknown@email.com',   // Optional email
                "name" => $validated['name'] ?? 'Anonymous',             // Optional name
                "phone" => $validated['mobile'] ?? '0000000000',         // Optional mobile
            ],
            "ewallet_account" => [
                "id" => $validated['upi_id']  // Use the validated UPI ID
            ]
        ];

        // cURL request to Unlimit API
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://sandbox.in.unlimit.com/api/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // If there's an error in the cURL request, return an error response
        if ($response === false) {
            return response()->json(['error' => 'cURL request failed'], 500);
        }

        // Decode the JSON response
        $decoded = json_decode($response, true);
        
        // Log the response for debugging
        Log::info('Unlimit UPI Response:', ['response' => $decoded]);

        // Check if the response has the necessary fields
        if (isset($decoded['payment_data'])) {
            return response()->json([
                'http_code' => $httpCode,
                'payment_id' => $decoded['payment_data']['id'] ?? null,
                'status' => $decoded['payment_data']['status'] ?? 'UNKNOWN'
            ]);
        }

        // Return an error response if the payment data is not in the response
        return response()->json([
            'error' => 'Failed to process payment.',
            'status' => 'ERROR'
        ], 500);
    }

    public function netbankingPayment(Request $request)
    {
        //  dd($request);
        $access_token = $this->access_token();

        $requestId = (string) Str::uuid();
        $requestTime = now()->toIso8601String(); // or Carbon::now()->toIso8601String()
        $full_name = $request['name'];
        $email = $request['email'];
        $amount = $request['amount'];
        $bank_code = $request['bank_code'];
        
        $payload = [
            "request" => [
                "id" => $requestId,
                "time" => $requestTime
            ],
            "merchant_order" => [
                "id" => (string) Str::uuid(),
                "description" => "Netbanking Payment"
            ],
            "payment_method" => "netbankinginr",
            "payment_data" => [
                "amount" => $amount,
                "currency" => "INR"
            ],
            "customer" => [
                "full_name" => $full_name,
                "email" => $email
            ],
            "ewallet_account" => [
                "bank_code" => $bank_code
            ]
        ];

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://sandbox.in.unlimit.com/api/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $access_token
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            $responseData = json_decode($response, true);

            if (isset($responseData['payment_data']['extended_data']['redirect_url'])) {
                return $responseData['payment_data']['extended_data']['redirect_url'];
            } else {
                // Handle error or unexpected response
                return response()->json(['error' => 'Redirect URL not found'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Unlimit Netbanking Error: ' . $e->getMessage());
            return null;
        }
    }


    public function cardPayment(Request $request)
    {
        try {
            $access_token = $this->access_token();

            $requestId = (string) Str::uuid();
            $requestTime = now()->toIso8601String();

            $full_name = $request['name'];
            $email = $request['email'];
            $amount = $request['amount'];
            $card_no = $request['card_no'];
            $card_holder = $request['card_holder'];
            $card_month = $request['card_month'];
            $card_year = $request['card_year'];
            $card_cvv = $request['card_cvv'];

            $payload = [
                "request" => [
                    "id" => $requestId,
                    "time" => $requestTime
                ],
                "merchant_order" => [
                    "id" => uniqid(),
                    "description" => "Card Payment"
                ],
                "payment_method" => "BANKCARD",
                "payment_data" => [
                    "amount" => $amount,
                    "currency" => env('DEFAULT_CURRENCY', 'INR')
                ],
                "card_account" => [
                    "card" => [
                        "pan" => $card_no,
                        "holder" => $card_holder,
                        "expiration" => $card_month . '/' . $card_year,
                        "security_code" => $card_cvv
                    ]
                ],
                "customer" => [
                    "email" => $email
                ]
            ];

            $ch = curl_init('https://sandbox.in.unlimit.com/api/payments');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $response = curl_exec($ch);

            if ($response === false) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);

            $responseData = json_decode($response, true);

            if (isset($responseData['redirect_url'])) {
                return response()->json(['redirect_url' => $responseData['redirect_url']]);
            } else {
                return response()->json(['error' => 'Redirect URL not found', 'response' => $responseData], 400);
            }

        } catch (\Exception $e) {
            \Log::error('Card payment failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Something went wrong while processing the card payment.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
