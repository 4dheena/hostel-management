<?php
include("../database/db_connect.php");

if(isset($_POST['submit'])) {

    $name = $_POST['student_name'];
    $hostel_id = $_POST['hostel_id'];
    $room = $_POST['room_number'];
    $contact = $_POST['contact_number'];
    $course = $_POST['course_sem'];
    $date = $_POST['vacating_date'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO vacating_requests 
    (student_name, hostel_id, room_number, contact_number, course_sem, vacating_date, reason) 
    VALUES 
    ('$name','$hostel_id','$room','$contact','$course','$date','$reason')";

    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Vacating request submitted');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>

<title>Hostel Vacating Form</title>

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
}

/* ================= MAIN CONTAINER ================= */
.container {
  max-width: 600px;
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

input, select, textarea {
  width: 100%;
  margin-top: 6px;
  padding: 10px 12px;
  height: 42px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  font-size: 14px;
}

input:focus, select:focus, textarea:focus {
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
  grid-column: 1 / -1;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 10px;
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

<h2>Hostel Vacating Form</h2>


<form method="POST" action="" class="form-grid">

<div>
<label>Student Name</label>
<input type="text" name="student_name" required>
</div>

<div>
<label>Hostel ID</label>
<input type="text" name="hostel_id" required>
</div>

<div>
<label>Room Number</label>
<input type="text" name="room_number" required>
</div>

<div>
<label>Contact Number</label>
<input type="tel" name="contact_number" required>
</div>

<div>
<label>Course / Semester</label>
<input type="text" name="course_sem" placeholder="Example: B.Tech S4">
</div>

<div>
<label>Date of Vacating</label>
<input type="date" name="vacating_date" required>
</div>

<div class="form-grid full-width">
<label>Reason for Vacating</label>
<textarea name="reason" rows="3" placeholder="End of semester / Course completion"></textarea>
</div>

<div class="checkbox-container">
<input type="checkbox" required>
<label>I confirm that I have cleared all hostel dues and returned hostel property.</label>
</div>

<button type="submit" name="submit">Submit Vacating Request</button>

</form>

</div>

</div>


</body>
</html>