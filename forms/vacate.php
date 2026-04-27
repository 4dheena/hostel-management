<!DOCTYPE html>
<?php $msg = $_GET['msg'] ?? ''; ?>
<html>
<head>

<title>Vacate Request</title>

<style>

/* ================= RESET ================= */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", sans-serif;
}

/* ================= PAGE ================= */
body {
  background: #f4f7fb;
  color: #1f2937;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* ================= CONTAINER ================= */
.container {
  max-width: 700px;
  width: 100%;
}

/* ================= CARD ================= */
.card {
  background: #ffffff;
  border-radius: 18px;
  padding: 40px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

/* ================= HEADINGS ================= */
h2 {
  font-size: 28px;
  margin-bottom: 25px;
  text-align: center;
  color: #1e293b;
}

/* ================= INPUTS ================= */
label {
  font-size: 13px;
  font-weight: 600;
  display: block;
  margin-bottom: 6px;
  margin-top: 15px;
}

input, textarea {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  font-size: 14px;
}

textarea {
  resize: vertical;
  min-height: 90px;
}

/* ================= BUTTON ================= */
button {
  width: 100%;
  padding: 14px;
  margin-top: 25px;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #1e5aa8, #3b82f6);
  color: #fff;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  opacity: 0.9;
}

</style>

</head>

<body>

<div class="container">
<div class="card">

<h2>Vacate Request</h2>
<?php if($msg != ""): ?>
<div style="
background:#e6f7ff;
padding:12px;
margin-bottom:20px;
border-radius:8px;
color:#1e293b;
font-weight:500;
">
<?= $msg ?>
</div>
<?php endif; ?>
<form action="../vacate/submit_vacate.php" method="POST">

    <!-- STUDENT ID -->
    <label>Student ID</label>
    <input type="text" name="student_id" required>

    <!-- CURRENT ROOM -->
    <label>Current Room Number</label>
    <input type="text" name="room_number" required>

    <!-- VACATE DATE -->
    <label>Vacate Date</label>
    <input type="date" name="vacate_date" required>

    <!-- REASON -->
    <label>Reason for Vacating</label>
    <textarea name="reason" required></textarea>

    <!-- SUBMIT -->
    <button type="submit">Submit Vacate Request</button>

</form>

</div>
</div>

</body>
</html>