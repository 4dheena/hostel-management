<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_POST['user_id'], $_POST['password'])) {
    die("Invalid request");
}

$user_id  = trim($_POST['user_id']);
$password = $_POST['password'];

$stmt = $conn->prepare(
    "SELECT user_id, password, role FROM users WHERE user_id = ?"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid User ID or Password");
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    die("Invalid User ID or Password");
}

/* ✅ LOGIN SUCCESS */
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role']    = $user['role'];

/* ROLE-BASED REDIRECT */
switch ($user['role']) {
    case 'admin':
        header("Location: admin/dashboard.php");
        break;

    case 'warden':
        header("Location: warden/dashboard.php");
        break;

    case 'student':
        header("Location: student/dashboard.php");
        break;

    default:
        die("Unauthorized role");
}
exit;
