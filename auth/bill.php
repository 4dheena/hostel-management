<?php
session_start();
require_once "../database/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT s.student_id, s.name, s.email FROM students s WHERE s.user_id = ? LIMIT 1"
);
$stmt->bind_param('i', $userId);
stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die('Student profile not found.');
}

$studentId = $student['student_id'];

$attendanceStmt = $conn->prepare(
    "SELECT COUNT(DISTINCT attendance_date) AS attendance_days FROM mess_attendance WHERE student_id = ?"
);
$attendanceStmt->bind_param('s', $studentId);
$attendanceStmt->execute();
$attendanceCount = $attendanceStmt->get_result()->fetch_assoc();
$attendanceDays = intval($attendanceCount['attendance_days'] ?? 0);

$paidStmt = $conn->prepare(
    "SELECT IFNULL(SUM(amount), 0) AS total_paid FROM payments WHERE student_id = ?"
);
$paidStmt->bind_param('s', $studentId);
$paidStmt->execute();
$paid = $paidStmt->get_result()->fetch_assoc();
$totalPaid = floatval($paid['total_paid'] ?? 0);

$ratePerDay = 160;
$totalBill = $attendanceDays * $ratePerDay;
$dueAmount = max(0, $totalBill - $totalPaid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mess Billing</title>
<link rel="stylesheet" href="../assets/css/mess.css">
<style>
.billing-card { max-width: 700px; margin: 40px auto; padding: 24px; background: #fff; border-radius: 16px; box-shadow: 0 16px 30px rgba(0,0,0,0.08); }
.billing-card h1 { margin-bottom: 16px; }
.billing-card .summary { display: grid; gap: 14px; margin-top: 24px; }
.billing-card .summary div { display: flex; justify-content: space-between; padding: 14px 18px; background: #f8f9fb; border-radius: 12px; }
.billing-card .summary strong { color: #1f3d72; }
.billing-card .status { margin-top: 22px; padding: 16px; border-radius: 12px; background: #eef7ff; color: #155e8b; }
.billing-card a { display: inline-block; margin-top: 16px; color: #fff; background: #1f3d72; padding: 12px 20px; border-radius: 10px; text-decoration: none; }
</style>
</head>
<body>
<div class="billing-card">
  <h1>Mess Billing</h1>
  <p>Student: <strong><?php echo htmlspecialchars($student['name']); ?></strong></p>
  <p>Student ID: <strong><?php echo htmlspecialchars($studentId); ?></strong></p>

  <div class="summary">
    <div><span>Effective mess days</span><strong><?php echo $attendanceDays; ?></strong></div>
    <div><span>Rate per day</span><strong>₹<?php echo number_format($ratePerDay, 2); ?></strong></div>
    <div><span>Total bill</span><strong>₹<?php echo number_format($totalBill, 2); ?></strong></div>
    <div><span>Total paid</span><strong>₹<?php echo number_format($totalPaid, 2); ?></strong></div>
    <div><span>Due amount</span><strong>₹<?php echo number_format($dueAmount, 2); ?></strong></div>
  </div>

  <div class="status">
    <?php if ($attendanceDays === 0): ?>
      No mess attendance has been recorded yet.
    <?php else: ?>
      Billing is based on effective days. Any number of meals in a day counts as one charge.
    <?php endif; ?>
  </div>

  <a href="../mess.html">Back to Mess Scanner</a>
</div>
</body>
</html>
