<?php

require_once '../database/db_connect.php';

/* ---------- SAFETY CHECK ---------- */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['status']) || !is_array($_POST['status'])) {
    header("Location: application_approval.php");
    exit;
}

$valid_status = ['pending','approved','rejected'];

/* ---------- PROCESS EACH APPLICATION ---------- */

foreach ($_POST['status'] as $id => $status) {

    if(!in_array($status,$valid_status)){
        continue;
    }

    $id = (int)$id;

    /* UPDATE STATUS */

    $stmt = $conn->prepare("
        UPDATE hostel_applications
        SET status = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si",$status,$id);
    $stmt->execute();


    /* ---------- IF APPROVED → CREATE ACCOUNT ---------- */

    if($status === 'approved'){

        /* FETCH APPLICATION DATA */

        $stmt = $conn->prepare("
            SELECT student_id, full_name, personal_email, phone, password_hash
            FROM hostel_applications
            WHERE id = ?
        ");

        $stmt->bind_param("i",$id);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();

        if(!$app){
            continue;
        }

        $student_id = $app['student_id'];
        $name       = $app['full_name'];
        $email      = $app['personal_email'];
        $phone      = $app['phone'];
        $password   = $app['password_hash'];


        /* ---------- CHECK IF USER ALREADY EXISTS ---------- */

        $check = $conn->prepare("
            SELECT user_id
            FROM users
            WHERE username = ?
        ");

        $check->bind_param("s",$student_id);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if($existing){
            continue;   // skip if already created
        }


        /* ---------- CREATE USER ACCOUNT ---------- */

        $stmt = $conn->prepare("
            INSERT INTO users (username,password,role,profile_image)
            VALUES (?, ?, 'student', NULL)
        ");

        $stmt->bind_param("ss",$student_id,$password);
        $stmt->execute();

        $user_id = $conn->insert_id;


        /* ---------- INSERT INTO STUDENTS TABLE ---------- */

        $stmt = $conn->prepare("
            INSERT INTO students
            (student_id,user_id,name,email,phone,hostel_id,room_id)
            VALUES (?, ?, ?, ?, ?, NULL, NULL)
        ");

        $stmt->bind_param("sisss",
            $student_id,
            $user_id,
            $name,
            $email,
            $phone
        );

        $stmt->execute();

    }

}

/* ---------- REDIRECT BACK ---------- */

header("Location: application_approval.php?updated=1");
exit;

?>