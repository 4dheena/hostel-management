<!DOCTYPE html>
<html>
<head>

<title>Room Swap Request</title>

<style>

/* ================= RESET ================= */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", sans-serif;
}

/* ================= PAGE BACKGROUND ================= */
body {
  background: #f4f7fb;
  color: #1f2937;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* ================= MAIN CONTAINER ================= */
.container {
  max-width: 800px;
  width: 100%;
  margin: 20px;
}

/* ================= CARD ================= */
.card {
  background: #ffffff;
  border-radius: 18px;
  padding: 40px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

h2 {
  font-size: 28px;
  margin-bottom: 30px;
  text-align: center;
  color: #1e293b;
}

/* ================= SECTIONS ================= */
.section {
  margin-bottom: 30px;
  padding: 25px;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
}

.section h3 {
  font-size: 18px;
  margin-bottom: 20px;
  color: #1e293b;
  border-bottom: 2px solid #1aa6a6;
  padding-bottom: 8px;
}

/* ================= FORM GRID ================= */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 22px 26px;
}

.form-grid.full-width {
  grid-template-columns: 1fr;
}

/* ================= INPUTS ================= */
label {
  font-size: 13px;
  font-weight: 600;
  display: block;
  margin-bottom: 6px;
}

input, textarea {
  width: 100%;
  margin-top: 6px;
  padding: 10px 12px;
  height: 42px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  font-size: 14px;
}

input:focus, textarea:focus {
  border-color: #1aa6a6;
  box-shadow: 0 0 0 2px rgba(26,166,166,0.2);
  outline: none;
}

textarea {
  resize: vertical;
  min-height: 80px;
}

/* ================= CHECKBOX ================= */
.checkbox-container {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 20px;
}

.checkbox-container input[type="checkbox"] {
  width: auto;
  height: auto;
  margin: 0;
}

.checkbox-container label {
  font-size: 14px;
  font-weight: 400;
  margin: 0;
  line-height: 1.4;
}

/* ================= BUTTON ================= */
button {
  width: 100%;
  padding: 14px 36px;
  margin-top: 32px;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #1e5aa8, #3b82f6);
  color: #fff;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.25s ease;
}

button:hover {
  background: linear-gradient(135deg, #1a4a8a, #2563eb);
  transform: translateY(-1px);
}

</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>Room Swap Request Form</h2>

<p style="text-align: center; color: #6b7280; margin-bottom: 30px; font-size: 14px;">
Student 1 will receive Student 2's room, and Student 2 will receive Student 1's room.
</p>

<form id="swapForm">

<!-- Student 1 -->

<div class="section">

<h3>Student 1 (Requesting Swap)</h3>

<div class="form-grid">
<div>
<label>Student Name</label>
<input type="text" id="name1" required>
</div>

<div>
<label>Hostel ID</label>
<input type="text" id="hostelID1" required>
</div>

<div>
<label>Current Room Number</label>
<input type="text" id="room1" required>
</div>
</div>

</div>

<!-- Student 2 -->

<div class="section">

<h3>Student 2 (Agreeing to Swap)</h3>

<div class="form-grid">
<div>
<label>Student Name</label>
<input type="text" id="name2" required>
</div>

<div>
<label>Hostel ID</label>
<input type="text" id="hostelID2" required>
</div>

<div>
<label>Current Room Number</label>
<input type="text" id="room2" required>
</div>
</div>

</div>

<!-- Swap Details -->

<div class="section">

<h3>Swap Agreement</h3>

<div class="form-grid full-width">
<label>Reason for Room Swap</label>
<textarea id="reason" rows="3" placeholder="Please provide a reason for the room swap request"></textarea>

<div class="checkbox-container">
<input type="checkbox" id="agreement" required>
<label for="agreement">Both students confirm they agree to swap rooms with each other.</label>
</div>
</div>

</div>

<button type="submit">Submit Room Swap Request</button>

</form>

</div>

</div>

<script>

document.getElementById("swapForm").addEventListener("submit",function(e){

e.preventDefault();

let swapRequest = {

student1:{
name:document.getElementById("name1").value,
hostelID:document.getElementById("hostelID1").value,
room:document.getElementById("room1").value
},

student2:{
name:document.getElementById("name2").value,
hostelID:document.getElementById("hostelID2").value,
room:document.getElementById("room2").value
},

reason:document.getElementById("reason").value,
status:"Pending"

};

let swaps = JSON.parse(localStorage.getItem("swapRequests")) || [];

swaps.push(swapRequest);

localStorage.setItem("swapRequests",JSON.stringify(swaps));

alert("Room Swap Request Submitted to Warden!");

this.reset();

});

</script>

</body>
</html>