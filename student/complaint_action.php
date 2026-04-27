<?php
session_start();
include '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id, hostel_id FROM students WHERE user_id='$user_id'
"));

$student_id = $student['student_id'];
$hostel_id = $student['hostel_id'];

$action = $_POST['action'] ?? '';

if($action == "submit"){

$title = $_POST['title'];
$message = $_POST['message'];
$receivers = $_POST['receivers'] ?? [];

$type = $_POST['type'] ?? 'normal';
$anonymous = ($type == "anonymous") ? 1 : 0;

mysqli_query($conn,"
INSERT INTO complaints (student_id, hostel_id, title, message, is_anonymous)
VALUES ('$student_id','$hostel_id','$title','$message','$anonymous')
");

$complaint_id = mysqli_insert_id($conn);

foreach($receivers as $r){

if($r == "admin"){
mysqli_query($conn,"INSERT INTO complaint_receivers (complaint_id, receiver_role) VALUES ('$complaint_id','admin')");
}else{
mysqli_query($conn,"INSERT INTO complaint_receivers (complaint_id, receiver_id, receiver_role) VALUES ('$complaint_id','$r','warden')");
}
}

}

if($action == "delete"){

$complaint_id = $_POST['complaint_id'];

mysqli_query($conn,"
DELETE FROM complaints WHERE id='$complaint_id' AND student_id='$student_id'
");

mysqli_query($conn,"
DELETE FROM complaint_receivers WHERE complaint_id='$complaint_id'
");

}

header("Location: complaints.php");
exit();