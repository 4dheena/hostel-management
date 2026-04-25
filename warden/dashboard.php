<?php

session_start();
date_default_timezone_set('Asia/Kolkata');
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];



/* FETCH WARDEN DETAILS */

$stmt = $conn->prepare("
SELECT 
wardens.full_name,
wardens.email,
wardens.phone,
wardens.join_date,
users.username,
users.profile_image
FROM users
JOIN wardens ON users.user_id = wardens.user_id
WHERE users.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* PROFILE IMAGE */

$defaultImage = "../assets/images/default_profile.jpg";
$uploadPath   = "../uploads/profile_pics/";

if (!empty($user['profile_image']) &&
file_exists($uploadPath . $user['profile_image'])) {
$profileImage = $uploadPath . $user['profile_image'];
} else {
$profileImage = $defaultImage;
}

/* ANALYTICS */

// get hostel_id

$warden_q = $conn->query("
    SELECT hostel_id FROM wardens WHERE user_id = $user_id
");

$warden = $warden_q->fetch_assoc();
$hostel_id = $warden['hostel_id'];


// get hostel details
$hostel_q = $conn->query("
    SELECT capacity, room_sharing 
    FROM hostels 
    WHERE hostel_id = $hostel_id
");

$hostel = $hostel_q->fetch_assoc();

$capacity = $hostel['capacity'];
$sharing = $hostel['room_sharing'];


// total students
$totalStudents = $conn->query("
    SELECT COUNT(*) AS total 
    FROM students 
    WHERE hostel_id = $hostel_id
")->fetch_assoc()['total'];


// total rooms (derived)
$totalRooms = ceil($capacity / $sharing);

// occupied rooms
$occupiedRooms = ceil($totalStudents / $sharing);

// vacant rooms
$vacantRooms = $totalRooms - $occupiedRooms;


// safety
$vacantRooms = max(0, $vacantRooms);
/* GENERAL ANNOUNCEMENTS */

$announcements = $conn->query("
SELECT title, message, created_at
FROM announcements
WHERE target='all'
ORDER BY created_at DESC
LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>Warden Dashboard | ARUVI</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

.quick-actions{
margin-top:25px;
background:#fff;
padding:30px;
border-radius:8px;
}

.action-buttons{
display:flex;
gap:15px;
flex-wrap:wrap;
padding: 20px;
}

.action-buttons a{
background:#1aa6a6;
color:#fff;
padding:10px 16px;
text-decoration:none;
border-radius:6px;
font-weight:bold;
}

.analytics-card{
margin-top:30px;
background:#fff;
padding:20px;
border-radius:8px;
display:flex;
gap:40px;
align-items:center;
}

.analytics-stats p{
font-size:18px;
font-weight:bold;
margin-bottom:10px;
}

.announcement-box{
margin-top:30px;
background:#fff;
padding:20px;
border-radius:8px;
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

<li class="active"><a href="dashboard.php">Dashboard</a></li>
<li><a href="complaints.php">Complaints</a></li>
<li><a href="staff_feedback.php">Staff Feedback</a></li>
<li><a href="notifications.php">Notifications</a></li>
<li><a href="fee_defaulters.php">Fee Defaulters</a></li>
<li><a href="attendance.php">Attendance Tracker</a></li>
<li><a href="gbm.php">GBM</a></li>
<li><a href="../auth/logout.php">Logout</a></li>

</ul>

</aside>

<!-- MAIN CONTENT -->

<main class="main-content">

<div class="top-bar">
<span>Warden Dashboard</span>
<span><?= date("l, d M Y | h:i A") ?></span>
</div>


<!-- PROFILE CARD -->

<section class="profile-card">

<img src="<?= htmlspecialchars($profileImage) ?>">

<div class="profile-info">

<h2><?= htmlspecialchars($user['full_name']) ?></h2>

<p><strong>Role:</strong> Warden</p>

<p><strong>User ID:</strong> <?= htmlspecialchars($user['username']) ?></p>

<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

<p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>

<p><strong>Joined Date:</strong> <?= date("d M Y", strtotime($user['join_date'])) ?></p>

<div class="profile-actions">
<a href="edit_profile.php" class="btn">Edit Profile</a>
<a href="/hostel-management/auth/change_password.php" class="btn secondary">Change Password</a>
</div>

</div>

</section>


<!-- QUICK ACTIONS -->

<section class="quick-actions">

<h3>Quick Actions</h3>

<div class="action-buttons">

<a href="requests.php">Requests</a>

<a href="write_announcement.php">Write Announcement</a>


</div>

</section>


<!-- ANALYTICS -->

<section class="analytics-card">

<div class="analytics-stats">

<p>Total Students: <?= $totalStudents ?></p>

<p>Occupied Rooms: <?= $occupiedRooms ?></p>

<p>Vacant Rooms: <?= $vacantRooms ?></p>

</div>

<div style="width:250px;">

<canvas id="occupancyChart"></canvas>

</div>

</section>


<!-- ANNOUNCEMENTS -->

<section class="announcement-box">

<h3>General Announcements</h3>

<ul>

<?php while($row = $announcements->fetch_assoc()): ?>

<li>
<strong><?= htmlspecialchars($row['title']) ?></strong><br>
<?= htmlspecialchars($row['message']) ?>
</li>

<?php endwhile; ?>

</ul>

</section>

</main>
</div>


<script>

const ctx = document.getElementById('occupancyChart');

new Chart(ctx, {
type: 'pie',
data: {
labels: ['Occupied Rooms','Vacant Rooms'],
datasets: [{
data: [<?= $occupiedRooms ?>, <?= $vacantRooms ?>],
backgroundColor: [
'#c29117',
'#5eb1c2'
]
}]
},
options:{
plugins:{
legend:{
position:'bottom'
}
}
}
});

</script>

</body>
</html>