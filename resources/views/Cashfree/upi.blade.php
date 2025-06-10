<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .card {
			box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
			padding-bottom: 0px;
			border-radius: 14px;
		}
		.container{
			border-radius: 20px;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			padding: 20px;border: 1px solid #ddd;
		}
    </style>
</head>
<body>
<main class="container">
		<div class="row">

			<div class="col  ">
				<div class="row">
					<h1>Demo of UPI Collect</h1>
				</div>
			</div>
			<hr>

		</div>
		<div class="row">
			<div class="col col-lg-6 col-sm-12">
				<div class="row">
					<h4>Payment Session ID</h4>
					<div class="col">
						<textarea name="" id="paymentSessionId" class="form-control"></textarea>
						<span class="form-text text-muted">Don't have a paymentSessionId? Get a sample one <a href="https://test.cashfree.com/pgappsdemos/sample-psi.php" target="_blank">here</a> for sandbox mode</span>
					</div>
				</div>
				
				<div class="row mt-4">
					<h4>Return URL <span class="badge h6 bg-danger">Required</span></h4>
					<div class="col">
						<textarea name="" id="returnUrl" class="form-control">https://test.cashfree.com/pgappsdemos/v3success.php?myorder={order_id}</textarea>
						<span class="form-text text-muted">Don't have a paymentSessionId? Get a sample one <a href="https://test.cashfree.com/pgappsdemos/sample-psi.php" target="_blank">here</a> for sandbox mode</span>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col ">
						<p class="alert" id="paymentMessage">
						</p>
					</div>
				</div>
			</div>
			<div class="col col-lg-6 col-sm-10">

				<div class="card" style="width: 24rem;">
					<div class="card-body pb-0">
						 
						<div class="row">
							<h4></h4>
							<div class="col">
								<div id="vpainput" class="form-control"></div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col">
								<button class="btn btn-primary btn-block w-100 pull-right" id="pay-collect" type="button">
									Send Collect Request
								</button>
							</div>
						</div>
						<br>
					</div>
				</div>

			</div>
		</div>
	</main>

<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        let paymentBtn = document.getElementById("pay-collect");
		paymentBtn.disabled = true;
		let paymentMessage = document.getElementById("paymentMessage");

		let cashfree = Cashfree({mode: "sandbox"});
		let opt = {
			values: {
				placeholder: "Enter UPI ID"
			}
		};
		let component = cashfree.create("upiCollect", opt);
		// component.mount("#vpainput");
		component.mount(document.getElementById("vpainput"));

		 
		function toggleBtn() {

			if (component.isComplete()) {
				paymentBtn.disabled = false;
			} else {
				paymentBtn.disabled = true;
			}
		}

		component.on('change', function (data) {
			toggleBtn();
		})
		 



		paymentBtn.addEventListener('click', function () {
			paymentBtn.disabled = true;
			paymentBtn.innerText = "Please wait ...";
			paymentMessage.innerText = "";
			paymentMessage.classList.remove("alert-danger");
			paymentMessage.classList.remove("alert-success");
			cashfree.pay({
				paymentMethod: component,
				paymentSessionId: document.getElementById("paymentSessionId").value,
				 
				returnUrl: document.getElementById("returnUrl").value,
			}).then(function (data) {
				if(data.error) {
					paymentMessage.innerHTML = data.error.message;
					paymentMessage.classList.add("alert-danger");
				}
				paymentBtn.innerText = "Retry";
				paymentBtn.disabled = false;
				
			});
		})
    </script>
</body>
</html>