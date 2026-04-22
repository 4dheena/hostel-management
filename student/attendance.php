<?php
session_start();
require_once "../database/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* 🔹 HARDCODED START DATE (warden will control later) */
$start_date = "2026-04-01";

/* 🔹 Total days from start_date → today */
$start = new DateTime($start_date);
$today = new DateTime();
$total_days = $start->diff($today)->days + 1;

/* 🔹 Total leave days */
$sql = "SELECT SUM(DATEDIFF(leave_date, leave_date) + 1) AS leave_days
        FROM leave_logs
        WHERE student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

$leave_days = $data['leave_days'] ?? 0;

/* 🔹 Attendance */
$present_days = $total_days - $leave_days;
$attendance_percentage = $total_days > 0 ? ($present_days / $total_days) * 100 : 0;

/* 🔹 Monthly data (for chart) */
$monthly_data = [];

for ($i = 1; $i <= 12; $i++) {
    $monthly_data[$i] = 0;
}

$sql2 = "SELECT MONTH(leave_date) as month, COUNT(*) as total
         FROM leave_logs
         WHERE student_id = ?
         GROUP BY MONTH(leave_date)";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$res2 = $stmt2->get_result();

while ($row = $res2->fetch_assoc()) {
    $monthly_data[$row['month']] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial; padding: 20px; }

        .summary {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            width: 250px;
            margin-bottom: 20px;
        }

        .summary h1 { margin: 0; color: green; }

        canvas {
            max-width: 600px;
            margin-top: 20px;
        }

        form {
            margin-top: 30px;
        }

        input, select, textarea {
            display: block;
            margin-bottom: 10px;
            padding: 8px;
            width: 250px;
        }
    </style>
</head>
<body>

<h2>Attendance</h2>

<!-- 🔹 Summary -->
<div class="summary">
    <p>Overall Attendance</p>
    <h1><?= round($attendance_percentage) ?>%</h1>
    <p>Present: <?= $present_days ?></p>
    <p>Absent: <?= $leave_days ?></p>
</div>

<!-- 🔹 Chart -->
<canvas id="attendanceChart"></canvas>

<script>
const ctx = document.getElementById('attendanceChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            label: 'Absent Days',
            data: <?= json_encode(array_values($monthly_data)) ?>
        }]
    }
});
</script>

<!-- 🔹 Add Leave -->
<h3>Add Leave</h3>

<form action="submit_leave.php" method="POST">
    <input type="date" name="leave_date" required>

    <select name="leave_type" required>
        <option value="night_out">Night Out</option>
        <option value="vacation">Vacation</option>
    </select>

    <textarea name="reason" placeholder="Reason" required></textarea>

    <button type="submit">Submit</button>
</form>

</body>
</html>