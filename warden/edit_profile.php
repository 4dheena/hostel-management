<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* Fetch warden details */

$stmt = $conn->prepare("
SELECT 
wardens.full_name,
wardens.email,
wardens.phone,
wardens.join_date,
users.username,
users.profile_image
FROM wardens
JOIN users ON wardens.user_id = users.user_id
WHERE users.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$warden = $stmt->get_result()->fetch_assoc();

/* Profile image */

$defaultImage = "../assets/images/default_profile.jpg";
$uploadPath   = "../uploads/profile_pics/";

if (!empty($warden['profile_image']) &&
file_exists($uploadPath . $warden['profile_image'])) {
$profileImage = $uploadPath . $warden['profile_image'];
} else {
$profileImage = $defaultImage;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Profile</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>

.form-card{
background:#fff;
padding:25px;
border-radius:10px;
max-width:600px;
margin:auto;
}

.form-group{
margin-bottom:15px;
}

.form-group label{
font-weight:600;
display:block;
margin-bottom:6px;
}

.form-group input{
width:100%;
padding:10px;
border:1px solid #ccc;
border-radius:6px;
}

.profile-preview{
text-align:center;
margin-bottom:20px;
}

.profile-preview img{
width:140px;
height:140px;
border-radius:50%;
object-fit:cover;
border:4px solid #2c5364;
}

.save-btn{
background:#2c5364;
color:#fff;
padding:10px 20px;
border:none;
border-radius:6px;
cursor:pointer;
}

.remove-btn{
background:#dc3545;
color:#fff;
padding:8px 14px;
border:none;
border-radius:6px;
cursor:pointer;
}

</style>

</head>

<body>

<div class="main-content">

<h2>Edit Profile</h2>

<div class="form-card">

<form action="update_profile.php" method="POST" enctype="multipart/form-data">

<div class="profile-preview">
<img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Image">
</div>

<?php if(!empty($warden['profile_image'])): ?>

<div style="text-align:center;margin-bottom:15px;">

<button type="submit"
name="remove_image"
class="remove-btn"
onclick="return confirm('Remove your profile picture?')">

Remove Profile Image

</button>

</div>

<?php endif; ?>

<div class="form-group">
<label>Full Name</label>
<input type="text" value="<?= htmlspecialchars($warden['full_name']) ?>" readonly>
</div>

<div class="form-group">
<label>User ID</label>
<input type="text" value="<?= htmlspecialchars($warden['username']) ?>" readonly>
</div>

<div class="form-group">
<label>Joined Date</label>
<input type="text" value="<?= date('d M Y', strtotime($warden['join_date'])) ?>" readonly>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?= htmlspecialchars($warden['email']) ?>" required>
</div>

<div class="form-group">
<label>Phone</label>
<input type="text" name="phone" value="<?= htmlspecialchars($warden['phone']) ?>" required>
</div>

<div class="form-group">
<label>Change Profile Image</label>
<input type="file" name="profile_image" accept="image/*">
</div>

<button type="submit" class="save-btn">Save Changes</button>

</form>

</div>

</div>

</body>
</html>