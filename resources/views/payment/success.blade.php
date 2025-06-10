<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container text-center mt-5">
    <h2 class="text-success">✅ Payment Successful</h2>
    <p>Your payment was processed successfully.</p>
    <h1>Payment Successful</h1>
    <p>Order ID: {{ $orderId }}</p>
    <p>Payment ID: {{ $cfPaymentId }}</p>
    <p>Payment Mode: {{ $paymentMode }}</p>
    <p>Message: {{ $paymentMessage }}</p>
    <a href="http://127.0.0.1:8000/cashfree/checkout" class="btn btn-primary mt-3">Return to Home</a>
</div>
</body>
</html>
