
<!-- /resources/views/payu/redirect.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        #payuForm{
            width: 60vw;
            height: 500px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top:3rem;
            text-align: center;
            border: 1px solid white;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
        }
        #paySubmit{
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 300px;
            opacity: 0.6;
            transition: ease-in-out .3s;
        }
        #paySubmit:hover{
            opacity: 1;
        }
    </style>
</head>
<body>

    <form id="payuForm" action="https://secure.payu.in/_payment" method="POST" target="payuIframe">
        <img src="https://spay.live/public/images/Spay%20TM%20Logo%20(Black).webp" alt="SPAY FINTECH PVT LTD" width="160"><br><br>
        <h3>Click checkout to Proceed...</h3>
        <input type="hidden" name="key" value="{{ $payuData['key'] }}">
        <input type="hidden" name="txnid" value="{{ $payuData['txnid'] }}">
        <input type="hidden" name="amount" value="{{ $payuData['amount'] }}">
        <input type="hidden" name="productinfo" value="{{ $payuData['productinfo'] }}">
        <input type="hidden" name="firstname" value="{{ $payuData['firstname'] }}">
        <input type="hidden" name="email" value="{{ $payuData['email'] }}">
        <input type="hidden" name="surl" value="{{ $payuData['surl'] }}">
        <input type="hidden" name="furl" value="{{ $payuData['furl'] }}">
        <input type="hidden" name="hash" value="{{ $payuData['hash'] }}">
        <input type="hidden" name="service_provider" value="payu_paisa">

        <button id="paySubmit">Checkout</button>
    </form>

    <script>
        document.getElementById('payuForm').submit();
    </script>
</body>
</html>