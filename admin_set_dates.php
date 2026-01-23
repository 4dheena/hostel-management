<?php
include 'database/db_connect.php';

$result = $conn->query("SELECT start_date, end_date FROM application_settings WHERE id=1");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['start_date'];
    $end   = $_POST['end_date'];

    $conn->query("
        UPDATE application_settings
        SET start_date='$start', end_date='$end'
        WHERE id=1
    ");

    header("Location: admin_set_dates.php");
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
      max-width: 400px;
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
  </style>
</head>
<body>

<div class="card">
  <h2>Application Window</h2>

  <form method="POST">
    <label>Start Date</label>
    <input type="date" name="start_date"
           value="<?= $row['start_date'] ?>" required>

    <label>End Date</label>
    <input type="date" name="end_date"
           value="<?= $row['end_date'] ?>" required>

    <button type="submit">Update Dates</button>
  </form>
</div>

</body>
</html>
