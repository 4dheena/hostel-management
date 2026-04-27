<?php
session_start();
include '../database/db_connect.php';
require_once '../utils/send_email.php';

if(!isset($_GET['id']) || !isset($_GET['action'])){
    exit("Invalid request");
}

$id = $_GET['id'];
$action = $_GET['action'];
$reason = $_GET['reason'] ?? '';

/* 🔹 GET REQUEST */
$req = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM room_swap_requests WHERE id='$id'
"));

if(!$req){
    exit("Request not found");
}

/* 🔹 GET STUDENTS (WITH EMAIL) */
$a = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM students 
WHERE student_id='{$req['student_a_id']}'
"));

$b = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM students 
WHERE student_id='{$req['student_b_id']}'
"));

if(!$a || !$b){
    exit("Student data error");
}

/* ================= APPROVE ================= */
if($action == "approve"){

    /* UPDATE REQUEST STATUS */
    mysqli_query($conn,"
    UPDATE room_swap_requests 
    SET warden_status='approved', request_status='completed'
    WHERE id='$id'
    ");

    /* SWAP ROOMS */
    $roomA = $a['room_id'];
    $roomB = $b['room_id'];

    mysqli_query($conn,"
    UPDATE students SET room_id='$roomB' WHERE student_id='{$a['student_id']}'
    ");

    mysqli_query($conn,"
    UPDATE students SET room_id='$roomA' WHERE student_id='{$b['student_id']}'
    ");

    /* 🔔 NOTIFICATIONS */
    $msg_db = "Your room swap request has been approved.";

    mysqli_query($conn,"
    INSERT INTO notifications (user_id,hostel_id,title,message,type,reference_id,is_read,created_at)
    VALUES 
    ('{$a['user_id']}','{$req['hostel_id']}','Room Swap Approved','$msg_db','room_swap','$id',0,NOW()),
    ('{$b['user_id']}','{$req['hostel_id']}','Room Swap Approved','$msg_db','room_swap','$id',0,NOW())
    ");

    /* 📧 EMAIL */
    $subject = "Room Swap Approved";

    $mailBody = "
    <h2>Room Swap Approved ✅</h2>

    <p>Dear Student,</p>

    <p>Your room swap request has been <b>approved</b> by the warden.</p>

    <p>Your rooms have been successfully updated in the system.</p>

    <p>Please check your new room allocation.</p>

    <br>
    <p>Regards,<br>Hostel Management</p>
    ";

    sendEmail($a['email'], $subject, $mailBody);
    sendEmail($b['email'], $subject, $mailBody);

    header("Location: requests.php");
    exit();
}

/* ================= REJECT ================= */
if($action == "reject"){

    mysqli_query($conn,"
    UPDATE room_swap_requests 
    SET warden_status='rejected', rejection_reason='$reason'
    WHERE id='$id'
    ");

    /* 🔔 NOTIFICATIONS */
    $msg_db = "Room swap request rejected. Reason: $reason";

    mysqli_query($conn,"
    INSERT INTO notifications (user_id,hostel_id,title,message,type,reference_id,is_read,created_at)
    VALUES 
    ('{$a['user_id']}','{$req['hostel_id']}','Room Swap Rejected','$msg_db','room_swap','$id',0,NOW()),
    ('{$b['user_id']}','{$req['hostel_id']}','Room Swap Rejected','$msg_db','room_swap','$id',0,NOW())
    ");

    /* 📧 EMAIL */
    $subject = "Room Swap Rejected";

    $mailBody = "
    <h2>Room Swap Request Rejected ❌</h2>

    <p>Dear Student,</p>

    <p>Your room swap request has been reviewed by the warden.</p>

    <p><b>Status:</b> Rejected</p>

    <p><b>Reason:</b> $reason</p>

    <br>
    <p>If you have any concerns, please contact the hostel office.</p>

    <br>
    <p>Regards,<br>Hostel Management</p>
    ";

    sendEmail($a['email'], $subject, $mailBody);
    sendEmail($b['email'], $subject, $mailBody);

    header("Location: requests.php");
    exit();
}
?>