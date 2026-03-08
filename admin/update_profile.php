<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$email = $_POST['email'];
$phone = $_POST['phone'];

/* Update email and phone in admins table */
$stmt = $conn->prepare("
    UPDATE admins 
    SET email = ?, phone = ?
    WHERE user_id = ?
");
$stmt->bind_param("ssi", $email, $phone, $user_id);
$stmt->execute();

/* ================= PROFILE IMAGE ================= */

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {

    $uploadDir = "../uploads/profile_pics/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
    $targetFile = $uploadDir . $fileName;

    move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);

    $stmt = $conn->prepare("
        UPDATE users 
        SET profile_image = ?
        WHERE user_id = ?
    ");
    $stmt->bind_param("si", $fileName, $user_id);
    $stmt->execute();
}

/* Success message */
$_SESSION['message'] = "Profile updated successfully.";

header("Location: dashboard.php");
exit;
?>