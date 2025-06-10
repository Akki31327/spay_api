<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhonePeController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\PayUController;
use App\Http\Controllers\UnlimitController;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Route::middleware(['auth'])->group(function () {

// phonepe gateway
Route::get('/phonepe/checkout', [PhonePeController::class, 'showCheckout']);
Route::post('/phonepe/generate-qr', [PhonePeController::class, 'generateQr']);
Route::post('/phonepe/upi-id-verify', [PhonePeController::class, 'UpiVerification']);
Route::post('/phonepe/upi-payment', [PhonePeController::class, 'UpiPayment']);

// cashfree gateway
Route::get('/cashfree/checkout', [CashfreeController::class, 'cashfreeshowCheckout']);
Route::post('/cashfree/payment-session', [CashfreeController::class, 'paymentSessionId']);
Route::get('/cashfree/redirect', [CashfreeController::class, 'handleRedirect']);
Route::get('/cashfree/success', [CashfreeController::class, 'success']);
Route::get('/cashfree/failed', [CashfreeController::class, 'failed']);
Route::get('/cashfree/pending', [CashfreeController::class, 'pending']);



Route::get('/cashfree/netbanking', [CashfreeController::class, 'cashfreenetbanking']);
Route::get('/cashfree/upi', [CashfreeController::class, 'cashfreeupi']);
Route::get('/cashfree/upimobile', [CashfreeController::class, 'cashfreeupimobile']);
Route::get('/cashfree/payment-status/{orderId}', [CashfreeController::class, 'checkStatus']);

// payu gateway 
Route::get('/payu/checkout', [PayUController::class, 'payushowCheckout']);
Route::post('/create-woo-order', [PayUController::class, 'createWooOrder'])->name('create.woo.order');
Route::post('/payu/generate-hash', [PayUController::class, 'generateHash']);
Route::get('/payu/redirect-page', [PayUController::class, 'redirectPagePayU']);
Route::match(['get', 'post'], '/payu/success', [PayUController::class, 'success']);
Route::match(['get', 'post'], '/payu/failure', [PayUController::class, 'failure']);


// Unlimit gateway
Route::get('/unlimit/checkout', [UnlimitController::class, 'UnlimitCheckout']);
Route::post('/unlimit/generate-QR', [UnlimitController::class, 'Generate_QR'])->name('create.QR');
Route::post('/unlimit/upi-payment', [UnlimitController::class, 'UPI_Payment'])->name('upi.payment');
Route::post('/unlimit/netbanking', [UnlimitController::class, 'netbankingPayment'])->name('unlimit.netbanking');
Route::post('/unlimit/cardpayment', [UnlimitController::class, 'cardPayment'])->name('unlimit.cardpayment');

// Route::get('/cashfree/redirect', [UnlimitController::class, 'handleRedirect']);
// Route::get('/cashfree/success', [UnlimitController::class, 'success']);
// Route::get('/cashfree/failed', [UnlimitController::class, 'failed']);
// Route::get('/cashfree/pending', [UnlimitController::class, 'pending']);



