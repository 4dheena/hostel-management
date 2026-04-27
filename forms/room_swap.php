
<!DOCTYPE html>
<?php
$msg = $_GET['msg'] ?? '';
?>
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
<h2>Room Swap Request</h2>
<?php if ($msg != ""): ?>
<div style="background:#e6f7ff;padding:12px;margin-bottom:20px;border-radius:8px;">
    <?php echo $msg; ?>
</div>
<?php endif; ?>
<form action="../room_swap/submit_room_swap.php" method="POST">

    <!-- 🔹 STUDENT A -->
    <h3>Student A Details</h3>

    <label>Name</label>
    <input type="text" name="student_a_name" required>

    <label>Student ID</label>
    <input type="text" name="student_a_id" required>

    <label>Current Room</label>
    <input type="text" name="room_a" required>


    <!-- 🔹 STUDENT B -->
    <h3>Student B Details</h3>

    <label>Name</label>
    <input type="text" name="student_b_name" required>

    <label>Student ID</label>
    <input type="text" name="student_b_id" required>

    <label>Current Room</label>
    <input type="text" name="room_b" required>


    <!-- 🔹 REASON -->
    <h3>Reason for Swap</h3>
    <textarea name="reason" required></textarea>


    <!-- 🔹 SUBMIT -->
    <button type="submit">Submit Room Swap</button>

</form>

</body>
</html>