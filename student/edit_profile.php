<?php
session_start();
require_once "../database/db_connect.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= FETCH STUDENT DATA ================= */

$sql = "SELECT 
s.student_id,
s.name,
s.email,
s.phone,
s.hostel_id,
s.room_id,
u.profile_image,
h.hostel_name,
r.room_number

FROM students s
LEFT JOIN users u ON s.user_id = u.user_id
LEFT JOIN hostels h ON s.hostel_id = h.hostel_id
LEFT JOIN rooms r ON s.room_id = r.room_id
WHERE s.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$student_id = $data['student_id'];
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$hostel = $data['hostel_name'] ?? "";
$room = $data['room_number'] ?? "";
$profile = $data['profile_image'] ?? "";

/* ================= REMOVE IMAGE ================= */

if(isset($_POST['remove_image'])){

    if(!empty($profile)){
        $file = "../uploads/profile_pics/".$profile;

        if(file_exists($file)){
            unlink($file);
        }
    }

    $remove = $conn->prepare(
        "UPDATE users SET profile_image=NULL WHERE user_id=?"
    );
    $remove->bind_param("i",$user_id);
    $remove->execute();

    header("Location: edit_profile.php");
    exit();
}

/* ================= UPDATE PROFILE ================= */

if(isset($_POST['save_profile'])){

$email = $_POST['email'];
$phone = $_POST['phone'];

$update = $conn->prepare(
"UPDATE students SET email=?, phone=? WHERE user_id=?"
);

$update->bind_param("ssi",$email,$phone,$user_id);
$update->execute();

/* IMAGE UPLOAD */

if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){

$uploadDir = "../uploads/profile_pics/";
$fileName = time()."_".basename($_FILES['profile_image']['name']);
$target = $uploadDir.$fileName;

move_uploaded_file($_FILES['profile_image']['tmp_name'],$target);

$updateImg = $conn->prepare(
"UPDATE users SET profile_image=? WHERE user_id=?"
);

$updateImg->bind_param("si",$fileName,$user_id);
$updateImg->execute();
}

header("Location: edit_profile.php");
exit();

}

/* ================= PROFILE IMAGE ================= */

$defaultImage = "../assets/images/default_profile.jpg";

if(!empty($profile) && file_exists("../uploads/profile_pics/".$profile)){
$profileImage = "../uploads/profile_pics/".$profile;
}else{
$profileImage = $defaultImage;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>

<style>

body{
font-family:Segoe UI;
background:#f4f7fb;
padding:40px;
}

.container{
max-width:600px;
margin:auto;
background:white;
padding:30px;
border-radius:10px;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
}

h2{
margin-bottom:20px;
}

.profile-section{
text-align:center;
margin-bottom:25px;
}

.profile-img{
width:120px;
height:120px;
border-radius:50%;
object-fit:cover;
border:4px solid #2c5364;
}

.remove-btn{
margin-top:8px;
background:none;
border:none;
color:#e74c3c;
font-size:13px;
cursor:pointer;
}

.remove-btn:hover{
text-decoration:underline;
}

label{
font-weight:600;
}

input{
width:100%;
padding:10px;
margin-top:5px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:6px;
}

.readonly{
background:#f2f2f2;
}

.save-btn{
background:#2c5364;
color:white;
border:none;
padding:10px 20px;
border-radius:6px;
cursor:pointer;
}

.save-btn:hover{
background:#1f3f4d;
}

</style>

</head>

<body>

<div class="container">

<h2>Edit Profile</h2>

<form method="POST" enctype="multipart/form-data">

<div class="profile-section">

<img src="<?= $profileImage ?>" class="profile-img">

<?php if(!empty($profile)): ?>
<br>
<button type="submit"
name="remove_image"
class="remove-btn"
onclick="return confirm('Remove your profile picture?')">
Remove profile image
</button>
<?php endif; ?>

</div>

<label>Student ID</label>
<input type="text" value="<?= htmlspecialchars($student_id) ?>" class="readonly" readonly>

<label>Hostel</label>
<input type="text" value="<?= htmlspecialchars($hostel) ?>" class="readonly" readonly>

<label>Room</label>
<input type="text" value="<?= htmlspecialchars($room) ?>" class="readonly" readonly>

<label>Email</label>
<input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

<label>Mobile</label>
<input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>

<label>Upload New Profile Image</label>
<input type="file" name="profile_image">

<button type="submit" name="save_profile" class="save-btn">
Save Changes
</button>

</form>

</div>

</body>
</html>