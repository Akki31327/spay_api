<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Pending</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #fffdf4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }

        .pending-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .pending-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #FFA500;
            position: relative;
            display: inline-block;
            animation: pop 0.3s ease-out;
        }

        .pending-icon::before {
            content: '';
            position: absolute;
            width: 4px;
            height: 24px;
            background-color: #FFA500;
            top: 12px;
            left: 38px;
            transform: rotate(0deg);
        }

        .pending-icon::after {
            content: '';
            position: absolute;
            width: 4px;
            height: 16px;
            background-color: #FFA500;
            top: 28px;
            left: 38px;
            transform: rotate(90deg);
        }

        h2 {
            color: #FFA500;
            margin-top: 20px;
            font-weight: 600;
        }

        .details-table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th, .details-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .details-table th {
            background-color: #fff7e6;
            font-weight: 600;
        }

        @keyframes pop {
            0% { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 600px) {
            .details-table th, .details-table td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="pending-container">
    <div class="pending-icon"></div>
    <h2>Payment Pending</h2>
    <p>Your payment is being processed. Please wait or check back later.</p>

    <table class="details-table">
        <tr>
            <th>Order ID</th>
            <td>{{ $order_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Transaction ID</th>
            <td>{{ $transaction_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>₹{{ $amount ?? '0.00' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #ff9800;">{{ ucfirst($status ?? 'pending') }}</strong></td>
        </tr>
        <tr>
            <th>Message</th>
            <td>{{ $message ?? 'Awaiting confirmation from payment gateway.' }}</td>
        </tr>
    </table>

    <p id="redirectMsg" style="margin-top: 30px; color: #555;">
        Redirecting back to payment page in <span id="countdown">15</span> seconds...
    </p>
</div>

<script>
    let seconds = 15;
    const countdownEl = document.getElementById('countdown');

    const interval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "http://127.0.0.1:8000/cashfree/checkout"; // Update to your route
        }
    }, 1000);
</script>

</body>
</html>
