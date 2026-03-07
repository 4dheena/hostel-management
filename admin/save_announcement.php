<?php

require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: announcements.php");
    exit;
}

$title = trim($_POST['title']);
$message = trim($_POST['message']);

$file_path = NULL;

/* Handle file upload */

if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){

    $file_tmp  = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if($ext !== 'pdf'){
        die("Only PDF files are allowed.");
    }

    $upload_dir = "../uploads/announcements/";

    if(!is_dir($upload_dir)){
        mkdir($upload_dir,0777,true);
    }

    $new_name = uniqid().".pdf";

    $destination = $upload_dir.$new_name;

    move_uploaded_file($file_tmp,$destination);

    $file_path = "uploads/announcements/".$new_name;
}

/* Insert announcement */

$stmt = $conn->prepare("
INSERT INTO announcements(title,message,file_path)
VALUES(?,?,?)
");

$stmt->bind_param("sss",$title,$message,$file_path);
$stmt->execute();

header("Location: announcements.php");
exit;

?>