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
      box-shadow: 0 0 30px rgba(0,0,0,0.08);
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

    .form-control, .form-select {
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

      .summary-panel, .form-panel {
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
      <div class="section-title">Enter Customer & Transaction Details</div>
      <form id="checkoutForm">
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" id="merchantId" placeholder="Merchant ID" required>
          </div>
          <div class="col-md-6">
            <input type="number" class="form-control" id="amount" placeholder="Amount (₹)" required>
          </div>
          <div class="col-md-6">
            <input type="text" class="form-control" id="customerName" placeholder="Customer Name" required>
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
            <textarea class="form-control" id="address" rows="2" placeholder="Address" required></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-primary pay-btn">Proceed to Payment</button>
      </form>
    </div>

    <!-- Step 2: Accordion Payment Section -->
    <div id="paymentSection" class="hidden">
      <div class="section-title">Choose Payment Method</div>

      <div class="accordion" id="paymentAccordion">
        <!-- UPI QR -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingQR">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQR">
              UPI QR
            </button>
          </h2>
          <div id="collapseQR" class="accordion-collapse collapse show" data-bs-parent="#paymentAccordion">
            <div class="accordion-body">
              <button id="generateQR" class="btn btn-warning mb-3">Generate QR</button>
              <div id="qrContainer" class="hidden">
                <div class="qr-img" id="qrCode">QR CODE</div>
                <p id="timerDisplay">Time Remaining: <span id="timer">60</span> seconds</p>
              </div>
            </div>
          </div>
        </div>

        <!-- UPI ID -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingUPI">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUPI">
              UPI ID
            </button>
          </h2>
          <div id="collapseUPI" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
            <div class="accordion-body">
              <form id="upiForm">
                <input type="text" id="upiId" class="form-control mb-2" placeholder="Enter your UPI ID" required>
                <button type="button" id="verifyUpiBtn" class="btn btn-outline-primary mb-2">Verify UPI ID</button>
                <div id="upiStatus" class="mb-3 text-success fw-semibold"></div>
                <button type="submit" class="btn btn-success pay-btn" disabled id="upiPayBtn">Pay via UPI ID</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Card -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingCard">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCard">
              Credit / Debit Card
            </button>
          </h2>
          <div id="collapseCard" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
            <div class="accordion-body">
              <form>
                <input type="text" class="form-control mb-3" placeholder="Cardholder Name" required>
                <input type="text" class="form-control mb-3" placeholder="Card Number" maxlength="19" required>
                <div class="row">
                  <div class="col-md-4">
                    <select class="form-select mb-3" required>
                      <option value="">Month</option>
                      <option value="01">01 - Jan</option>
                      <option value="02">02 - Feb</option>
                      <option value="03">03 - Mar</option>
                      <option value="04">04 - Apr</option>
                      <option value="05">05 - May</option>
                      <option value="06">06 - Jun</option>
                      <option value="07">07 - Jul</option>
                      <option value="08">08 - Aug</option>
                      <option value="09">09 - Sep</option>
                      <option value="10">10 - Oct</option>
                      <option value="11">11 - Nov</option>
                      <option value="12">12 - Dec</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <select class="form-select mb-3" required>
                      <option value="">Year</option>
                      <script>
                        const yearSelects = document.querySelectorAll("select");
                        const currentYear = new Date().getFullYear();
                        yearSelects.forEach(select => {
                          if (select.options.length === 1 && select.innerHTML.includes("Year")) {
                            for (let i = 0; i < 15; i++) {
                              const year = currentYear + i;
                              const option = document.createElement("option");
                              option.value = year;
                              option.textContent = year;
                              select.appendChild(option);
                            }
                          }
                        });
                      </script>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <input type="password" class="form-control mb-3" placeholder="CVV" maxlength="4" required>
                  </div>
                </div>
                <button class="btn btn-primary pay-btn">Pay with Card</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Netbanking -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingBank">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBank">
              Netbanking
            </button>
          </h2>
          <div id="collapseBank" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
            <div class="accordion-body">
              <form>
                <select class="form-select mb-3" id="netBankingSelect" required>
                  <option value="" disabled selected>Select Your Bank</option>
                  <option value="SBI">State Bank of India</option>
                  <option value="PNB">Punjab National Bank</option>
                  <option value="BoB">Bank of Baroda</option>
                  <option value="BoI">Bank of India</option>
                  <option value="UBI">Union Bank of India</option>
                  <option value="Canara">Canara Bank</option>
                  <option value="IndianBank">Indian Bank</option>
                  <option value="IOB">Indian Overseas Bank</option>
                  <option value="BoM">Bank of Maharashtra</option>
                  <option value="UCO">UCO Bank</option>
                  <option value="CBI">Central Bank of India</option>
                  <option value="PSB">Punjab & Sind Bank</option>
                </select>
                <button class="btn btn-primary pay-btn" id="netBankingPayBtn" disabled>Pay via NetBanking</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer Section -->
<footer>
  <p>Powered by <a href="https://spay.live" target="_blank">SPAY FINTECH PVT LTD</a> </p><img src="certified.png" alt="SPAY FINTECH PVT LTD" width="160">
</footer>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  document.getElementById("checkoutForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const merchantId = document.getElementById("merchantId").value;
    const amount = document.getElementById("amount").value;

    document.getElementById("displayMerchant").textContent = merchantId;
    document.getElementById("displayAmount").textContent = amount;

    document.getElementById("formSection").classList.add("hidden");
    document.getElementById("paymentSection").classList.remove("hidden");
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Generate QR code and start timer
  document.getElementById("generateQR").addEventListener("click", function() {
    document.getElementById("generateQR").classList.add("hidden");
    document.getElementById("qrContainer").classList.remove("hidden");

    // Start 60-second timer
    let timer = 60;
    const timerDisplay = document.getElementById("timer");
    const countdown = setInterval(function() {
      timer--;
      timerDisplay.textContent = timer;
      if (timer === 0) {
        clearInterval(countdown);
        document.getElementById("qrContainer").classList.add("hidden");
        document.getElementById("generateQR").classList.remove("hidden");
      }
    }, 1000);
  });
</script>
<script>
  // Mock UPI ID verification
  document.getElementById("verifyUpiBtn").addEventListener("click", function () {
    const upiInput = document.getElementById("upiId");
    const upiStatus = document.getElementById("upiStatus");
    const payBtn = document.getElementById("upiPayBtn");

    const upiId = upiInput.value.trim();

    if (upiId === "") {
      upiStatus.textContent = "Please enter a UPI ID.";
      upiStatus.classList.remove("text-success");
      upiStatus.classList.add("text-danger");
      payBtn.disabled = true;
      return;
    }

    // Fake validation logic (you can plug in an API here)
    if (/^[\w.-]+@[\w]+$/.test(upiId)) {
      upiStatus.textContent = "UPI ID is valid ✅";
      upiStatus.classList.remove("text-danger");
      upiStatus.classList.add("text-success");
      payBtn.disabled = false;
    } else {
      upiStatus.textContent = "Invalid UPI ID ❌";
      upiStatus.classList.remove("text-success");
      upiStatus.classList.add("text-danger");
      payBtn.disabled = true;
    }
  });

  // Optional: Prevent form submit for demo
  document.getElementById("upiForm").addEventListener("submit", function(e) {
    e.preventDefault();
    alert("Proceeding to UPI payment...");
  });
</script>
<script>
  document.getElementById('netBankingSelect').addEventListener('change', function() {
    const payBtn = document.getElementById('netBankingPayBtn');
    payBtn.disabled = !this.value;
  });
</script>

<script>
    $(document).ready(function() {
    // Handle form submission and proceed to payment
    $('#checkoutForm').submit(function(event) {
        event.preventDefault();  // Prevent the default form submission

        // Collect form data
        var formData = {
            merchantId: $('#merchantId').val(),
            amount: $('#amount').val(),
            customerName: $('#customerName').val(),
            contactNo: $('#contactNo').val(),
            email: $('#email').val(),
            token: $('#token').val(),
            address: $('#address').val()
        };

        // Send AJAX request
        $.ajax({
            url: '/phonepe/checkout',  // Controller route
            method: 'GET',
            data: formData,  // Pass form data
            success: function(response) {
                // Process the response (if needed)
                console.log(response);
                // You can show or hide parts of the page based on the response
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
            }
        });
    });

    $('#generateQR').on('click', function () {
        // Send AJAX request to generate QR
        $.ajax({
            url: '/phonepe/generate-qr',
            method: 'POST',
            data: {
                merchantId: $('#merchantId').val(),
                amount: $('#amount').val(),
                customerName: $('#customerName').val(),
                contactNo: $('#contactNo').val(),
                email: $('#email').val(),
                token: $('#token').val(),
                address: $('#address').val(),
                _token: $('meta[name="csrf-token"]').attr('content')  // CSRF token
            },
            success: function (response) {
                if (response.qr_code) {
                    // Display QR code
                    $('#qrCode').html('<img src="' + response.qr_code + '" alt="QR Code" height="200" width="200" />');
                    $('#qrContainer').removeClass('hidden');  // Make QR container visible
                } else {
                    alert('Failed to generate QR code');
                }
            },
            error: function () {
                alert('An error occurred while generating the QR code.');
            }
        });
    });

    $('#verifyUpiBtn').submit(function(event) {
        event.preventDefault();  // Prevent the default form submission

        // Collect form data
        var formData = {
            merchantId: $('#merchantId').val(),
            amount: $('#amount').val(),
            customerName: $('#customerName').val(),
            contactNo: $('#contactNo').val(),
            email: $('#email').val(),
            token: $('#token').val(),
            address: $('#address').val()
        };

        // Send AJAX request
        $.ajax({
            url: '/phonepe/upi-id-verify',  // Controller route
            method: 'GET',
            data: formData,  // Pass form data
            success: function(response) {
                // Process the response (if needed)
                console.log(response);
                
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
            }
        });
    });

    $('#upiPayBtn').click(function(event) {
        event.preventDefault(); // Prevent default button behavior

        var formData = {
            merchantId: $('#merchantId').val(),
            amount: $('#amount').val(),
            customerName: $('#customerName').val(),
            contactNo: $('#contactNo').val(),
            email: $('#email').val(),
            token: $('#token').val(),
            address: $('#address').val()
        };

        $.ajax({
            url: '/phonepe/upi-payment',
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });

});

</script>
</body>
</html>
