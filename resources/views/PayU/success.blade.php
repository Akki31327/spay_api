<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4fdf4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }

        .success-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #4CAF50;
            position: relative;
            display: inline-block;
            animation: pop 0.3s ease-out;
        }

        .checkmark::after {
            content: '';
            position: absolute;
            left: 22px;
            top: 12px;
            width: 20px;
            height: 40px;
            border-right: 4px solid #4CAF50;
            border-bottom: 4px solid #4CAF50;
            transform: rotate(45deg);
            animation: check 0.5s ease-out 0.3s forwards;
        }

        h2 {
            color: #4CAF50;
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
            background-color: #f2f2f2;
            font-weight: 600;
        }

        @keyframes check {
            from { opacity: 0; transform: rotate(45deg) scale(0.5); }
            to { opacity: 1; transform: rotate(45deg) scale(1); }
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

<div class="success-container">
    <div class="checkmark"></div>
    <h2>Payment Successful!</h2>
    <p>Thank you for your payment. Below are the transaction details.</p>

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
            <td><strong style="color: green;">{{ ucfirst($data['status'] ?? 'success') }}</strong></td>
        </tr>
    </table>

    <p id="redirectMsg" style="margin-top: 30px; color: #555;">
        Redirecting back to payment page in <span id="countdown">10</span> seconds...
    </p>

</div>


<script>
    let seconds = 10;
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
