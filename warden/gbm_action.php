<?php
session_start();
include '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

/* GET WARDEN */
$warden = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT hostel_id FROM wardens WHERE user_id='$user_id'
"));

$hostel_id = $warden['hostel_id'];

$action = $_POST['action'] ?? '';

/* ===== CREATE POLL ===== */
if($action === "create_poll"){

    $question = trim($_POST['question'] ?? '');
    $options  = $_POST['options'] ?? [];

    if($question !== '' && count($options) >= 2){

        mysqli_query($conn,"
        INSERT INTO gbm_polls (hostel_id, question, created_by)
        VALUES ('$hostel_id','$question','$user_id')
        ");

        $poll_id = mysqli_insert_id($conn);

        foreach($options as $opt){
            $opt = trim($opt);
            if($opt !== ''){
                mysqli_query($conn,"
                INSERT INTO gbm_poll_options (poll_id, option_text)
                VALUES ('$poll_id','$opt')
                ");
            }
        }
    }
}

/* 🔁 BACK */
header("Location: gbm.php");
exit();
?>