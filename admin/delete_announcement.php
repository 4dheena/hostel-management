<?php

session_start();
require_once '../database/db_connect.php';

/* Only admin allowed */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* Check ID */

if (!isset($_GET['id'])) {
    header("Location: announcements.php");
    exit;
}

$id = (int) $_GET['id'];

/* Get file path first */

$stmt = $conn->prepare("SELECT file_path FROM announcements WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

/* Delete uploaded file if exists */

if ($row && !empty($row['file_path'])) {

    $file = "../".$row['file_path'];

    if (file_exists($file)) {
        unlink($file);
    }
}

/* Delete announcement */

$stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

/* Redirect back */

header("Location: announcements.php");
exit;

?>