<?php
session_start();
include '../database/db_connect.php';

/* 🔹 GET DATA */
$student_id = $_POST['student_id'] ?? '';
$room_number = $_POST['room_number'] ?? '';
$vacate_date = $_POST['vacate_date'] ?? '';
$reason = $_POST['reason'] ?? '';

/* 🔹 VALIDATION */
if($student_id == '' || $room_number == '' || $vacate_date == '' || $reason == ''){
    $msg = "All fields are required";
    header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 FETCH STUDENT */
$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM students WHERE student_id='$student_id'
"));

if(!$student){
    $msg = "Invalid student ID";
    header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 FETCH ROOM */
$room = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM rooms WHERE room_number='$room_number'
"));

if(!$room){
    $msg = "Invalid room number";
    header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 CHECK MATCH */
if($student['room_id'] != $room['room_id']){
    $msg = "Student does not belong to this room";
    header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 CHECK ACTIVE REQUEST */
$check = mysqli_query($conn,"
SELECT * FROM vacate_requests 
WHERE student_id='$student_id' 
AND request_status='active'
");

if(mysqli_num_rows($check) > 0){
    $msg = "You already have an active vacate request";
    header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 INSERT */
$insert = mysqli_query($conn,"
INSERT INTO vacate_requests
(student_id, user_id, hostel_id, room_id, vacate_date, reason, warden_status, request_status, created_at)
VALUES (
'$student_id',
'{$student['user_id']}',
'{$student['hostel_id']}',
'{$student['room_id']}',
'$vacate_date',
'$reason',
'pending',
'pending_confirmation',
NOW()
)
");

if(!$insert){
    $msg = "Error submitting request";
    header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
    exit();
}

/* 🔔 NOTIFICATION (student confirmation) */
$request_id = mysqli_insert_id($conn);

$title = "Vacate Request";
$message = "A vacate request was submitted. Confirm if this was you.";

mysqli_query($conn,"
INSERT INTO notifications (user_id, hostel_id, title, message, type, reference_id, is_read, created_at)
VALUES (
'{$student['user_id']}',
'{$student['hostel_id']}',
'$title',
'$message',
'vacate_request',
'$request_id',
0,
NOW()
)
");

/* ✅ SUCCESS */
$msg = "Vacate request submitted successfully";
header("Location: ../forms/vacate.php?msg=" . urlencode($msg));
exit();
?>