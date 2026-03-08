<?php

session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* COUNT APPROVED STUDENTS */

$approvedQuery = $conn->query("
SELECT COUNT(*) AS approved
FROM hostel_applications
WHERE status = 'approved'
");

$approved = $approvedQuery->fetch_assoc()['approved'];


/* COUNT ALLOTTED STUDENTS */

$allottedQuery = $conn->query("
SELECT COUNT(*) AS allotted
FROM students
WHERE hostel_id IS NOT NULL
");

$allotted = $allottedQuery->fetch_assoc()['allotted'];
/* Fetch admin details */
$stmt = $conn->prepare("
    SELECT 
        admins.full_name,
        admins.email,
        admins.phone,
        admins.join_date,
        users.username,
        users.profile_image
    FROM users
    JOIN admins ON users.user_id = admins.user_id
    WHERE users.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Profile image logic */
$defaultImage = "../assets/images/default_profile.jpg";
$uploadPath   = "../uploads/profile_pics/";

if (!empty($user['profile_image']) &&
    file_exists($uploadPath . $user['profile_image'])) {
    $profileImage = $uploadPath . $user['profile_image'];
} else {
    $profileImage = $defaultImage;
}

/* ================= LIVE STATS ================= */

$totalApplications = $conn->query("
    SELECT COUNT(*) as total 
    FROM hostel_applications 
    WHERE submitted_at IS NOT NULL
")->fetch_assoc()['total'];

$rankStatus = $conn->query("
    SELECT COUNT(*) as calculated
    FROM hostel_applications
    WHERE priority_score IS NOT NULL
")->fetch_assoc()['calculated'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | ARUVI</title>
<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>

.admin-actions{
    margin-top:30px;
    display:flex;
    gap:20px;
}

.admin-actions a{
    padding:12px 18px;
    background:#1aa6a6;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
}

.success-message{
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
}

.profile-info p{
    margin-bottom:6px;
}

</style>

</head>

<body>

<div class="dashboard-container">

<!-- Sidebar -->
<aside class="sidebar">

<div class="logo">
<img src="../assets/images/logo.jpeg" alt="ARUVI Logo">
<span>ARUVI</span>
</div>

<ul class="menu">

<li class="active"><a href="dashboard.php">Dashboard</a></li>
<li><a href="rank_list.php">Rank List</a></li>
<li><a href="analytics.php">Analytics</a></li>
<li><a href="application_approval.php">Application Approval</a></li>
<li><a href="manage_warden.php">Manage Warden</a></li>
<li><a href="staff_feedback.php">Staff Feedback</a></li>
<li><a href="announcements.php">Announcements</a></li>
<li><a href="notifications.php">Notifications</a></li>
<li><a href="fee_defaulters.php">Fee Defaulters</a></li>
<li><a href="complaints.php">Complaints</a></li>
<li><a href="../auth/logout.php">Logout</a></li>

</ul>
</aside>

<!-- Main Content -->
<main class="main-content">

<!-- Top Bar -->
<div class="top-bar">
<span>Administrator Dashboard</span>
<span><?= date("l, d M Y | h:i A") ?></span>
</div>

<!-- SUCCESS MESSAGE -->
<?php if (isset($_SESSION['message'])): ?>
<div id="flash-message" class="success-message">
<?= $_SESSION['message']; ?>
</div>
<?php unset($_SESSION['message']); ?>
<?php endif; ?>

<!-- Profile Card -->
<section class="profile-card">

<img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Image">

<div class="profile-info">

<h2><?= htmlspecialchars($user['full_name']) ?></h2>

<p><strong>Role:</strong> Administrator</p>

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

<!-- Quick Stats -->
<section class="stats">

<div class="stat-box">
<h3>Total Applications</h3>
<p><?= $totalApplications ?></p>
</div>

<div class="stat-box">
<h3>Priority Calculated</h3>
<p><?= $rankStatus > 0 ? "Yes" : "No" ?></p>
</div>

<div class="stat-box">
<h3>Allotted Students</h3>
<p>—</p>
</div>

</section>

<!-- ADMIN CONTROL BUTTONS -->
<section class="admin-actions">

<a href="admin_set_dates.php">Set Application Dates</a>

<a href="calculate_priority.php">Calculate Priority</a>

<?php if($approved != $allotted): ?>

<a href="run_allotment.php" class="btn">
Run Allotment
</a>

<?php else: ?>

<div style="background:#fff3cd;
color:#856404;
padding:10px;
border-radius:6px;
font-weight:bold;
margin-top:10px;">
Allotment already completed. Please reset allotment to run again.
</div>

<?php endif; ?>

</section>

</main>
</div>

<script>

setTimeout(function() {
    const msg = document.getElementById('flash-message');
    if (msg) {
        msg.style.display = 'none';
    }
}, 3000);

</script>

</body>
</html>