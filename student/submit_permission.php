<?php
session_start();
require_once '../database/db_connect.php';

/* =========================
   1. CHECK SESSION
========================= */

if(!isset($_SESSION['user_id'])){
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];


/* =========================
   2. GET STUDENT ID
========================= */

$student_q = mysqli_query($conn, "
    SELECT student_id, hostel_id 
    FROM students 
    WHERE user_id = '$user_id'
");

if(!$student_q || mysqli_num_rows($student_q) == 0){
    die("Student not found");
}

$student = mysqli_fetch_assoc($student_q);

$student_id = $student['student_id'];   // VARCHAR ✔
$hostel_id = $student['hostel_id'];


/* =========================
   3. GET FORM DATA
========================= */

$guest_name = $_POST['guest_name'];
$relation   = $_POST['relation'];
$from_date  = $_POST['from_date'];
$to_date    = $_POST['to_date'];
$reason     = $_POST['reason'];


/* =========================
   4. INSERT STAY PERMISSION
========================= */

$insert = mysqli_query($conn, "
    INSERT INTO stay_permissions 
    (student_id, guest_name, relation, from_date, to_date, reason, status)
    VALUES 
    ('$student_id','$guest_name','$relation','$from_date','$to_date','$reason','pending')
");

if(!$insert){
    die("Insert Error: " . mysqli_error($conn));
}


/* =========================
   5. GET LAST INSERT ID
========================= */

$permission_id = mysqli_insert_id($conn);


/* =========================
   6. INSERT NOTIFICATIONS (FOR BOTH WARDENS)
========================= */

// get wardens of this hostel
$warden_q = mysqli_query($conn, "
    SELECT user_id FROM wardens WHERE hostel_id = '$hostel_id'
");

while($warden = mysqli_fetch_assoc($warden_q)){

    $warden_user_id = $warden['user_id'];

    $title = "Stay Permission";
    $message = "New stay request submitted";

    mysqli_query($conn, "
        INSERT INTO notifications 
        (user_id, hostel_id, title, message, type, reference_id, created_at)
        VALUES 
        ('$warden_user_id', '$hostel_id', '$title', '$message', 'guest_approval', '$permission_id', NOW())
    ");
}


/* =========================
   7. REDIRECT
========================= */

header("Location: dashboard.php");
exit;
?>