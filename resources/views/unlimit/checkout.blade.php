
<!-- /resources/views/payu/redirect.blade.php -->

<!DOCTYPE html>
<html>
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
                                    <div class="mb-3" id="vpainput">
                                        <input type="text" class="form-control" name="upi_id" placeholder="Enter your UPI ID (e.g., name@bank)" required>
                                    </div>
                                    <div id="upiStatus" class="mb-2 fw-semibold text-danger"></div>
                                    <button type="submit" class="btn btn-success" id="upiInitBtn">Start Payment</button>
                                </form>

                                <div id="qrContainer" class="mt-3 d-none">
                                    <div class="qr-img" id="qrCode"></div>
                                    <p id="paymentMessage" class="text-success fw-bold mt-2"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCard">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseCard">
                            Credit / Debit Card
                        </button>
                        </h2>
                        <div id="collapseCard" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                        <div class="accordion-body">
                            <!-- <form> -->
                            <input type="text" class="form-control mb-3" id="card_holder" placeholder="Cardholder Name" required>
                            <input type="text" class="form-control mb-3" id="card_no" placeholder="Card Number" maxlength="19" required>
                            <div class="row">
                                <div class="col-md-4">
                                <select class="form-select mb-3" id="card_month" required>
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
                                <select class="form-select mb-3" id="card_year" required>
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
                                <input type="password" id="card_cvv" class="form-control mb-3" placeholder="CVV" maxlength="4" required>
                                </div>
                            </div>
                            <button class="btn btn-primary pay-btn" id="cardPayBtn">Pay with Card</button>
                            <!-- </form> -->
                        </div>
                        </div>
                    </div>

                    <!-- Netbanking -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBank">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseBank">
                            Netbanking
                        </button>
                        </h2>
                        <div id="collapseBank" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                        <div class="accordion-body">
                            <!-- <form> -->
                                <select class="form-select mb-3" id="netBankingSelect" required>
                                    <option value="" disabled selected>Select Your Bank</option>
                                    <option value="INB1111">Kotak Bank</option>
                                    <option value="INB111">Andhra Bank</option>
                                    <option value="INB1113">Andhra Bank Corporate</option>
                                    <option value="INB1114">Allahabad Bank</option>
                                    <option value="INB1115">AU Small Finance Bank</option>
                                    <option value="INB1116">Bank of Baroda - Corporate Banking</option>
                                    <option value="INB1117">Bank of Bahrain and Kuwait</option>
                                    <option value="INB1118">Bank of Baroda - Retail Banking</option>
                                    <option value="INB1119">Bassien Catholic Coop Bank</option>
                                    <option value="INB1120">Bandhan Bank - Corporate</option>
                                    <option value="INB1121">Bandhan Bank</option>
                                    <option value="INB1122">Bank of Maharashtra</option>
                                    <option value="INB1123">Barclays Bank - Corporate Net Banking</option>
                                    <option value="INB1124">Central Bank</option>
                                    <option value="INB1125">Canara Bank</option>
                                    <option value="INB1126">Cosmos Bank</option>
                                    <option value="INB1127">Punjab National Bank - Corporate Banking</option>
                                    <option value="INB1128">Corporation Bank - Corporate</option>
                                    <option value="INB1129">Corporation Bank</option>
                                    <option value="INB1130">Catholic Syrian Bank</option>
                                    <option value="INB1131">City Union Bank</option>
                                    <option value="INB1132">Deutsche Bank</option>
                                    <option value="INB1133">Digibank by DBS</option>
                                    <option value="INB1134">Development Credit Bank</option>
                                    <option value="INB1135">Dena Bank</option>
                                    <option value="INB1136">Dhanlaxmi Bank Corporate</option>
                                    <option value="INB1137">Dhanalakshmi Bank</option>
                                    <option value="INB1138">Equitas Small Finance Bank</option>
                                    <option value="INB1139">ESAF Small Finance Bank</option>
                                    <option value="INB1140">Federal Bank</option>
                                    <option value="INB1141">Fincare Bank - Retail</option>
                                    <option value="INB1142">HDFC Bank</option>
                                    <option value="INB1143">ICICI Bank</option>
                                    <option value="INB1144">IDBI Bank</option>
                                    <option value="INB1145">IDBI Corporate</option>
                                    <option value="INB1146">IDFC FIRST Bank</option>
                                    <option value="INB1147">IndusInd Bank</option>
                                    <option value="INB1148">Indian Bank</option>
                                    <option value="INB1149">Indian Overseas Bank</option>
                                    <option value="INB1150">JK Bank</option>
                                    <option value="INB1151">Janata Sahakari Bank Ltd Pune</option>
                                    <option value="INB1152">Karnataka Bank</option>
                                    <option value="INB1153">Kalyan Janata Sahakari Bank</option>
                                    <option value="INB1154">The Kalupur Commercial Co-Operative Bank</option>
                                    <option value="INB1155">Karur Vysya Bank</option>
                                    <option value="INB1156">Laxmi Vilas Bank - Corporate Net Banking</option>
                                    <option value="INB1157">Laxmi Vilas Bank - Retail Net Banking</option>
                                    <option value="INB1158">Mehsana urban Co-op Bank</option>
                                    <option value="INB1159">North East Small Finance Bank Ltd</option>
                                    <option value="INB1160">NKGSB Co-op Bank</option>
                                    <option value="INB1161">Oriental Bank of Commerce</option>
                                    <option value="INB1162">Karnataka Gramin Bank</option>
                                    <option value="INB1163">Punjab & Maharashtra Co-op Bank</option>
                                    <option value="INB1164">Punjab National Bank</option>
                                    <option value="INB1165">PNB Yuva Netbanking</option>
                                    <option value="INB1166">Punjab and Sindh Bank</option>
                                    <option value="INB1167">RBL Bank Limited</option>
                                    <option value="INB1168">RBL Bank Limited - Corporate Banking</option>
                                    <option value="INB1169">State bank Of India</option>
                                    <option value="INB1170">Standard Chartered Bank</option>
                                    <option value="INB1171">South Indian Bank</option>
                                    <option value="INB1172">Suryoday Small Finance Bank</option>
                                    <option value="INB1173">Shamrao Vithal Co-op Bank - Corporate</option>
                                    <option value="INB1174">Shamrao Vithal Co-op Bank</option>
                                    <option value="INB1175">Saraswat Bank</option>
                                    <option value="INB1176">Syndicate Bank</option>
                                    <option value="INB1177">Thane Bharat Sahakari Bank Ltd</option>
                                    <option value="INB1178">TJSB Bank</option>
                                    <option value="INB1179">Tamilnad Mercantile Bank</option>
                                    <option value="INB1180">Tamil Nadu State Co-operative Bank</option>
                                    <option value="INB1181">Union Bank of India</option>
                                    <option value="INB1182">UCO Bank</option>
                                    <option value="INB1183">United Bank Of India</option>
                                    <option value="INB1184">AXIS Bank</option>
                                    <option value="INB1185">Vijaya Bank</option>
                                    <option value="INB1186">Varachha Co-operative Bank Limited</option>
                                    <option value="INB1187">Yes Bank Corporate</option>
                                    <option value="INB1188">Yes Bank</option>
                                    <option value="INB1189">Zoroastrian Co-Operative Bank Ltd</option>
                                </select>
                            <button class="btn btn-primary pay-btn" id="netBankingPayBtn">Pay via NetBanking</button>
                            <!-- </form> -->
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


    <!-- QR generating  -->
    <script>
        document.getElementById('generateQR').addEventListener('click', function (e) {
            e.preventDefault();

            const generateBtn = document.getElementById('generateQR');
            const qrContainer = document.getElementById('qrContainer');
            const qrCode = document.getElementById('qrCode');
            const timerDisplay = document.getElementById('timer');
            const paymentMessage = document.getElementById('paymentMessage');

            // Disable the button to prevent multiple clicks
            generateBtn.disabled = true;
            generateBtn.innerText = 'Getting QR...';

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

            $.ajax({
                url: '{{ route("create.QR") }}',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response && response.qr_code_content) {
                        // Show QR code
                        const qrImageUrl = `https://quickchart.io/qr?text=${encodeURIComponent(response.qr_code_content)}&size=200`;
                        qrCode.innerHTML = `<img src="${qrImageUrl}" alt="UPI QR Code">`;
                        qrContainer.classList.remove('hidden');

                        // Hide the generate button
                        generateBtn.style.display = 'none';

                        // Start timer
                        startTimer(60, () => {
                            qrContainer.classList.add('hidden');
                            qrCode.innerHTML = '';
                            generateBtn.disabled = false;
                            generateBtn.innerText = 'Generate QR';
                            generateBtn.style.display = 'inline-block';
                            paymentMessage.innerText = '⏰ QR expired. Please generate a new one.';
                        });

                        paymentMessage.innerText = '';
                    } else {
                        alert('❌ Invalid response from server.');
                        generateBtn.disabled = false;
                        generateBtn.innerText = 'Generate QR';
                    }
                },
                error: function (xhr) {
                    alert('❌ Order failed: ' + (xhr.responseJSON?.error || 'Unknown error'));
                    console.error(xhr.responseJSON);
                    generateBtn.disabled = false;
                    generateBtn.innerText = 'Generate QR';
                }
            });
        });

        function startTimer(duration, onExpire) {
            let timer = duration;
            const display = document.getElementById('timer');
            const interval = setInterval(() => {
                display.textContent = timer;
                if (--timer < 0) {
                    clearInterval(interval);
                    display.textContent = 'Expired';
                    onExpire(); // Call the callback function when time is up
                }
            }, 1000);
        }
    </script>

    <!-- UPI Payment -->
    <script>
        $('#upiForm').on('submit', function (e) {
            e.preventDefault();

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

            const upiID = $('input[name="upi_id"]').val();

            if (!upiID) {
                $('#upiStatus').text("⚠️ Please enter a valid UPI ID.");
                return;
            }

            // Include UPI ID in the data object
            data.upi_id = upiID;

            $.ajax({
                url: '{{ route("upi.payment") }}',
                type: 'POST',
                data: JSON.stringify(data),  // Send all data as a JSON string
                contentType: 'application/json',  // Set content type as JSON
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 'AUTHORIZED') {
                        $('#upiStatus').removeClass('text-danger').addClass('text-success')
                            .html(`✅ Payment Successful<br>Payment ID: ${response.payment_id}`);
                    } else {
                        $('#upiStatus').removeClass('text-success').addClass('text-danger')
                            .text(`❌ Payment Failed or Not Authorized. Status: ${response.status}`);
                    }
                },
                error: function (xhr) {
                    $('#upiStatus').removeClass('text-success').addClass('text-danger')
                        .text('❌ Payment request failed.');
                    console.error(xhr.responseText);
                }
            });
        });
    </script>

    <!-- NetBanking  -->
    <script>
        $(document).ready(function() {
            $('#netBankingPayBtn').on('click', function(e) {
                e.preventDefault(); 
                
                var selectEl = document.getElementById("netBankingSelect");

                // If no value is selected
                if (!selectEl.value) {
                    selectEl.setCustomValidity("Please select your bank.");
                    selectEl.reportValidity(); // Shows the native validation message
                    return;
                }

                // Clear any previous message
                selectEl.setCustomValidity("");
                
                // Ajax call Net Banking
                const data = {
                    name: @json($data['name'] ?? ''),
                    email: @json($data['email'] ?? ''),
                    amount: @json($data['amount'] ?? ''),
                    bank_code: selectEl.value
                };

                $.ajax({
                url: '{{ route("unlimit.netbanking") }}',
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response) {
                        window.location.href = response; // 🔁 Redirect browser
                    } else {
                        alert('Redirect URL not found.');
                    }
                },
                error: function (xhr, status, error) {

                }
                });
            });


            // cards

                 $('#cardPayBtn').on('click', function(e) {
                        e.preventDefault(); 
            
                    var card_holder = document.getElementById("card_holder");
                    var card_no = document.getElementById("card_no");
                    var card_month = document.getElementById("card_month");
                    var card_year = document.getElementById("card_year");
                    var card_cvv = document.getElementById("card_cvv");

                    // If no value is selected
                    if (!card_holder.value) {
                        card_holder.setCustomValidity("Please enter card holder name.");
                        card_holder.reportValidity(); // Shows the native validation message
                        return;
                    }else if (!card_no.value) {
                        card_no.setCustomValidity("Please enter card number.");
                        card_no.reportValidity(); // Shows the native validation message
                        return;
                    }else if (!card_month.value) {
                        card_month.setCustomValidity("Please enter card expiry month.");
                        card_month.reportValidity(); // Shows the native validation message
                        return;
                    }else if (!card_year.value) {
                        card_year.setCustomValidity("Please enter card expiry year.");
                        card_year.reportValidity(); // Shows the native validation message
                        return;
                    }else if (!card_cvv.value) {
                        card_cvv.setCustomValidity("Please enter cvv.");
                        card_cvv.reportValidity(); // Shows the native validation message
                        return;
                    }

                    // Clear any previous message
                    card_holder.setCustomValidity("");
                    card_no.setCustomValidity("");
                    card_month.setCustomValidity("");
                    card_year.setCustomValidity("");
                    card_cvv.setCustomValidity("");
                    
                    // Ajax call Net Banking
                    const data = {
                        name: @json($data['name'] ?? ''),
                        email: @json($data['email'] ?? ''),
                        amount: @json($data['amount'] ?? ''),
                        card_holder: card_holder.value,
                        card_no: card_no.value,
                        card_month: card_month.value,
                        card_year: card_year.value,
                        card_cvv: card_cvv.value
                    };

                    $.ajax({
                    url: '{{ route("unlimit.cardpayment") }}',
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response) {
                            window.location.href = response.redirect_url;
                        } else {
                            alert('Redirect URL not found.');
                        }
                    },
                    error: function (xhr, status, error) {

                    }
                    });
                });

        });
        
    </script>

    <!-- cards -->

</body>
</html>