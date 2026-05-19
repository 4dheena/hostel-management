<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
require_once "../database/db_connect.php";

/* check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* fetch student + user data */
$sql = "SELECT s.*, u.profile_image,r.room_number 
        FROM students s
        JOIN users u ON s.user_id = u.user_id
        LEFT JOIN rooms r ON s.room_id = r.room_id
        WHERE s.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

/* safe values */
$name = $student['name'] ?? "Student";
$student_id = $student['student_id'] ?? "-";
$email = $student['email'] ?? "-";
$phone = $student['phone'] ?? "-";
$hostel = $student['hostel_id'] ?? "-";

/* room logic */
if (!empty($student['room_number'])) {
    $room = $student['room_number'];
} else {
    $room = "Not Allotted";
}

/* profile image logic */
$defaultImage = "../assets/images/default_profile.jpg";
$uploadPath = "../uploads/profile_pics/";

if (!empty($student['profile_image']) && file_exists($uploadPath . $student['profile_image'])) {
    $profileImage = $uploadPath . $student['profile_image'];
} else {
    $profileImage = $defaultImage;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI;
}

body{
background:#f4f7fb;
}

/* Layout */

.dashboard{
display:flex;
min-height:100vh;
}

/* Sidebar */

.sidebar{
width:240px;
background:linear-gradient(180deg,#0f2027,#203a43,#2c5364);
color:white;
padding:25px;
}

.sidebar h2{
text-align:center;
margin-bottom:35px;
}

.sidebar a{
display:block;
color:white;
text-decoration:none;
padding:12px;
border-radius:8px;
margin-bottom:6px;
}

.sidebar a:hover{
background:rgba(255,255,255,0.15);
}

/* Content */

.content{
flex:1;
padding:30px;
}

/* Profile Card */

.profile-card{
background:white;
border-radius:14px;
padding:30px;
display:flex;
gap:30px;
align-items:center;
margin-bottom:30px;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
}

.profile-card img{
width:180px;
height:180px;
border-radius:50%;
object-fit:cover;
border:5px solid #2c5364;
}

.profile-info h2{
margin-bottom:10px;
}

.profile-info p{
margin-bottom:6px;
color:#555;
}

.buttons{
margin-top:12px;
}

.btn{
display:inline-block;
padding:8px 14px;
margin-right:10px;
background:#2c5364;
color:white;
text-decoration:none;
border-radius:6px;
font-size:14px;
}

.btn.secondary{
background:#6c757d;
}

/* Cards */

.cards{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
margin-bottom:20px;
}

.card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 8px 18px rgba(0,0,0,0.08);
min-height:140px;
overflow:hidden;
}

.event-list{
padding-left:18px;
line-height:1.6;
}

.card canvas{
max-height:180px;
}

.card h3{
margin-bottom:10px;
}

/* Buttons */

.actions{
display:flex;
gap:10px;
}

.actions button{
padding:8px 14px;
border:none;
border-radius:20px;
background:#1e5aa8;
color:white;
cursor:pointer;
}

/* Announcements */

.announcements{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 8px 18px rgba(0,0,0,0.08);
}

.modal {
    display: none;
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    width: 320px;
    margin: 8% auto;
    padding: 20px;
    border-radius: 10px;
}

.modal-content input,
.modal-content textarea {
    width: 100%;
    margin-bottom: 10px;
    padding: 8px;
}



</style>

</head>

<body>

<div class="dashboard">

<!-- Sidebar -->

<div class="sidebar">

<h2>ARUVI</h2>

<a href="dashboard.php">Dashboard</a>
<a href="attendance.php">Attendance</a>
<a href="gbm.php">GBM</a>
<a href="chat.php">Community Chat</a>
<a href="complaints.php">Complaints</a>
<a href="feedback.php">Feedback</a>
<a href="announcements.php">Announcements</a>
<a href="notifications.php">Notifications</a>
<a href="../auth/logout.php">Logout</a>

</div>

<!-- Content -->

<div class="content">

<!-- Profile -->

<div class="profile-card">

<img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Image">

<div class="profile-info">

<h2><?= htmlspecialchars($name) ?></h2>

<p><b>Student ID:</b> <?= htmlspecialchars($student_id) ?></p>
<p><b>Hostel:</b> <?= htmlspecialchars($hostel) ?></p>
<p><b>Room No:</b> <?= htmlspecialchars($room) ?></p>
<p><b>Mobile:</b> <?= htmlspecialchars($phone) ?></p>
<p><b>Email:</b> <?= htmlspecialchars($email) ?></p>

<div class="buttons">

<a href="edit_profile.php" class="btn">Edit Profile</a>
<a href="/hostel-management/auth/change_password.php" class="btn secondary">Change Password</a>

</div>

</div>

</div>

<!-- Cards -->

<div class="cards">

<div class="card">

<h3>Hostel Actions</h3>

<div class="actions">
<a href="view_allotment.php" class="btn">View Allotment</a>
<a href="#" onclick="openModal()" class="btn">Stay Permission</a>
</div>

</div>

<div class="card">

<h3>Upcoming Events</h3>
<p>No events available</p>

</div>

<div class="card">

<h3>Attendance Summary</h3>
<p>-- %</p>

</div>

</div>


</div>

</div>
<div id="permissionModal" class="modal">
    <div class="modal-content">

        <h3>Stay Permission</h3>

        <form method="POST" enctype="multipart/form-data" autocomplete="off" action="submit_permission.php">

            <input type="text" name="guest_name" placeholder="Guest Name" required>

            <input type="text" name="relation" placeholder="Relation" required>

            <label>From Date</label>
            <input type="date" name="from_date" required>

            <label>To Date</label>
            <input type="date" name="to_date" required>

            <textarea name="reason" placeholder="Reason" required></textarea>

            <label>ID Proof</label>
            <input type="file" name="id_proof" required>

            <div style="margin-top:10px;">
                <button type="submit">Submit</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </div>

        </form>

    </div>
</div>
<script>
function openModal(){
    document.getElementById("permissionModal").style.display = "block";
}

function closeModal(){
    document.getElementById("permissionModal").style.display = "none";
}
</script>
</body>
</html>