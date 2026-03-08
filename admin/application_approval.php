<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* ================= FETCH CAPACITY INFO ================= */

$capacityQuery = $conn->query("
SELECT SUM(capacity) AS total_capacity 
FROM hostels
");

$total_capacity = $capacityQuery->fetch_assoc()['total_capacity'];

/* COUNT APPROVED STUDENTS */

$approvedQuery = $conn->query("
SELECT COUNT(*) AS approved_students
FROM hostel_applications
WHERE status='approved'
");

$approved_students = $approvedQuery->fetch_assoc()['approved_students'];

$remaining_seats = $total_capacity - $approved_students;


/* ================= FETCH APPLICATIONS ================= */

$query = "
SELECT *
FROM hostel_applications
WHERE submitted_at IS NOT NULL
ORDER BY priority_score DESC
";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html>
<head>

<title>Application Approval</title>

<style>

body{
    font-family: Arial;
    padding:40px;
}

h2{
    margin-bottom:20px;
}

/* Capacity Info Box */

.capacity-box{
background:#eef6ff;
border:1px solid #cfe2ff;
padding:15px;
border-radius:8px;
margin-bottom:20px;
font-weight:bold;
}

.approve-all-btn{
padding:10px 18px;
background:#28a745;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
margin-bottom:15px;
}

.approve-all-btn:hover{
background:#218838;
}

table{
width:100%;
border-collapse:collapse;
}

th, td{
border:1px solid #ccc;
padding:8px;
text-align:center;
}

th{
background:#f4f4f4;
}

.status-dropdown{
background-color:#28a745;
color:white;
padding:5px 8px;
border:none;
border-radius:6px;
font-weight:bold;
}

.status-dropdown option{
color:black;
}

.update-btn{
margin-top:20px;
background:#007bff;
color:white;
border:none;
padding:10px 16px;
border-radius:6px;
cursor:pointer;
font-size:14px;
}

.update-btn:hover{
background:#0056b3;
}

</style>

</head>

<body>

<h2>Application Approval</h2>

<!-- Capacity Information -->

<div class="capacity-box">

Total Capacity: <?= $total_capacity ?> |
Approved Students: <?= $approved_students ?> |
Remaining Seats: <?= $remaining_seats ?>

</div>

<?php
if($result->num_rows == 0){
    echo "<p>No applications found.</p>";
}
?>

<?php if(isset($_GET['updated'])): ?>
<p id="successMessage" style="color:green;font-weight:bold;">
Applications updated successfully.
</p>
<?php endif; ?>

<form method="POST" action="update_status.php"
onsubmit="return confirm('Are you sure you want to update these application statuses?');">

<button type="button" onclick="approveAll()" class="approve-all-btn">
Mark All Approved
</button>

<table>

<tr>
<th>Student ID</th>
<th>Name</th>
<th>Department</th>
<th>Priority Score</th>
<th>Income</th>
<th>Distance (km)</th>
<th>PWD</th>
<th>Certificates</th>
<th>Status</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($row['student_id']) ?></td>

<td><?= htmlspecialchars($row['full_name']) ?></td>

<td><?= htmlspecialchars($row['department']) ?></td>

<td><?= htmlspecialchars($row['priority_score']) ?></td>

<td><?= htmlspecialchars($row['annual_income']) ?></td>

<td><?= htmlspecialchars($row['distance_km']) ?></td>

<td><?= htmlspecialchars($row['pwd_status']) ?></td>

<td>

<?php
$hasFile = false;

if(!empty($row['income_certificate'])){
echo '<a href="../'.$row['income_certificate'].'" target="_blank">Income</a><br>';
$hasFile = true;
}

if(!empty($row['pwd_certificate'])){
echo '<a href="../'.$row['pwd_certificate'].'" target="_blank">PWD</a><br>';
$hasFile = true;
}

if(!empty($row['id_proof'])){
echo '<a href="../'.$row['id_proof'].'" target="_blank">ID</a>';
$hasFile = true;
}

if(!$hasFile){
echo "Not Uploaded";
}
?>

</td>

<td>

<select name="status[<?= $row['id'] ?>]" class="status-dropdown">

<option value="pending"
<?= $row['status']=='pending' ? 'selected' : '' ?>>
Pending
</option>

<option value="approved"
<?= $row['status']=='approved' ? 'selected' : '' ?>>
Approved
</option>

<option value="rejected"
<?= $row['status']=='rejected' ? 'selected' : '' ?>>
Rejected
</option>

</select>

</td>

</tr>

<?php endwhile; ?>

</table>

<button type="submit" class="update-btn">
Update All Applications
</button>

</form>

<script>

setTimeout(function(){

var msg = document.getElementById("successMessage");

if(msg){
msg.style.transition = "opacity 0.5s";
msg.style.opacity = "0";
setTimeout(() => msg.remove(), 500);
}

},3000);

</script>

<script>

function approveAll() {

let selects = document.querySelectorAll("select[name^='status']");

selects.forEach(function(select){
select.value = "approved";
});

}

</script>

</body>
</html>