<?php

session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$email = trim($_POST['email']);
$phone = trim($_POST['phone']);

/* Update email and phone */

$stmt = $conn->prepare("
UPDATE wardens
SET email=?, phone=?
WHERE user_id=?
");

$stmt->bind_param("ssi", $email, $phone, $user_id);
$stmt->execute();


/* Remove profile image */

if (isset($_POST['remove_image'])) {

    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!empty($result['profile_image'])) {

        $file = "../uploads/profile_pics/" . $result['profile_image'];

        if (file_exists($file)) {
            unlink($file);
        }
    }

    $stmt = $conn->prepare("
    UPDATE users SET profile_image=NULL WHERE user_id=?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}


/* Upload new profile image */

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {

    $tmp  = $_FILES['profile_image']['tmp_name'];
    $name = $_FILES['profile_image']['name'];

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','webp'];

    if (in_array($ext, $allowed)) {

        $upload_dir = "../uploads/profile_pics/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $new_name = uniqid() . "." . $ext;

        move_uploaded_file($tmp, $upload_dir . $new_name);

        $stmt = $conn->prepare("
        UPDATE users
        SET profile_image=?
        WHERE user_id=?
        ");

        $stmt->bind_param("si", $new_name, $user_id);
        $stmt->execute();
    }
}


/* Redirect to dashboard after update */

header("Location: dashboard.php");
exit;

?>