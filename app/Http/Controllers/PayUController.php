<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PayUController extends Controller
{
    public function payushowCheckout(Request $request)
    {
        $json = base64_decode($request['enc']);
        $data = json_decode($json, true);
        // return view('payu.checkout');
        return view('payu.checkout', compact('data'));
    }

    public function createWooOrder(Request $request)
    {

        $amount = $request->input('amount');
        $name = $request->input('name');
        $email = $request->input('email');
        $mobile = $request->input('mobile');

        // Log to verify
        \Log::info('Received Woo Order Data:', $request->all());
        $targetAmount = 1179.0;
        $maxQtyPerProduct = 10;

        $products = [
            ['id' => 9854, 'sale_price' => 105],
            ['id' => 9848, 'sale_price' => 379],
            ['id' => 9847, 'sale_price' => 485],
            ['id' => 9843, 'sale_price' => 484],
            ['id' => 9840, 'sale_price' => 489],
            ['id' => 9834, 'sale_price' => 379],
            ['id' => 9831, 'sale_price' => 377],
            ['id' => 9830, 'sale_price' => 399],
            ['id' => 9822, 'sale_price' => 439],
            ['id' => 9817, 'sale_price' => 398],
            ['id' => 9816, 'sale_price' => 199],
            ['id' => 9810, 'sale_price' => 285],
            ['id' => 9802, 'sale_price' => 169],
            ['id' => 9803, 'sale_price' => 249],
            ['id' => 9795, 'sale_price' => 75],
            ['id' => 9786, 'sale_price' => 299],
            ['id' => 9774, 'sale_price' => 299],
            ['id' => 9757, 'sale_price' => 89],
            ['id' => 9781, 'sale_price' => 64],
            ['id' => 9774, 'sale_price' => 299],
            ['id' => 9773, 'sale_price' => 359],
            ['id' => 9767, 'sale_price' => 379],
            ['id' => 9768, 'sale_price' => 219],
            ['id' => 9762, 'sale_price' => 329],
            ['id' => 9759, 'sale_price' => 498],
            ['id' => 9757, 'sale_price' => 89],
            ['id' => 9752, 'sale_price' => 349],
            ['id' => 9745, 'sale_price' => 279],
            ['id' => 9743, 'sale_price' => 249],
            ['id' => 9739, 'sale_price' => 149],
            ['id' => 9693, 'sale_price' => 18989],
            ['id' => 9687, 'sale_price' => 37990],
            ['id' => 9575, 'sale_price' => 999],
            ['id' => 9576, 'sale_price' => 199],
            ['id' => 9577, 'sale_price' => 199],
            ['id' => 9578, 'sale_price' => 199],
            ['id' => 9579, 'sale_price' => 199],
            ['id' => 9580, 'sale_price' => 199],
            ['id' => 9568, 'sale_price' => 599],
            ['id' => 9563, 'sale_price' => 299],
            ['id' => 9571, 'sale_price' => 799],
            ['id' => 9564, 'sale_price' => 299],
            ['id' => 9570, 'sale_price' => 799],
            ['id' => 9565, 'sale_price' => 299],
            ['id' => 9572, 'sale_price' => 799],
            ['id' => 9573, 'sale_price' => 799],
            ['id' => 9574, 'sale_price' => 799],
            ['id' => 9480, 'sale_price' => 299],
            ['id' => 9481, 'sale_price' => 299],
            ['id' => 9482, 'sale_price' => 299],
            ['id' => 9465, 'sale_price' => 1899],
            ['id' => 9479, 'sale_price' => 299],
            ['id' => 9466, 'sale_price' => 2221],
            ['id' => 9467, 'sale_price' => 2932],
            ['id' => 9468, 'sale_price' => 2999],
            ['id' => 9469, 'sale_price' => 299],
            ['id' => 9470, 'sale_price' => 299],
            ['id' => 9471, 'sale_price' => 299],
            ['id' => 9472, 'sale_price' => 299],
            ['id' => 9473, 'sale_price' => 299],
            ['id' => 9474, 'sale_price' => 299],
            ['id' => 9475, 'sale_price' => 299],
            ['id' => 9476, 'sale_price' => 299],
            ['id' => 9477, 'sale_price' => 299],
            ['id' => 9478, 'sale_price' => 299],
            ['id' => 9393, 'sale_price' => 399],
            ['id' => 9394, 'sale_price' => 539],
            ['id' => 9395, 'sale_price' => 399],
            ['id' => 9379, 'sale_price' => 199],
            ['id' => 9380, 'sale_price' => 399],
            ['id' => 9381, 'sale_price' => 349],
            ['id' => 9382, 'sale_price' => 349],
            ['id' => 9383, 'sale_price' => 729],
            ['id' => 9384, 'sale_price' => 729],
            ['id' => 9385, 'sale_price' => 499],
            ['id' => 9386, 'sale_price' => 399],
            ['id' => 9387, 'sale_price' => 399],
            ['id' => 9388, 'sale_price' => 249],
            ['id' => 9389, 'sale_price' => 749],
            ['id' => 9378, 'sale_price' => 499],
            ['id' => 9377, 'sale_price' => 299],
            ['id' => 9367, 'sale_price' => 249],
            ['id' => 9368, 'sale_price' => 399],
            ['id' => 9369, 'sale_price' => 549],
            ['id' => 9370, 'sale_price' => 549],
            ['id' => 9371, 'sale_price' => 999],
            ['id' => 9372, 'sale_price' => 729],
            ['id' => 9373, 'sale_price' => 479],
            ['id' => 9374, 'sale_price' => 399],
            ['id' => 9375, 'sale_price' => 249],
            ['id' => 9376, 'sale_price' => 279]
        ];

        usort($products, fn($a, $b) => $b['sale_price'] <=> $a['sale_price']);

        function generateProductCombination($products, $targetAmount, $minShipping = 50, $maxQty = 20)
        {
            $startIndex = 0;
            while ($startIndex < count($products)) {
                $total = 0;
                $orderItems = [];
                for ($i = $startIndex; $i < count($products); $i++) {
                    $product = $products[$i];
                    $remaining = $targetAmount - $total;
                    if ($remaining <= 0) break;
                    $maxAffordableQty = floor($remaining / $product['sale_price']);
                    $qty = min($maxAffordableQty, $maxQty);
                    if ($qty > 0) {
                        $orderItems[] = ['product_id' => $product['id'], 'quantity' => $qty];
                        $total += $product['sale_price'] * $qty;
                    }
                }
                $shippingCharge = $targetAmount - $total;
                if ($shippingCharge >= $minShipping || abs($shippingCharge) < 1.0e-2) {
                    return [
                        'items' => $orderItems,
                        'shipping' => round($shippingCharge, 2),
                        'product_total' => round($total, 2)
                    ];
                }
                $startIndex++;
            }
            return null;
        }

        $combo = generateProductCombination($products, $targetAmount);

        if (!$combo) {
            return response()->json(['error' => 'No valid product combination found.'], 400);
        }

        Log::info("Generating variable for Order firstname: {$request->input('firstname')}, phone: {$request->input('email')}");

        $orderPayload = [
            'payment_method' => 'cod',
            'payment_method_title' => 'Cash on Delivery',
            'set_paid' => true,
            'billing' => [
                'first_name' => $name,
                'last_name' => 'Doe',
                'address_1' => '969 Market',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postcode' => '94103',
                'country' => 'US',
                'email' => $email,
                'phone' => $mobile
            ],
            'shipping' => [
                'first_name' => $name,
                'last_name' => 'Doe',
                'address_1' => '969 Market',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postcode' => '94103',
                'country' => 'US'
            ],
            'line_items' => $combo['items'],
            'shipping_lines' => [
                [
                    'method_id' => 'flat_rate',
                    'method_title' => 'Flat Rate',
                    'total' => number_format($combo['shipping'], 2)
                ]
            ]
        ];

        $consumerKey = 'ck_63f765aae559bc665d8ed664f67099e4415db62b';
        $consumerSecret = 'cs_b28d67e98e675513ac2994a0b2131099837a9c0f';
        $authHeader = 'Basic ' . base64_encode($consumerKey . ':' . $consumerSecret);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://soulfuloverseas.com/wp-json/wc/v3/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($orderPayload),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $authHeader,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($curl);
        Log::info("response create: $response");
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::error("cURL Error: $error");
            return response()->json(['error' => $error], 500);
        }

        $res = json_decode($response, true);

        if (isset($res['id'])) {
            Log::info("Order created with ID: {$res['id']}");
            return response()->json(['success' => true, 'order_id' => $res['id']]);
        } else {
            Log::error("WooCommerce Error", ['response' => $res ?? $response]);
            return response()->json(['error' => $res ?? 'Invalid JSON response'], 500);
        }

    }

    public function generateHash(Request $request)
    {

        // Log::info("Generating request: {$request}");
        $orderId = $request->input('order_id');

        $key = 'TnzDmn';
        $salt = 'zD5cQmAPWR102q2ZXRkoRc2WD2T9h55U';

        // $txnid = uniqid('txn_', true);
        // $txnid = uniqid("txn_{$orderId}_", true);
        $txnid = (string) $orderId;
        Log::info("Generating hash for Order ID: {$orderId}, Txn ID: {$txnid}");
        $amount = $request->input('amount');
        $productinfo = $request->input('productinfo');
        $firstname = $request->input('firstname');
        $email = $request->input('email');

        // Hash sequence: key|txnid|amount|productinfo|firstname|email|||||||||||salt
        $hashString = $key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|'
            . $firstname . '|' . $email . '|||||||||||' . $salt;

        $hash = strtolower(hash('sha512', $hashString));
        Session::put("payu_data_$txnid", [
            'hash' => $hash,
            'txnid' => $txnid,
            'key' => $key,
            'amount' => $amount,
            'productinfo' => $productinfo,
            'firstname' => $firstname,
            'email' => $email,
            'surl' => 'http://127.0.0.1:8000/payu/success',
            'furl' => 'http://127.0.0.1:8000/payu/failure',
            'service_provider' => 'payu_paisa'
        ]);

        return response()->json([
            'hash' => $hash,
            'txnid' => $txnid,
            'key' => $key,
            'amount' => $amount,
            'productinfo' => $productinfo,
            'firstname' => $firstname,
            'email' => $email
        ]);
    }

    private function verifyHash($data)
    {
        $key = 'TnzDmn';
        $salt = 'zD5cQmAPWR102q2ZXRkoRc2WD2T9h55U';

        $hashSequence = $salt . '|' . $data['status'] . '|||||||||||' . $data['email'] . '|' . $data['firstname'] . '|' . $data['productinfo'] . '|' . $data['amount'] . '|' . $data['txnid'] . '|' . $key;
        $generatedHash = strtolower(hash('sha512', $hashSequence));

        return $generatedHash === $data['hash'];
    }

    public function success(Request $request)
    {
        // return view('payu.success');
        $data = $request->all();  // Contains transaction details
        \Log::info('PayU Success', $data);

        // Optional: Verify the hash
        if ($this->verifyHash($data)) {
            // Save transaction to DB, mark as paid
            return view('payu.success', ['data' => $data]);
        } else {
            // Possible tampering
            return view('payu.failed', ['error' => 'Hash mismatch']);
        }
    }

    public function failure(Request $request)
    {
        // return view('payu.failed');
        $data = $request->all();
        \Log::info('PayU Failure', $data);

        return view('payu.failed', ['data' => $data]);
    }

    public function redirectPagePayU(Request $request)
    {
        $txnid = $request->query('txnid');
        $payuData = Session::get("payu_data_$txnid");  // or retrieve from DB
        // dd([$payuData,$txnid]);

        if (!$payuData) {
            abort(404, 'Transaction not found.');
        }

        return view('payu.mirror-screen', ['payuData' => $payuData]);
    }
}
