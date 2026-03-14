<?php

session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: ../index.php");
    exit;
}

$title = trim($_POST['title']);
$message = trim($_POST['message']);
$target = $_POST['target'];

$file_path = NULL;

/* File upload */

if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){

    $file_tmp  = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if($ext !== 'pdf'){
        die("Only PDF files allowed.");
    }

    $upload_dir = "../uploads/ranklists/";

    if(!is_dir($upload_dir)){
        mkdir($upload_dir,0777,true);
    }

    $new_name = uniqid().".pdf";

    move_uploaded_file($file_tmp,$upload_dir.$new_name);

    $file_path = "uploads/ranklists/".$new_name;
}

/* Insert announcement */

$created_by = "warden";

$stmt = $conn->prepare("
INSERT INTO announcements(title,message,target,file_path,created_by)
VALUES(?,?,?,?,?)
");

$stmt->bind_param("sssss",$title,$message,$target,$file_path,$created_by);
$stmt->execute();

    header("Location: write_announcement.php");
    exit;
?>  