<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #fff4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }

        .failed-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .fail-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #f44336;
            position: relative;
            display: inline-block;
            animation: pop 0.3s ease-out;
        }

        .fail-icon::before,
        .fail-icon::after {
            content: '';
            position: absolute;
            top: 38px;
            left: 20px;
            width: 40px;
            height: 4px;
            background-color: #f44336;
        }

        .fail-icon::before {
            transform: rotate(45deg);
        }

        .fail-icon::after {
            transform: rotate(-45deg);
        }

        h2 {
            color: #f44336;
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
            background-color: #f9f9f9;
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

<div class="failed-container">
    <div class="fail-icon"></div>
    <h2>Payment Failed</h2>
    <p>Unfortunately, the transaction was not successful. Please review the details below or try again.</p>

    <table class="details-table">
        <tr>
            <th>Transaction ID</th>
            <td>{{ $data['txnid'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>₹{{ $data['amount'] ?? '0.00' }}</td>
        </tr>
        <tr>
            <th>Product</th>
            <td>{{ $data['productinfo'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Customer Name</th>
            <td>{{ $data['firstname'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $data['email'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #f44336;">{{ ucfirst($data['status'] ?? 'failed') }}</strong></td>
        </tr>
    </table>

    <p id="redirectMsg" style="margin-top: 30px; color: #555;">
        Redirecting back to payment page in <span id="countdown">5</span> seconds...
    </p>
</div>


<script>
    let seconds = 5;
    const countdownEl = document.getElementById('countdown');

    const interval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "http://127.0.0.1:8000/payu/checkout";
        }
    }, 1000);
</script>

</body>
</html>
