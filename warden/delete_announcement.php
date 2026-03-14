<?php

session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: ../index.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: writeAnnouncement.php");
    exit;
}

$id = (int) $_GET['id'];

/* get file */

$stmt = $conn->prepare("
SELECT file_path FROM announcements
WHERE id=? AND created_by='warden'
");

$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

if($result && !empty($result['file_path'])){

    $file = "../".$result['file_path'];

    if(file_exists($file)){
        unlink($file);
    }
}

/* delete */

$stmt = $conn->prepare("
DELETE FROM announcements
WHERE id=? AND created_by='warden'
");

$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: writeAnnouncement.php");
exit;

?>