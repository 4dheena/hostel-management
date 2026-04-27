<?php
session_start();
include '../database/db_connect.php';

/* 🔐 CHECK LOGIN */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* 🔹 ACTION */
$action = $_POST['action'] ?? '';

/* ================= UPDATE STATUS ================= */
if($action == "update_status"){

    $complaint_id = $_POST['complaint_id'] ?? '';
    $status = $_POST['status'] ?? '';

    /* 🔐 VALID STATUS ONLY */
    $allowed = ['not_read','reviewing','resolved'];

    if(!$complaint_id || !in_array($status, $allowed)){
        exit("Invalid request");
    }

    /* 🔐 OPTIONAL: CHECK IF THIS WARDEN HAS ACCESS */
    $check = mysqli_num_rows(mysqli_query($conn,"
        SELECT * FROM complaint_receivers 
        WHERE complaint_id='$complaint_id' 
        AND (receiver_id='$user_id' OR receiver_role='admin')
    "));

    if($check == 0){
        exit("Unauthorized");
    }

    /* 🔥 UPDATE */
    mysqli_query($conn,"
        UPDATE complaints 
        SET status='$status' 
        WHERE id='$complaint_id'
    ");
}

/* 🔹 REDIRECT BACK */
header("Location: complaints.php");
exit();
?>