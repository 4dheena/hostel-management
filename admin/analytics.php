<?php
require_once '../database/db_connect.php';

$hostels_query = mysqli_query($conn, "SELECT * FROM hostels");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #f4f8fc;
            padding: 20px;
        }

        h1 {
            color: #1e3a8a;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            background: white;
            width: 320px;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #2563eb;
            margin-bottom: 5px;
        }

        .warden {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stats div {
            text-align: center;
            font-size: 14px;
        }

        canvas {
            max-height: 150px;
        }
    </style>
</head>

<body>

<h1>Analytics Overview</h1>

<div class="container">

<?php
$index = 1;

while($hostel = mysqli_fetch_assoc($hostels_query)){

    $hostel_id = $hostel['hostel_id'];

    // students
    $students_q = mysqli_query($conn,
        "SELECT COUNT(*) as total FROM students WHERE hostel_id = $hostel_id");
    $students = mysqli_fetch_assoc($students_q)['total'];

    // capacity
    $capacity = $hostel['capacity'];
    $vacant = $capacity - $students;

    // 👨‍💼 get BOTH wardens
    $warden_q = mysqli_query($conn,
        "SELECT full_name FROM wardens WHERE hostel_id = $hostel_id");

    $warden_names = [];

    while($w = mysqli_fetch_assoc($warden_q)){
        $warden_names[] = $w['full_name'];
    }

    $warden = !empty($warden_names) 
        ? implode(", ", $warden_names) 
        : "Not Assigned";
?>

    <div class="card">
        <h3><?php echo $hostel['hostel_name']; ?></h3>
        <div class="warden">Warden: <?php echo $warden; ?></div>

        <div class="stats">
            <div>👩‍🎓<br><?php echo $students; ?></div>
            <div>🛏️<br><?php echo $students; ?></div>
            <div>🚪<br><?php echo $vacant; ?></div>
        </div>

        <canvas id="chart<?php echo $index; ?>"></canvas>
    </div>

<?php
$index++;
}
?>

</div>

<script>
function createChart(id, occupied, vacant, colors) {
    new Chart(document.getElementById(id), {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                data: [occupied, vacant],
                backgroundColor: colors
            }]
        }
    });
}

const colors = [
    ['#22c55e', '#d1d5db'],
    ['#f97316', '#a855f7'],
    ['#14b8a6', '#facc15'],
    ['#ef4444', '#f472b6'],
    ['#3b82f6', '#06b6d4']
];

<?php
mysqli_data_seek($hostels_query, 0);
$index = 1;

while($hostel = mysqli_fetch_assoc($hostels_query)){

    $hostel_id = $hostel['hostel_id'];

    $students_q = mysqli_query($conn,
        "SELECT COUNT(*) as total FROM students WHERE hostel_id = $hostel_id");
    $students = mysqli_fetch_assoc($students_q)['total'];

    $capacity = $hostel['capacity'];
    $vacant = $capacity - $students;
?>

createChart(
    "chart<?php echo $index; ?>",
    <?php echo $students; ?>,
    <?php echo $vacant; ?>,
    colors[<?php echo ($index-1) % 5; ?>]
);

<?php
$index++;
}
?>
</script>

</body>
</html>