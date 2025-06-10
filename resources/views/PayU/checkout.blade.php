<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Responsive Checkout with Accordion | Razorpay Style</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f0f4f8;
        font-family: 'Segoe UI', sans-serif;
        color: #2b2e4a;
    }

    .checkout-container {
        max-width: 1200px;
        margin: 40px auto;
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: row;
    }

    .summary-panel {
        background: linear-gradient(to bottom, #ffffff, #2252ffbd);
        color: #1f1f1f;
        padding: 40px;
        width: 35%;
    }

    .form-panel {
        padding: 40px;
        width: 65%;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 12px;
    }

    .pay-btn {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: 500;
    }

    .qr-img {
        width: 100%;
        max-width: 200px;
        height: 200px;
        background-color: #e5e5e5;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-top: 10px;
    }

    .hidden {
        display: none;
    }

    .brand {
        font-weight: bold;
        font-size: 22px;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .checkout-container {
            flex-direction: column;
        }

        .summary-panel,
        .form-panel {
            width: 100%;
            padding: 30px;
        }

        .section-title {
            font-size: 20px;
        }
    }

    footer {
        background-color: #f8f9fa;
        padding: 20px;
        text-align: center;
        font-size: 14px;
        color: #6c757d;
        position: relative;
        bottom: 0;
        width: 100%;
    }

    footer a {
        text-decoration: none;
        color: #0d6efd;
        font-weight: bold;
    }
    </style>
</head>

<body>

    <div class="checkout-container">
        <!-- Summary Panel -->
        <div class="summary-panel">
            <img src="logo3.png" alt="SPAY FINTECH PVT LTD" width="160"><br><br>
            <h4>Payment Summary</h4>
            <p><strong>Merchant ID:</strong> <span id="displayMerchant">---</span></p>
            <p><strong>Amount:</strong> ₹<span id="displayAmount">---</span></p>
        </div>

        <!-- Form & Payment Panel -->
        <div class="form-panel">
            <!-- Step 1: Form -->
            <div id="formSection">
                <!-- <div class="section-title">Enter Customer & Transaction Details</div> -->
                <form id="checkoutForm">
                    <!-- <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="merchantId" placeholder="Merchant ID" required>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control" id="amount" placeholder="Amount (₹)" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="customerName" placeholder="Customer Name"
                                required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="contactNo" placeholder="Contact No" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control" id="email" placeholder="Email" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="token" placeholder="Token" required>
                        </div>
                        <div class="col-md-12">
                            <textarea class="form-control" id="address" rows="2" placeholder="Address"
                                required></textarea>
                        </div>
                    </div> -->
                    <button type="submit" class="btn btn-primary pay-btn">Proceed to Payment</button>
                </form>
            </div>
        </div>

    </div>

    <!-- Footer Section -->
    <footer>
        <p>Powered by <a href="https://spay.live" target="_blank">SPAY FINTECH PVT LTD</a> </p><img src="certified.png"
            alt="SPAY FINTECH PVT LTD" width="160">
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1-crypto-js.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"
        integrity="sha512-a+SUDuwNzXDvz4XrIcXHuCf089/iJAoN4lmrXJg18XnduKK6YlDHNRalv4yd1N40OKI80tFidF+rqTFKGPoWFQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- subimt page  -->

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.querySelector('.pay-btn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Processing...';

            const amount = @json($data['amount'] ?? '');
            const firstname = @json($data['name'] ?? '');
            const email = @json($data['email'] ?? '');
            const phone = @json($data['mobile'] ?? '');

            const formData = $(this).serializeArray(); // existing form data
            formData.push({ name: 'amount', value: amount });
            formData.push({ name: 'name', value: firstname });
            formData.push({ name: 'email', value: email });
            formData.push({ name: 'mobile', value: phone });

            // STEP 1: Create WooCommerce Order

            $.ajax({
                url: '{{ route("create.woo.order") }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.order_id) {
                        const data = {
                            amount: amount,
                            productinfo: 'Product Name or Info',
                            firstname: firstname,
                            email: email,
                            phone: phone,
                            order_id: response.order_id
                        };

                        console.log("➡️ Sending to PayU:", data);

                        fetch('/payu/generate-hash', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(res => res.json())
                        .then(hashResponse => {
                            if (hashResponse && hashResponse.txnid) {
                                window.location.href = `/payu/redirect-page?txnid=${hashResponse.txnid}`;
                            } else {
                                alert('❌ Failed to generate payment hash.');
                            }
                        })
                        .catch(err => {
                            console.error('❌ Error in hash generation:', err);
                            alert('❌ Hash generation failed.');
                        });

                    } else {
                        alert('❌ WooCommerce order creation failed.');
                    }
                },
                error: function(xhr) {
                    alert('❌ Order failed: ' + (xhr.responseJSON?.error || 'Unknown error'));
                    console.error(xhr.responseJSON);
                }
            });
           
        });
    </script>











</body>

</html>