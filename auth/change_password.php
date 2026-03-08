<?php
session_start();
require_once "../database/db_connect.php";

/* check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$error = "";

/* fetch current password */
$stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    /* verify current password */
    if (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    }

    /* confirm password match */
    elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    }

    /* password strength check */
    elseif (
        strlen($new_password) < 8 ||
        !preg_match('/[A-Z]/', $new_password) ||
        !preg_match('/[a-z]/', $new_password) ||
        !preg_match('/[0-9]/', $new_password) ||
        !preg_match('/[\W]/', $new_password)
    ) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    }

    else {

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update->bind_param("si", $hashed_password, $user_id);
        $update->execute();

        $message = "Password updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Change Password</title>

<style>

body{
font-family:Segoe UI;
background:#f4f7fb;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}

.container{
background:white;
padding:30px;
border-radius:10px;
width:400px;
box-shadow:0 8px 20px rgba(0,0,0,0.1);
}

h2{
margin-bottom:20px;
text-align:center;
}

input{
width:100%;
padding:10px;
margin-bottom:15px;
border-radius:6px;
border:1px solid #ccc;
}

button{
width:100%;
padding:10px;
background:#2c5364;
color:white;
border:none;
border-radius:6px;
cursor:pointer;
}

.success{
color:green;
margin-bottom:10px;
text-align:center;
}

.error{
color:red;
margin-bottom:10px;
text-align:center;
}

</style>

</head>

<body>

<div class="container">

<h2>Change Password</h2>

<?php if($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if($message): ?>
<div class="success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">

<input type="password" name="current_password" placeholder="Current Password" required>

<input type="password" name="new_password" placeholder="New Password" required>

<input type="password" name="confirm_password" placeholder="Confirm New Password" required>

<button type="submit">Update Password</button>

</form>

</div>

</body>
</html>