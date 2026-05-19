<?php
require_once '../database/db_connect.php';

$query = "
    SELECT
        ha.id,
        ha.full_name,
        ha.student_id,
        ha.department,
        ha.distance_km,
        ha.annual_income,
        ha.pwd_status,
        ha.disability_percentage,
        ha.priority_score,
        h.hostel_name
    FROM hostel_applications ha

    LEFT JOIN students s
        ON ha.student_id = s.student_id

    LEFT JOIN hostels h
        ON s.hostel_id = h.hostel_id

    WHERE ha.status = 'approved'

    ORDER BY ha.priority_score DESC,
             ha.distance_km DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Hostel Priority Rank List</title>

<style>

body{
font-family: Arial;
padding:40px;
}

table{
border-collapse: collapse;
width: 100%;
}

th, td{
border: 1px solid #ccc;
padding: 8px;
text-align: center;
}

th{
background: #f2f2f2;
}

button{
background:#28a745;
color:white;
border:none;
padding:10px 16px;
border-radius:5px;
cursor:pointer;
margin-bottom:20px;
}

</style>

</head>

<body>
<?php if(isset($_GET['published'])): ?>
<p style="color:green;font-weight:bold;">
Rank list published successfully.
</p>
<?php endif; ?>
<h2>Hostel Priority Rank List</h2>

<form action="publish_ranklist.php" method="POST">
<button type="submit">Publish Rank List</button>
</form>

<table>

<tr>
<th>Rank</th>
<th>Student ID</th>
<th>Name</th>
<th>Department</th>
<th>Distance (km)</th>
<th>Income</th>
<th>PWD</th>
<th>Hostel Name</th>
<th>Score</th>
</tr>

<?php
$rank = 1;

if($result->num_rows == 0){
echo "<tr><td colspan='8'>No approved students found.</td></tr>";
}

while ($row = $result->fetch_assoc()):
?>

<tr>

<td><?php echo $rank++; ?></td>

<td><?php echo htmlspecialchars($row['student_id']); ?></td>

<td><?php echo htmlspecialchars($row['full_name']); ?></td>

<td><?php echo htmlspecialchars($row['department']); ?></td>

<td><?php echo $row['distance_km']; ?></td>

<td><?php echo $row['annual_income']; ?></td>

<td>
<?php
if($row['pwd_status'] === 'Yes'){
echo "Yes (".$row['disability_percentage']."%)";
}else{
echo "No";
}
?>
</td>

<td><?php echo $row['hostel_name']; ?></td>
<td><?php echo $row['priority_score']; ?></td>


</tr>

<?php endwhile; ?>

</table>

</body>
</html>