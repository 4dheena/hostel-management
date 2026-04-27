<?php
session_start();
include '../database/db_connect.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* 🔹 GET STUDENT */
$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id, hostel_id FROM students WHERE user_id='$user_id'
"));

if(!$student){
    header("Location: gbm.php");
    exit();
}

$student_id = $student['student_id'];
$hostel_id  = $student['hostel_id'];

/* 🔹 ACTION */
$action = $_POST['action'] ?? '';

/* ===================== VOTE ===================== */
if($action === "vote"){

    $poll_id   = $_POST['poll_id'] ?? '';
    $option_id = $_POST['option_id'] ?? '';

    if($poll_id && $option_id){

        /* CHECK ALREADY VOTED */
        $check = mysqli_query($conn,"
        SELECT id FROM gbm_votes 
        WHERE poll_id='$poll_id' AND student_id='$student_id'
        ");

        if(mysqli_num_rows($check) == 0){

            mysqli_query($conn,"
            INSERT INTO gbm_votes (poll_id, option_id, student_id)
            VALUES ('$poll_id','$option_id','$student_id')
            ");
        }
    }
}

/* ===================== SUGGESTION ===================== */
if($action === "suggest"){

    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');

    if($title !== '' && $desc !== ''){

    mysqli_query($conn,"
INSERT INTO gbm_suggestions (hostel_id, student_id, title, description)
VALUES ('$hostel_id','$student_id','$title','$desc')
"); 
    
    }
}

/* ===================== REACTION ===================== */
if($action === "react"){

    $suggestion_id = $_POST['suggestion_id'] ?? '';
    $reaction      = $_POST['reaction'] ?? '';

    if($suggestion_id && ($reaction === 'up' || $reaction === 'down')){

        /* CHECK EXISTING */
        $check = mysqli_query($conn,"
        SELECT id FROM gbm_reactions 
        WHERE suggestion_id='$suggestion_id' AND student_id='$student_id'
        ");

        if(mysqli_num_rows($check) > 0){

            mysqli_query($conn,"
            UPDATE gbm_reactions 
            SET reaction='$reaction'
            WHERE suggestion_id='$suggestion_id' AND student_id='$student_id'
            ");

        } else {

            mysqli_query($conn,"
            INSERT INTO gbm_reactions (suggestion_id, student_id, reaction)
            VALUES ('$suggestion_id','$student_id','$reaction')
            ");
        }
    }
}

/* 🔁 ALWAYS RETURN TO SAME PAGE */
header("Location: gbm.php");
exit();
?>