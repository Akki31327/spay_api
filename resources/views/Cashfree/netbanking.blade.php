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

		.bank {
			text-align: center;
			padding: 20px;
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
		.container{
			border-radius: 20px;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			padding: 20px;
			border: 1px solid #ddd;
		}
    </style>
</head>
<body>
<div class="container ">
		<div class="row">

			<div class="col  ">
				<div class="row">
					<h1>Demo of Netbanking Payment</h1>
				</div>
			</div>
			<hr>
			<div class="col offset-1 col-10">
				
			</div>
		</div>
		<div class="row">
			<div class="col col-lg-8 col-sm-12">
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
			<div class="col col-lg-4 col-sm-10">
				<div class="">
					<div class="card" style="width: 18rem;">
						<div class="card-body pb-0">
							<h5 class="card-title">Select Bank</h5>
							 
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
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        const cashfree = Cashfree({mode:"sandbox"});
		const paymentMessage = document.getElementById("paymentMessage");
		let style = {
			base: {
				fontSize: "22px"
			}
		}
		let hdfc = cashfree.create('netbanking', {
			values: {
				netbankingBankName: 'HDFCR'
			},
			style
		});
		hdfc.mount("#hdfcr");
		hdfc.on('click', function(){
			initPay(hdfc)
		});
		hdfc.on('paymentrequested', function(){
			console.log("waiting for cs");
		});

		let icici = cashfree.create('netbanking', {
			values: {
				netbankingBankName: 'ICICR'
			},
			style
		});
		icici.mount("#icici");
		icici.on('click', function(){
			initPay(icici)
		})

		let sbi = cashfree.create('netbanking', {
			values: {
				netbankingBankName: 'SBINR'
			},
			style
		});
		sbi.mount("#sbi");
		sbi.on('click', function(){
			initPay(sbi)
		})

		let kotak = cashfree.create('netbanking', {
			values: {
				netbankingBankName: 'KKBKR'
			},
			style
		});
		kotak.mount("#kotak");
		kotak.on('click', function(){
			initPay(kotak)
		})
		let bankObj = {
			kotak: kotak,
			sbi:sbi,
			icici: icici,
			hdfc: hdfc
		}

		
		function initPay(comp){
			paymentMessage.innerText = "";
			paymentMessage.classList.remove("alert-danger");
			paymentMessage.classList.remove("alert-success");
			comp.disable();
			cashfree.pay({
				paymentMethod: comp,
				paymentSessionId: document.getElementById("paymentSessionId").value,
				returnUrl: document.getElementById("returnUrl").value,
			}).then(function (data) {
				comp.enable();
				if(data.error) {
					paymentMessage.innerHTML = data.error.message;
					paymentMessage.classList.add("alert-danger");
				}
				if(data.paymentDetails) {
					paymentMessage.innerHTML = data.paymentDetails.paymentMessage;
					paymentMessage.classList.add("alert-success");
				}
				if(data.redirect){
					console.log("We are redirtion");
				}	
			});
		}

		const allBanks = document.getElementsByClassName("bank");
		for (let i = 0; i < allBanks.length; i++) {
			const element = allBanks[i];
			element.addEventListener('click', function(e){
				e.preventDefault();
				initPay(bankObj[e.target.getAttribute("bfor")])
			})	
		}
    </script>
</body>
</html>