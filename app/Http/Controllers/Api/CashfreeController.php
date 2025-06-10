<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Model\Mahaagent;
use App\Model\Company;
use App\Model\Mahastate;
use App\Model\Report;
use App\Model\Commission;
use App\Model\Aepsreport;
use App\Model\Provider;
use App\Model\Api;
use App\Model\Cosmosmerchant;
use App\Model\Apitoken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Model\Microatmreport;
use App\Model\UserPermission;
use App\Model\Apilog;
use App\Model\Scheme;
use App\Model\Utiid;
use App\Model\Packagecommission;
use App\Model\Package;

class CashfreeController extends Controller
{
    public function cfcreate(Request $request)
    {
        $rules = [
            'token'       => 'required',
            'apitxnid'    => 'required|alpha_num|min:8|max:20|unique:reports,mytxnid',
            'name'        => 'required|string',
            'email'       => 'required|email',
            'mobile'      => 'required|digits_between:10,15',
            'amount'      => 'required|numeric',
            'return_url'  => 'required|url',
        ];
    
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'statuscode' => 'ERR',
                'message'    => $validator->errors()->first(),
                'extmsg'     => 'fwd',
            ]);
        }
    
        $apiToken = Apitoken::where('ip', $request->ip())
                    ->where('token', $request->token)
                    ->first(['user_id']);
    
        if (!$apiToken) {
            return response()->json([
                'statuscode' => 'ERR',
                'message'    => "IP or Token mismatch. Your IP is " . $request->ip(),
            ]);
        }
        
        $amt = $request->amount;
        $request->amount = $request->amount / 100;
    
        $user = User::find($apiToken->user_id);
        $provider = Provider::where('recharge1', 'upi1')->first();
        $charge = $this->getCommission($request->amount, $user->scheme_id, $provider->id, $user->role->slug);
        $gst = $this->getGst($charge, $user->gstrate);
        Log::info("charge create: $charge");
        Log::info("gst create: $gst");
        $orderId = 'SPAY' . now()->format('YmdHis') . rand(11111111, 99999999);
    
        $queryData = [
            'token'      => $request->token,
            'apitxnid'   => $request->apitxnid,
            'name'       => $request->name,
            'email'      => $request->email,
            'mobile'     => $request->mobile,
            'type'       => 'dynamic',
            'return_url' => $request->return_url,
            'amount'     => $amt,
        ];
    
        $encryptedQuery = base64_encode(http_build_query($queryData));
        $paymentLink = 'https://deepak.spay.live/cashfree/checkout?' . $encryptedQuery;
        
        $data = [
            "gst"         => $gst,
            "charge"      => $charge,
            "mobile"      => $request->mobile,
            "payeeVPA"    => '',
            'txnid'       => $orderId,
            "payid"       => $orderId,
            'mytxnid'     => $request->apitxnid,
            "amount"      => $request->amount,
            "api_id"      => $provider->api->id,
            "user_id"     => $apiToken->user_id,
            "balance"     => $user->mainwallet,
            'aepstype'    => "UPI",
            "trans_type"  => "credit",
            "option1"     => $paymentLink,
            'status'      => 'initiated',
            'description' => $request->return_url,
            'credited_by' => $apiToken->user_id,
            'balance'     => $user->mainwallet,
            'provider_id' => $provider->id,
            'product'     => "upi"
        ];
        
        Log::info('Creating report with data:', $data);
        
        $report = \App\Model\Report::create($data);
        Log::info('Report created:', $report->toArray());

    
        return response()->json([
            'statuscode'   => 'TXNS',
            'message'      => 'Payment Initiated successfully',
            'payment_link' => $paymentLink,
        ]);
    }

    public static function getCommission($amount, $scheme, $slab, $role)
    {
        $commission = 0;
    
        try {
            $schememanager = \DB::table('portal_settings')->where('code', 'schememanager')->first(['value']);
            if ($schememanager->value != "all") {
                $myscheme = \App\Model\Scheme::find($scheme);
                if ($myscheme && $myscheme->status == "1") {
                    $comdata = \App\Model\Commission::where('scheme_id', $scheme)->where('slab', $slab)->first();
                    if ($comdata) {
                        $commission = $comdata->type == "percent"
                            ? ($amount * ($comdata[$role] ?? 0) / 100)
                            : ($comdata[$role] ?? 0);
                    }
                }
            } else {
                $myscheme = \App\Model\Package::find($scheme);
                if ($myscheme && $myscheme->status == "1") {
                    $comdata = \App\Model\Packagecommission::where('scheme_id', $scheme)->where('slab', $slab)->first();
                    if ($comdata) {
                        $commission = $comdata->type == "percent"
                            ? ($amount * ($comdata->value ?? 0) / 100)
                            : ($comdata->value ?? 0);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Commission Calculation Error", ['message' => $e->getMessage()]);
        }
    
        \Log::debug("Final Commission Returned", ['commission' => $commission]);
    
        return $commission;
    }

    
    public function getGst($amount,$gstrate){
        return ($gstrate/100)*$amount;
    }
}
