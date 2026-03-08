<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* UPDATE ALL WARDEN ASSIGNMENTS */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hostel_id'])) {

    foreach ($_POST['hostel_id'] as $warden_id => $hostel_id) {

        $stmt = $conn->prepare("UPDATE wardens SET hostel_id=? WHERE warden_id=?");
        $stmt->bind_param("ii", $hostel_id, $warden_id);
        $stmt->execute();

    }

    $_SESSION['message'] = "Warden assignments updated successfully.";
    header("Location: manage_wardens.php");
    exit;
}

/* FETCH WARDENS */

$query = "
SELECT 
w.warden_id,
w.full_name,
w.gender,
w.phone,
w.email,
w.hostel_id,
h.hostel_name
FROM wardens w
LEFT JOIN hostels h ON w.hostel_id = h.hostel_id
";

$wardens = $conn->query($query);

/* FETCH HOSTELS */

$hostels = $conn->query("SELECT hostel_id, hostel_name FROM hostels");

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Wardens</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th,td{
border:1px solid #ccc;
padding:10px;
text-align:center;
}

select{
padding:6px 10px;
border-radius:8px;
border:1px solid #ccc;
background:#f9f9f9;
font-size:14px;
}

select:focus{
outline:none;
border-color:#2c5364;
box-shadow:0 0 5px rgba(44,83,100,0.4);
}

.update-btn{
margin-top:20px;
padding:10px 20px;
background:#2c5364;
color:white;
border:none;
border-radius:8px;
font-size:15px;
cursor:pointer;
}

.update-btn:hover{
background:#1f3c48;
}

.success{
background:#d4edda;
padding:10px;
margin-bottom:15px;
border-radius:6px;
}

</style>

</head>

<body>

<div class="dashboard-container">

<!-- SIDEBAR -->

<aside class="sidebar">

<div class="logo">
<img src="../assets/images/logo.jpeg">
<span>ARUVI</span>
</div>

<ul class="menu">

<li><a href="dashboard.php">Dashboard</a></li>

<li><a href="rank_list.php">Rank List</a></li>

<li><a href="analytics.php">Analytics</a></li>

<li><a href="application_approval.php">Application Approval</a></li>

<li class="active"><a href="manage_wardens.php">Manage Wardens</a></li>

<li><a href="staff_feedback.php">Staff Feedback</a></li>

<li><a href="announcements.php">Announcements</a></li>

<li><a href="notifications.php">Notifications</a></li>

<li><a href="fee_defaulters.php">Fee Defaulters</a></li>

<li><a href="complaints.php">Complaints</a></li>

<li><a href="../auth/logout.php">Logout</a></li>

</ul>

</aside>

<!-- MAIN CONTENT -->

<main class="main-content">

<h2>Manage Wardens</h2>

<p>Change hostel assignments and click Update All to save changes.</p>

<?php
if(isset($_SESSION['message'])){
echo "<div class='success'>".$_SESSION['message']."</div>";
unset($_SESSION['message']);
}
?>

<form method="POST">

<table>

<tr>
<th>Name</th>
<th>Gender</th>
<th>Phone</th>
<th>Email</th>
<th>Current Hostel</th>
<th>Assign Hostel</th>
</tr>

<?php while($row = $wardens->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row['full_name']); ?></td>

<td><?php echo htmlspecialchars($row['gender']); ?></td>

<td><?php echo htmlspecialchars($row['phone']); ?></td>

<td><?php echo htmlspecialchars($row['email']); ?></td>

<td><?php echo htmlspecialchars($row['hostel_name']); ?></td>

<td>

<select name="hostel_id[<?php echo $row['warden_id']; ?>]">

<?php
$hostels->data_seek(0);
while($h = $hostels->fetch_assoc()) {
?>

<option value="<?php echo $h['hostel_id']; ?>"
<?php if($row['hostel_id'] == $h['hostel_id']) echo "selected"; ?>>

<?php echo $h['hostel_name']; ?>

</option>

<?php } ?>

</select>

</td>

</tr>

<?php } ?>

</table>

<button class="update-btn" type="submit">Update All Assignments</button>

</form>

</main>

</div>

</body>

</html>