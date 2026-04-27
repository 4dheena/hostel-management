<?php
session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY CHECK ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* ================= FETCH CURRENT SETTINGS ================= */
$result = $conn->query("
    SELECT start_date, end_date, edit_start, edit_end 
    FROM application_settings 
    WHERE id = 1
");
$row = $result->fetch_assoc();

/* ================= HANDLE FORM SUBMIT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $start       = $_POST['start_date'];
    $end         = $_POST['end_date'];
    $edit_start  = $_POST['edit_start'];
    $edit_end    = $_POST['edit_end'];

    $stmt = $conn->prepare("
        UPDATE application_settings
        SET start_date = ?, 
            end_date = ?, 
            edit_start = ?, 
            edit_end = ?
        WHERE id = 1
    ");
    $stmt->bind_param("ssss", $start, $end, $edit_start, $edit_end);
    $stmt->execute();

    $_SESSION['message'] = "✅ Dates updated successfully!";
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Set Application Dates</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      padding: 40px;
    }
    .card {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
    }
    button {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      background: #1aa6a6;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .back-btn {
      margin-top: 10px;
      display: block;
      text-align: center;
      color: #555;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="card">
  <h2>Application & Edit Window Settings</h2>

  <form method="POST">

    <label>Application Start Date</label>
    <input type="datetime-local" name="start_date"
           value="<?= date('Y-m-d\TH:i', strtotime($row['start_date'])) ?>" required>

    <label>Application End Date</label>
    <input type="datetime-local" name="end_date"
           value="<?= date('Y-m-d\TH:i', strtotime($row['end_date'])) ?>" required>

    <label>Edit Window Start</label>
    <input type="datetime-local" name="edit_start"
           value="<?= !empty($row['edit_start']) ? date('Y-m-d\TH:i', strtotime($row['edit_start'])) : '' ?>">

    <label>Edit Window End</label>
    <input type="datetime-local" name="edit_end"
           value="<?= !empty($row['edit_end']) ? date('Y-m-d\TH:i', strtotime($row['edit_end'])) : '' ?>">

    <button type="submit">Update Dates</button>
  </form>

  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

</body>
</html>