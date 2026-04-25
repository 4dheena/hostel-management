<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Mess QR Code Scanner</title>

<link rel="stylesheet" href="assets/css/mess.css">

<script src="https://unpkg.com/html5-qrcode"></script>

</head>

<body>

<div class="container">

<h1>Mess QR Code Scanner</h1>


<!-- Scanner Section -->
<div class="scanner-section">
  <div id="reader"></div>
 
</div>

<p class="subtitle">Everything you need to manage your mess efficiently</p>


<div class="cards">

<a href="auth/menu.html">
<div class="card">
<div class="icon menu">🍽</div>
<h3>Mess Menu</h3>
<p>Complete daily and weekly meal plans with visual display</p>
</div>
</a>

<a href="auth/messcut.html">
<div class="card">
<div class="icon cut">🎟</div>
<h3>Messcuts</h3>
<p>Easily manage mess coupons and token systems for meals</p>
</div>
</a>

<a href="auth/bill.php">
<div class="card">
<div class="icon bill">💰</div>
<h3>Billing & Payments</h3>
<p>Automated billing with payment tracking and receipt generation</p>
</div>
</a>

<a href="stats.html">
<div class="card">
<div class="icon stats">📈</div>
<h3>Statistics</h3>
<p>Analyze consumption trends, costs, and feedback data</p>
</div>
</a>

</div>

</div>


<script>

function onScanSuccess(decodedText) {

alert("QR Code Scanned: " + decodedText);

}

let scanner = new Html5QrcodeScanner(
"reader",
{ fps: 10, qrbox: 250 }
);

scanner.render(onScanSuccess);

</script>

</body>
</html>