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
    .bank {
			text-align: center;
			padding: 10px;
			border-top: 1px solid #efefef;

		}

		.bank:nth-child(3) {
			border-right: 1px solid #efefef;
		}

		.bank:nth-child(1) {
			border-right: 1px solid #efefef;
		}

		.icon {
			width: 34px;
			margin: 0 auto;
		}

		.btext {
			margin-top: 10px
		}
		.bank{
			cursor: pointer;
		}
  </style>

</head>

<body>

    <div class="checkout-container">
      <!-- Summary Panel -->
      <div class="summary-panel">
        <img src="logo3.png" alt="SPAY FINTECH PVT LTD" width="160"><br><br>
        <h4>Payment Summary </h4>
        <p><strong>Merchant ID:</strong> <span id="displayMerchant">---</span></p>
        <p><strong>Amount:</strong> ₹<span id="displayAmount">---</span></p>
      </div>

      <!-- Form & Payment Panel -->
      <div class="form-panel">
        <!-- Step 1: Form -->
         
        <div id="formSection">
          <!-- <div class="section-title">Enter Customer & Transaction Details</div> -->
          <!-- <form id="checkoutForm">
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
          </form> -->
        </div>

        <!-- Step 2: Accordion Payment Section -->
        <div id="paymentSection">
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
                        <p id="paymentMessage"></p>
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
                          <div id="vpainput" class="mb-3"></div> <!-- Cashfree UPI field mounts here -->
                          <div id="upiStatus" class="mb-2 fw-semibold text-danger"></div>
                          <button type="submit" class="btn btn-success" id="upiInitBtn">Start Payment</button>
                          <button type="button" class="btn btn-primary mt-2 d-none" id="upiPayBtn">Confirm & Pay</button>
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
                <form id="card-payment-form">
                  <div class="mb-3">
                    <div id="cardHolder" class="form-control" placeholder="Cardholder Name"></div>
                  </div>
                  <div class="mb-3">
                    <div id="card-number" class="form-control" placeholder="Card Number"></div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div id="cardExpiry" class="form-control mb-3" placeholder="MM/YY"></div>
                    </div>
                    <div class="col-md-4">
                      <div id="cardCvv" class="form-control mb-3" placeholder="CVV"></div>
                    </div>
                    <div class="col-md-4">
                      <div id="save" class="form-check mt-2"></div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-primary w-100" id="pay-card">Pay with Card</button>
                  <p id="paymentMessage" class="alert mt-3" style="display:none;"></p>
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
                    
                <div class="row">
                    <div class="col-6 bank col" bfor="hdfc">
                      <div id="hdfcr" class="icon"></div>
                      <div class="btext">HDFC Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="kotak">
                      <div id="kotak" class="icon"></div>
                      <div class="btext">Kotak Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="sbi">
                      <div id="sbi" class="icon"></div>
                      <div class="btext">SBI Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="icici">
                      <div id="icici" class="icon"></div>
                      <div class="btext">ICICI Bank</div>
                    </div>

                    <!-- TPV Supported Banks -->
                    <div class="col-6 bank col" bfor="au">
                      <div id="aublr" class="icon"></div>
                      <div class="btext">AU Small Finance Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="axis">
                      <div id="utibr" class="icon"></div>
                      <div class="btext">Axis Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="bandhan">
                      <div id="bdblr" class="icon"></div>
                      <div class="btext">Bandhan Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="barodacorp">
                      <div id="barbc" class="icon"></div>
                      <div class="btext">Bank of Baroda - Corporate</div>
                    </div>
                    <div class="col-6 bank col" bfor="baroda">
                      <div id="barbr" class="icon"></div>
                      <div class="btext">Bank of Baroda</div>
                    </div>
                    <div class="col-6 bank col" bfor="boi">
                      <div id="bkidr" class="icon"></div>
                      <div class="btext">Bank of India</div>
                    </div>
                    <div class="col-6 bank col" bfor="mahb">
                      <div id="mahbr" class="icon"></div>
                      <div class="btext">Bank of Maharashtra</div>
                    </div>
                    <div class="col-6 bank col" bfor="canara">
                      <div id="cnrbr" class="icon"></div>
                      <div class="btext">Canara Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="capital">
                      <div id="clblr" class="icon"></div>
                      <div class="btext">Capital Small Finance Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="central">
                      <div id="cbinr" class="icon"></div>
                      <div class="btext">Central Bank of India</div>
                    </div>
                    <div class="col-6 bank col" bfor="cityunion">
                      <div id="ciubr" class="icon"></div>
                      <div class="btext">City Union Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="cosmos">
                      <div id="cosbr" class="icon"></div>
                      <div class="btext">Cosmos Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="csb">
                      <div id="csbkr" class="icon"></div>
                      <div class="btext">CSB Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="deutsche">
                      <div id="deutr" class="icon"></div>
                      <div class="btext">Deutsche Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="dhanlaxmi">
                      <div id="dlxbr" class="icon"></div>
                      <div class="btext">Dhanlakshmi Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="federal">
                      <div id="fdrlr" class="icon"></div>
                      <div class="btext">Federal Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="gujaratcoop">
                      <div id="gscbr" class="icon"></div>
                      <div class="btext">Gujarat State Co-op Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="hsbc">
                      <div id="hsbcr" class="icon"></div>
                      <div class="btext">HSBC Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="idbi">
                      <div id="ibklr" class="icon"></div>
                      <div class="btext">IDBI Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="idfc">
                      <div id="idfbr" class="icon"></div>
                      <div class="btext">IDFC FIRST Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="indian">
                      <div id="idibr" class="icon"></div>
                      <div class="btext">Indian Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="iob">
                      <div id="iobar" class="icon"></div>
                      <div class="btext">Indian Overseas Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="indusind">
                      <div id="indbr" class="icon"></div>
                      <div class="btext">IndusInd Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="jandk">
                      <div id="jakar" class="icon"></div>
                      <div class="btext">Jammu and Kashmir Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="jana">
                      <div id="jsfbr" class="icon"></div>
                      <div class="btext">Jana Small Finance Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="karnataka">
                      <div id="karbr" class="icon"></div>
                      <div class="btext">Karnataka Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="karur">
                      <div id="kvblr" class="icon"></div>
                      <div class="btext">Karur Vysya Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="psb">
                      <div id="psibr" class="icon"></div>
                      <div class="btext">Punjab & Sind Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="pnb">
                      <div id="punbr" class="icon"></div>
                      <div class="btext">Punjab National Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="rbl">
                      <div id="ratnr" class="icon"></div>
                      <div class="btext">RBL Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="saraswat">
                      <div id="srcbr" class="icon"></div>
                      <div class="btext">Saraswat Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="sbm">
                      <div id="stcbr" class="icon"></div>
                      <div class="btext">SBM Bank India</div>
                    </div>
                    <div class="col-6 bank col" bfor="shivalik">
                      <div id="smcbr" class="icon"></div>
                      <div class="btext">Shivalik Small Finance Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="sib">
                      <div id="siblr" class="icon"></div>
                      <div class="btext">South Indian Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="scb">
                      <div id="scblr" class="icon"></div>
                      <div class="btext">Standard Chartered Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="tmb">
                      <div id="tmblr" class="icon"></div>
                      <div class="btext">Tamilnad Mercantile Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="surat">
                      <div id="spcbr" class="icon"></div>
                      <div class="btext">The Surat Peoples Co-op Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="sutex">
                      <div id="sutbr" class="icon"></div>
                      <div class="btext">The Sutex Co-op Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="uco">
                      <div id="ucbar" class="icon"></div>
                      <div class="btext">UCO Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="ujjivan">
                      <div id="ujjivan" class="icon"></div>
                      <div class="btext">Ujjivan Small Finance Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="union">
                      <div id="ubinr" class="icon"></div>
                      <div class="btext">Union Bank of India</div>
                    </div>
                    <div class="col-6 bank col" bfor="utkarsh">
                      <div id="utksr" class="icon"></div>
                      <div class="btext">Utkarsh Small Finance Bank</div>
                    </div>
                    <div class="col-6 bank col" bfor="yesbank">
                      <div id="yesbr" class="icon"></div>
                      <div class="btext">Yes Bank</div>
                    </div>
                  </div>
                </div>

                <div id="paymentMessage" class="alert" style="display:none;"></div>

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
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script src="https://sdk.cashfree.com/js/ui/2.0.0/cashfree.js"></script>
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

    <!-- QR code  -->

    <script>
      const generateQRBtn = document.getElementById("generateQR");
      const qrContainer = document.getElementById("qrContainer");
      const qrCodeDiv = document.getElementById("qrCode");
      const paymentMessage = document.getElementById("paymentMessage");

      // Prepare the dynamic data to send to the server
      
      const data = {
          token: @json($data['token'] ?? ''),
          apitxnid: @json($data['apitxnid'] ?? ''),
          name: @json($data['name'] ?? ''),
          email: @json($data['email'] ?? ''),
          mobile: @json($data['mobile'] ?? ''),
          type: @json($data['type'] ?? ''),
          return_url: @json($data['return_url'] ?? ''),
          amount: @json($data['amount'] ?? ''),
          callback: @json($data['callback'] ?? '')
      };

      // Hidden input for session ID
      let sessionInput = document.getElementById("paymentSessionId");
      if (!sessionInput) {
          sessionInput = document.createElement("input");
          sessionInput.type = "hidden";
          sessionInput.id = "paymentSessionId";
          document.body.appendChild(sessionInput);
      }

      const cashfree = Cashfree({ mode: "sandbox" }); // use "PROD" for production

      let qrReady = false;
      let currentOrderId = null;
      let qr = cashfree.create("upiQr", {
          values: { size: "200px" }
      });
      qr.mount("#qrCode");

      qr.on("ready", () => qrReady = true);
      qr.on("paymentrequested", () => generateQRBtn.disabled = true);

      generateQRBtn.addEventListener("click", function () {
          generateQRBtn.disabled = true;
          qrContainer.classList.remove("hidden");
          startTimer(60);

          // Send data to your Laravel backend
          fetch("/cashfree/payment-session", {
              method: "POST",
              headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content") // CSRF Token
              },
              body: JSON.stringify(data) // Send dynamic data as JSON
          })
          .then(res => res.json())
          .then(data => {
              if (data.payment_session_id && data.order_id) {
                  sessionInput.value = data.payment_session_id;
                  currentOrderId = data.order_id;

                  const returnUrl = `http://127.0.0.1:8000/cashfree/redirect?order_id=${data.order_id}`;

                  const waitForQr = setInterval(() => {
                      if (qrReady) {
                          clearInterval(waitForQr);

                          cashfree.pay({
                              paymentMethod: qr,
                              paymentSessionId: sessionInput.value,
                              returnUrl: returnUrl,
                              redirect: "always", // Always try to redirect
                              redirectTarget: "_self"
                          }).then(function (res) {
                              generateQRBtn.disabled = false;

                              if (res.error) {
                                  console.log("Error:", res.error.message);
                              }

                              if (res.paymentDetails) {
                                  console.log("Payment Message:", res.paymentDetails.paymentMessage);
                              }

                              if (res.redirect) {
                                  console.log("Redirecting...");
                              }
                          });
                      }
                  }, 100);
              } else {
                  alert("Failed to get payment session ID or order ID.");
                  generateQRBtn.disabled = false;
              }
          })
          .catch(err => {
              console.error("Error:", err);
              alert("Something went wrong.");
              generateQRBtn.disabled = false;
          });
      });

      function startTimer(duration) {
          let timer = duration;
          const display = document.getElementById("timer");
          const interval = setInterval(() => {
              display.textContent = timer;
              timer--;
              if (timer < 0) {
                  clearInterval(interval);
                  display.textContent = "Expired";
                  qrContainer.classList.add("hidden");
                  generateQRBtn.disabled = false;

                  // Manually check payment status if no redirect happened
                  const fallbackOrderId = currentOrderId || new URLSearchParams(window.location.search).get("order_id");
                  if (fallbackOrderId) {
                      console.log("Checking payment status manually...");
                      fetch(`/cashfree/redirect?order_id=${fallbackOrderId}`)
                          .then(response => {
                              if (response.redirected) {
                                  window.location.href = response.url;
                              } else {
                                  paymentMessage.innerText = "Payment status could not be determined.";
                              }
                          })
                          .catch(err => {
                              console.error("Error checking payment status:", err);
                              paymentMessage.innerText = "Error checking payment status.";
                          });
                  }
              }
          }, 1000);
      }

      // On page load, check if order_id is in URL and redirect backend
      window.onload = function () {
          const urlParams = new URLSearchParams(window.location.search);
          const orderId = urlParams.get('order_id');

          if (orderId) {
              // Let backend handle status
              window.location.href = `/cashfree/redirect?order_id=${orderId}`;
          }
      };
    </script>

    <!-- upi  -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const upiForm = document.getElementById("upiForm");
        const upiStatus = document.getElementById("upiStatus");
        const startBtn = document.getElementById("upiInitBtn");
        const payBtn = document.getElementById("upiPayBtn");
        const vpainput = document.getElementById("vpainput");

        const data = {
          token: @json($data['token'] ?? ''),
          apitxnid: @json($data['apitxnid'] ?? ''),
          name: @json($data['name'] ?? ''),
          email: @json($data['email'] ?? ''),
          mobile: @json($data['mobile'] ?? ''),
          type: @json($data['type'] ?? ''),
          return_url: @json($data['return_url'] ?? ''),
          amount: @json($data['amount'] ?? ''),
          callback: @json($data['callback'] ?? '')
        };

        let component = null;
        let sessionId = null;
        let currentOrderId = null;

        upiForm.addEventListener("submit", function (e) {
          e.preventDefault();
          upiStatus.textContent = "Creating payment session...";
          startBtn.disabled = true;

          fetch("/cashfree/payment-session", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify(data)
          })
          .then(res => res.json())
          .then(response => {
            if (response.payment_session_id && response.order_id) {
              sessionId = response.payment_session_id;
              currentOrderId = response.order_id;

              const cashfree = Cashfree({ mode: "sandbox" });

              component = cashfree.create("upiCollect", {
                style: {
                  input: {
                    borderColor: "#ced4da",
                    borderRadius: "0.25rem",
                    padding: "0.5rem",
                    width: "100%",
                  }
                }
              });

              component.mount(vpainput);
              upiStatus.textContent = "Enter your UPI ID and click Confirm & Pay";
              payBtn.classList.remove("d-none");
            } else {
              upiStatus.textContent = "Failed to create payment session.";
              startBtn.disabled = false;
            }
          })
          .catch((err) => {
            console.error("Error fetching session:", err);
            upiStatus.textContent = "An error occurred while creating session.";
            startBtn.disabled = false;
          });
        });

        payBtn.addEventListener("click", function () {
          if (!component || !sessionId || !currentOrderId) {
            upiStatus.textContent = "Payment component not initialized.";
            return;
          }

          if (!component.isComplete()) {
            upiStatus.textContent = "Please enter a valid UPI ID.";
            return;
          }

          payBtn.disabled = true;
          payBtn.textContent = "Processing...";

          const cashfree = Cashfree({ mode: "sandbox" });

          const returnUrl = `http://127.0.0.1:8000/cashfree/redirect?order_id=${currentOrderId}`;

          cashfree.pay({
            paymentMethod: component,
            paymentSessionId: sessionId,
            returnUrl: returnUrl,
            redirect: "always",
            redirectTarget: "_self"
          }).then(function (res) {
            if (res.error) {
              upiStatus.textContent = "Payment failed: " + res.error.message;
              payBtn.disabled = false;
              payBtn.textContent = "Retry";
            } else {
              upiStatus.textContent = "Payment response received.";
              payBtn.textContent = "Done";
            }
          }).catch(function (err) {
            console.error("Cashfree error:", err);
            upiStatus.textContent = "Something went wrong: " + err.message;
            payBtn.disabled = false;
            payBtn.textContent = "Retry";
          });
        });

        // Redirect handler (should not conflict with DOMContentLoaded)
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');

        if (orderId) {
          window.location.href = `/cashfree/redirect?order_id=${orderId}`;
        }
      });
    </script>


    <!-- cards -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const payBtn = document.getElementById("pay-card");
        const messageBox = document.getElementById("paymentMessage");

        const data = {
          token: @json($data['token'] ?? ''),
          apitxnid: @json($data['apitxnid'] ?? ''),
          name: @json($data['name'] ?? ''),
          email: @json($data['email'] ?? ''),
          mobile: @json($data['mobile'] ?? ''),
          type: @json($data['type'] ?? ''),
          return_url: @json($data['return_url'] ?? ''),
          amount: @json($data['amount'] ?? ''),
          callback: @json($data['callback'] ?? '')
        };

        const cashfree = Cashfree({ mode: "sandbox" }); // Change to "production" in live

        const cardNumber = cashfree.create("cardNumber");
        cardNumber.mount("#card-number");

        const cardHolder = cashfree.create("cardHolder");
        cardHolder.mount("#cardHolder");

        const cardExpiry = cashfree.create("cardExpiry");
        cardExpiry.mount("#cardExpiry");

        const cardCvv = cashfree.create("cardCvv");
        cardCvv.mount("#cardCvv");

        const save = cashfree.create("savePaymentInstrument", {
          values: { label: "Save this card" }
        });
        save.mount("#save");

        function togglePayBtn() {
          payBtn.disabled = !(
            cardNumber.isComplete() &&
            cardHolder.isComplete() &&
            cardExpiry.isComplete() &&
            cardCvv.isComplete()
          );
        }

        [cardNumber, cardHolder, cardExpiry, cardCvv].forEach(field => {
          field.on("change", togglePayBtn);
        });

        function showMessage(msg, type = "success") {
          messageBox.textContent = msg;
          messageBox.className = "alert alert-" + type;
          messageBox.style.display = "block";
        }

        payBtn.addEventListener("click", async function () {
          payBtn.disabled = true;
          payBtn.innerText = "Processing...";
          messageBox.style.display = "none";

          try {
            const response = await fetch("/cashfree/payment-session", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(data) // <-- Correctly placed here
            });

            const resultData = await response.json();

            if (!resultData.payment_session_id) {
              throw new Error("Payment session ID not received.");
            }

            const result = await cashfree.pay({
              paymentMethod: cardNumber,
              paymentSessionId: resultData.payment_session_id,
              redirectTarget: "_self",
              savePaymentInstrument: save,
              redirect: "always"
            });

            if (result?.error) {
              showMessage(result.error.message, "danger");
            } else {
              showMessage("Redirecting to payment gateway...", "success");
            }
          } catch (error) {
            showMessage(error.message || "Payment failed", "danger");
          } finally {
            payBtn.disabled = false;
            payBtn.innerText = "Pay with Card";
          }
        });
      });
    </script>


   <!-- netbanking -->
    <!-- <script>
          let style = {
              base: {
                  fontSize: "22px"
              }
          };

          // --- Initialize TPV-supported banks ---
          let bankObj = {};

          // Function to setup banks dynamically
          function setupBank(bfor, displayCode) {
              let comp = cashfree.create('netbanking', {
                  values: {
                      netbankingBankName: displayCode
                  },
                  style
              });
              comp.mount("#" + displayCode.toLowerCase());
              comp.on('click', function () {
                  initPay(comp);
              });
              comp.on('paymentrequested', function () {
                  console.log("waiting for cs");
              });
              bankObj[bfor] = comp;
          }

          // Existing TPV-supported banks
          setupBank('hdfc', 'HDFCR');
          setupBank('icici', 'ICICR');
          setupBank('sbi', 'SBINR');
          setupBank('kotak', 'KKBKR');

          // New TPV-supported banks
          setupBank('au', 'AUBLR');
          setupBank('axis', 'UTIBR');
          setupBank('bandhan', 'BDBLR');
          setupBank('barodacorp', 'BARBC');
          setupBank('baroda', 'BARBR');
          setupBank('boi', 'BKIDR');
          setupBank('mahb', 'MAHBR');
          setupBank('canara', 'CNRBR');
          setupBank('capital', 'CLBLR');
          setupBank('central', 'CBINR');
          setupBank('cityunion', 'CIUBR');
          setupBank('cosmos', 'COSBR');
          setupBank('csb', 'CSBKR');
          setupBank('deutsche', 'DEUTR');
          setupBank('dhanlaxmi', 'DLXBR');
          setupBank('federal', 'FDRLR');
          setupBank('gujaratcoop', 'GSCBR');
          setupBank('hsbc', 'HSBCR');
          setupBank('idbi', 'IBKLR');
          setupBank('idfc', 'IDFBR');
          setupBank('indian', 'IDIBR');
          setupBank('iob', 'IOBAR');
          setupBank('indusind', 'INDBR');
          setupBank('jandk', 'JAKAR');
          setupBank('jana', 'JSFBR');
          setupBank('karnataka', 'KARBR');
          setupBank('karur', 'KVBLR');
          setupBank('psb', 'PSIBR');
          setupBank('pnb', 'PUNBR');
          setupBank('rbl', 'RATNR');
          setupBank('saraswat', 'SRCBR');
          setupBank('sbm', 'STCBR');
          setupBank('shivalik', 'SMCBR');
          setupBank('sib', 'SIBLR');
          setupBank('scb', 'SCBLR');
          setupBank('tmb', 'TMBLR');
          setupBank('surat', 'SPCBR');
          setupBank('sutex', 'SUTBR');
          setupBank('uco', 'UCBAR');
          setupBank('union', 'UBINR');
          setupBank('utkarsh', 'UTKSR');
          setupBank('yesbank', 'YESBR');

          let ujjivan = cashfree.create('netbanking', {
              values: {
                  netbankingBankName: 'UJJIVAN' // Custom fallback
              },
              style
          });
          ujjivan.mount("#ujjivan");
          ujjivan.on('click', function () {
              initPay(ujjivan);
          });
          bankObj['ujjivan'] = ujjivan;

          // --- Get payment session ID from Laravel ---
          async function getPaymentSession() {
              try {
                  const response = await fetch("/cashfree/payment-session", {
                    method: "POST",
                    headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                    body: JSON.stringify(data) // <-- Correctly placed here
                  });

                  const data = await response.json();

                  if (data.payment_session_id) {
                      return data.payment_session_id;
                  } else {
                      throw new Error(data.error || 'Unable to get session ID');
                  }
              } catch (error) {
                  console.error('Error fetching payment session:', error);
                  return null;
              }
          }

          // --- Payment Handler ---
          async function initPay(comp) {
              // Disable the bank button to prevent multiple clicks
              comp.disable();

              // Get dynamic session ID
              const sessionId = await getPaymentSession();

              if (!sessionId) {
                  // Enable the bank button again and show error if session ID fetch failed
                  comp.enable();
                  alert('Failed to initialize payment session.');
                  return;
              }

              // Define if payment should be saved
              const save = false; // Set to true if the user chooses to save their payment instrument

              // Call Cashfree's payment API with the dynamic session ID and redirection options
              const result = await cashfree.pay({
                  paymentMethod: comp,
                  paymentSessionId: sessionId,
                  redirectTarget: "_self", // Open payment page in the same tab
                  savePaymentInstrument: save, // Save payment details if user opts in
                  redirect: "always" // Always redirect after payment
              });

              // Re-enable the bank button after payment process
              comp.enable();

              if (result.error) {
                  alert(result.error.message); // Show any error message
              }
              if (result.paymentDetails) {
                  alert(result.paymentDetails.paymentMessage); // Show success message
              }
              if (result.redirect) {
                  console.log("Redirecting...");
                  // You can also handle the redirection here if needed
              }
          }

          // --- Event Listeners --- for all bank elements
          const allBanks = document.getElementsByClassName("bank");
          for (let i = 0; i < allBanks.length; i++) {
              const element = allBanks[i];
              element.addEventListener('click', function (e) {
                  e.preventDefault();
                  const key = element.getAttribute("bfor");
                  if (bankObj[key]) {
                      initPay(bankObj[key]);
                  }
              });
          }
    </script> -->

    <script>
  document.addEventListener("DOMContentLoaded", async function () {
    const style = {
      base: {
        fontSize: "22px"
      }
    };

    const data = {
      token: @json($data['token'] ?? ''),
      apitxnid: @json($data['apitxnid'] ?? ''),
      name: @json($data['name'] ?? ''),
      email: @json($data['email'] ?? ''),
      mobile: @json($data['mobile'] ?? ''),
      type: @json($data['type'] ?? ''),
      return_url: @json($data['return_url'] ?? ''),
      amount: @json($data['amount'] ?? ''),
      callback: @json($data['callback'] ?? '')
    };

    const cashfree = Cashfree({ mode: "sandbox" }); // Use "production" in live

    let bankObj = {};

    function setupBank(bfor, displayCode) {
      const comp = cashfree.create('netbanking', {
        values: {
          netbankingBankName: displayCode
        },
        style
      });

      comp.mount(`#${displayCode.toLowerCase()}`);

      comp.on('click', function () {
        initPay(comp);
      });

      comp.on('paymentrequested', function () {
        console.log(`Waiting for customer authorization: ${displayCode}`);
      });

      bankObj[bfor] = comp;
    }

    // Supported TPV Banks
    const bankMap = {
      hdfc: 'HDFCR',
      icici: 'ICICR',
      sbi: 'SBINR',
      kotak: 'KKBKR',
      au: 'AUBLR',
      axis: 'UTIBR',
      bandhan: 'BDBLR',
      barodacorp: 'BARBC',
      baroda: 'BARBR',
      boi: 'BKIDR',
      mahb: 'MAHBR',
      canara: 'CNRBR',
      capital: 'CLBLR',
      central: 'CBINR',
      cityunion: 'CIUBR',
      cosmos: 'COSBR',
      csb: 'CSBKR',
      deutsche: 'DEUTR',
      dhanlaxmi: 'DLXBR',
      federal: 'FDRLR',
      gujaratcoop: 'GSCBR',
      hsbc: 'HSBCR',
      idbi: 'IBKLR',
      idfc: 'IDFBR',
      indian: 'IDIBR',
      iob: 'IOBAR',
      indusind: 'INDBR',
      jandk: 'JAKAR',
      jana: 'JSFBR',
      karnataka: 'KARBR',
      karur: 'KVBLR',
      psb: 'PSIBR',
      pnb: 'PUNBR',
      rbl: 'RATNR',
      saraswat: 'SRCBR',
      sbm: 'STCBR',
      shivalik: 'SMCBR',
      sib: 'SIBLR',
      scb: 'SCBLR',
      tmb: 'TMBLR',
      surat: 'SPCBR',
      sutex: 'SUTBR',
      uco: 'UCBAR',
      union: 'UBINR',
      utkarsh: 'UTKSR',
      yesbank: 'YESBR',
      ujjivan: 'UJJIVAN' // Included here for consistency
    };

    for (const [key, code] of Object.entries(bankMap)) {
      setupBank(key, code);
    }

    async function getPaymentSession() {
      try {
        const response = await fetch("/cashfree/payment-session", {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(data)
        });

        const json = await response.json();

        if (json.payment_session_id) {
          return json.payment_session_id;
        } else {
          throw new Error(json.error || 'Unable to get session ID');
        }
      } catch (error) {
        console.error('Error fetching payment session:', error);
        return null;
      }
    }

    async function initPay(comp) {
      comp.disable();

      const sessionId = await getPaymentSession();
      if (!sessionId) {
        comp.enable();
        alert('Failed to initialize payment session.');
        return;
      }

      const save = false;

      try {
        const result = await cashfree.pay({
          paymentMethod: comp,
          paymentSessionId: sessionId,
          redirectTarget: "_self",
          savePaymentInstrument: save,
          redirect: "always"
        });

        if (result.error) {
          alert("Error: " + result.error.message);
        } else if (result.paymentDetails) {
          alert("Payment Message: " + result.paymentDetails.paymentMessage);
        }
      } catch (err) {
        console.error("Cashfree payment error:", err);
        alert("Payment failed.");
      } finally {
        comp.enable();
      }
    }
  });
</script>


</body>
</html>
