<?php
require_once '../database/db_connect.php';

$query = "
    SELECT
        application_id,
        full_name,
        student_id,
        department,
        distance_km,
        annual_income,
        pwd_status,
        disability_percentage,
        priority_score
    FROM hostel_applications
    WHERE submitted_at IS NOT NULL
    ORDER BY priority_score DESC, distance_km DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hostel Priority Rank List</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>

<h2>Hostel Priority Rank List</h2>

<table>
    <tr>
        <th>Rank</th>
        <th>Student ID</th>
        <th>Name</th>
        <th>Department</th>
        <th>Distance (km)</th>
        <th>Income</th>
        <th>PWD</th>
        <th>Score</th>
    </tr>

    <?php
    $rank = 1;
    while ($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?= $rank++ ?></td>
        <td><?= htmlspecialchars($row['student_id']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
        <td><?= $row['distance_km'] ?></td>
        <td><?= $row['annual_income'] ?></td>
        <td>
            <?= $row['pwd_status'] === 'Yes'
                ? 'Yes (' . $row['disability_percentage'] . '%)'
                : 'No'
            ?>
        </td>
        <td><?= $row['priority_score'] ?></td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
