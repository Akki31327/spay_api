<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhonePeController extends Controller
{
    //

    public function showCheckout()
    {
        return view('Phonepe.checkout'); 
    }

    public function AuthToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token', // Replace with actual token endpoint
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'client_id=TSPSPAYFINTECH_250409171&client_version=1&client_secret=NWJkZWUxZDgtMDY0Zi00OWIwLWJmMzAtNmFmMTcxNTgyZDFk&grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            curl_close($curl);
            Log::error('Curl error: ' . curl_error($curl));
            return null;
        }

        curl_close($curl);

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }


    public function generateQr(Request $request)
    {
        try {
            // Call AuthToken method to get the access token
            $token = $this->AuthToken();

            if (!$token) {
                return response()->json(['error' => 'Failed to authenticate'], 401);
            }

            $merchantId = $request->input('merchantId');
            $amount = $request->input('amount');
            $customerName = $request->input('customerName');
            $contactNo = $request->input('contactNo');
            $email = $request->input('email');
            $address = $request->input('address');

            $apiUrl = 'https://api-preprod.phonepe.com/apis/pg-sandbox/payments/v2/pay'; // Replace with actual QR endpoint
            $postData = [
                "merchantOrderId" => "TSPSPAYFINTECH",
                "amount" => $amount,
                "expireAfter" => 1200,
                "metaInfo" => [
                    "udf1" => "additional-information-1",
                    "udf2" => "additional-information-2",
                    "udf3" => "additional-information-3",
                    "udf4" => "additional-information-4",
                    "udf5" => "additional-information-5"
                ],
                "paymentFlow" => [
                    "type" => "PG",
                    "paymentMode" => [
                        "type" => "UPI_QR"
                    ]
                ]
            ];

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'O-Bearer ' . $token,
                'X-SOURCE' => 'API',
                'X-SOURCE-PLATFORM' => 'PARTNERNAME',
                'X-MERCHANT-ID' => 'ENDMERCHANTCUAT',
                'X-BROWSER-FINGERPRINT' => '8357426ac73fcd60b17355ab7de60421',
                'USER-AGENT' => 'Chrome/119.0.61.150 Mobile',
                'X-MERCHANT-DOMAIN' => 'https://www.google.com',
                'X-MERCHANT-IP' => '11.123.123.212',
                'X-SOURCE-CHANNEL' => 'web'
            ];

            $response = Http::withHeaders($headers)->post($apiUrl, $postData);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Response JSON: ' . json_encode($responseData));

                if (isset($responseData['qrData'])) {
                    $qrData = $responseData['qrData'];
                    return response()->json(['qr_code' => 'data:image/png;base64,' . $qrData]);
                } else {
                    Log::error('QR data not found in API response.');
                    return response()->json(['error' => 'Failed to generate QR code.'], 500);
                }
            } else {
                Log::error('API request failed: ' . $response->body());
                return response()->json(['error' => 'Failed to generate QR code.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in generateQr: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    
    public function UpiVerification()
    {
        return view('Phonepe.checkout'); 
    }



    public function UpiPayment(Request $request)
    {
        $payload = [
            "merchantOrderId" => "MO-" . uniqid("Order_"),
            "amount" => (int) $request->input('amount'),  // Convert to int as required by API
            "expireAfter" => 1200,
            "metaInfo" => [
                "udf1" => $request->input('customerName'),
                "udf2" => $request->input('contactNo'),
                "udf3" => $request->input('email'),
                "udf4" => $request->input('address'),
                "udf5" => $request->input('merchantId'),
            ],
            "paymentFlow" => [
                "type" => "PG",
                "paymentMode" => [
                    "type" => "UPI_COLLECT",
                    "details" => [
                        "type" => "VPA",
                        "vpa" => "abc@ybl"  // Ideally should also come from form
                    ],
                    "message" => "Payment for UPI Collect - Test"
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api-preprod.phonepe.com/apis/pg-sandbox/payments/v2/pay', // Replace with real URL
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
                'Authorization: O-Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHBpcmVzT24iOjE3NDUyNDUxNjk0NDUsIm1lcmNoYW50SWQiOiJUU1BTUEFZRklOVEVDSCJ9.qd_qDrZAUwM26FbkZQXEYAfIBHBiogp9iQcnrOn_VxQ',
                'X-SOURCE: API',
                'X-SOURCE-PLATFORM: PARTNERNAME',
                'X-MERCHANT-ID: ENANTCDMERCHUAT',
                'X-BROWSER-FINGERPRINT: 57426ac73fcd8360b17355ab7de60421',
                'USER-AGENT: Chrome/119.0.61.150 Mobile',
                'X-MERCHANT-DOMAIN: https://www.google.com',
                'X-SOURCE-CHANNEL: web'
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)], 500);
        }

        curl_close($curl);

        return response($response);
    }




}
