<!DOCTYPE html>
<html>
<head>
<title>Hostel Mess Management</title>
<link rel="stylesheet" href="../assets/css/mess.css">
</head>

<body>

<header>Hostel Mess Management System</header>

<div class="container">

<!-- STUDENT PANEL -->
<div class="card">
<h2>Meal Selection</h2>

<label>Select Meal Type</label>
<select id="mealType">
<option>Breakfast</option>
<option>Lunch</option>
<option>Dinner</option>
</select>

<label>Food Choice</label>
<select id="foodChoice">
<option value="veg">Veg</option>
<option value="nonveg">Non-Veg</option>
</select>

<label>Mess Cut</label>
<select id="messCut">
<option value="no">No</option>
<option value="yes">Yes</option>
</select>

<button onclick="saveSelection()">Save Selection</button>
</div>

<!-- QR SCAN -->
<div class="card">
<h2>QR Attendance</h2>
<p>Simulated QR Scan</p>
<button onclick="scanQR()">Scan QR</button>
<div class="stat" id="scanStatus">Not scanned</div>
</div>

<!-- ADMIN STATS -->
<div class="card">
<h2>Daily Mess Statistics</h2>
<div class="stat">Total Meals: <span id="totalMeals">0</span></div>
<div class="stat">Veg Count: <span id="vegCount">0</span></div>
<div class="stat">Non-Veg Count: <span id="nonvegCount">0</span></div>
</div>

<!-- BILLING -->
<div class="card">
<h2>Monthly Billing</h2>
<div class="bill">Veg Meals: <span id="billVeg">0</span></div>
<div class="bill">Non-Veg Meals: <span id="billNonveg">0</span></div>
<div class="bill">Fine: ₹<span id="fine">0</span></div>
<hr>
<div class="bill">Total Bill: ₹<span id="totalBill">0</span></div>

<button onclick="generateBill()">Generate Bill</button>
<button onclick="markUnpaid()">Mark Unpaid</button>
</div>

</div>

<script>

let vegCount = 0
let nonvegCount = 0
let totalMeals = 0

let billVeg = 0
let billNonveg = 0
let fine = 0

let currentChoice = ""
let messCut = false

const VEG_PRICE = 50
const NONVEG_PRICE = 80
const DAILY_FINE = 10

function saveSelection(){
currentChoice = document.getElementById("foodChoice").value
messCut = document.getElementById("messCut").value === "yes"
alert("Selection Saved")
}

function scanQR(){

if(messCut){
alert("Mess cut marked. Meal not counted.")
return
}

totalMeals++

if(currentChoice === "veg"){
vegCount++
billVeg++
}
else{
nonvegCount++
billNonveg++
}

document.getElementById("scanStatus").innerText = "Meal Recorded"
updateStats()
}

function updateStats(){
document.getElementById("vegCount").innerText = vegCount
document.getElementById("nonvegCount").innerText = nonvegCount
document.getElementById("totalMeals").innerText = totalMeals
}

function generateBill(){
let total =
(billVeg * VEG_PRICE) +
(billNonveg * NONVEG_PRICE) +
fine

document.getElementById("billVeg").innerText = billVeg
document.getElementById("billNonveg").innerText = billNonveg
document.getElementById("fine").innerText = fine
document.getElementById("totalBill").innerText = total
}

function markUnpaid(){
fine += DAILY_FINE
alert("Fine Added for Unpaid Bill")
}

</script>

</body>
</html>
