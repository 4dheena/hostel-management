<?php

require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

/* ================= GET TOTAL CAPACITY ================= */

$capacityQuery = $conn->query("
    SELECT SUM(capacity) AS total_capacity
    FROM hostels
");

$total_capacity = $capacityQuery->fetch_assoc()['total_capacity'];


/* ================= COUNT CURRENT APPROVED ================= */

$currentApprovedQuery = $conn->query("
    SELECT COUNT(*) AS approved_count
    FROM hostel_applications
    WHERE status='approved'
");

$currentApproved = $currentApprovedQuery->fetch_assoc()['approved_count'];


/* ================= COUNT NEW APPROVALS ================= */

$newApproved = 0;

foreach ($_POST['status'] as $app_id => $status) {
    if ($status === 'approved') {
        $newApproved++;
    }
}


/* ================= CAPACITY CHECK ================= */

if (($currentApproved + $newApproved) > $total_capacity) {

    die("Error: Approved students exceed hostel capacity ($total_capacity). Please reduce approvals.");

}


/* ================= PREPARE STATUS UPDATE ================= */

$stmt = $conn->prepare("
    UPDATE hostel_applications
    SET status = ?
    WHERE id = ?
");


/* ================= UPDATE DATABASE ================= */

foreach ($_POST['status'] as $app_id => $status) {

    /* UPDATE APPLICATION STATUS */

    $stmt->bind_param("si", $status, $app_id);
    $stmt->execute();


    /* ================= HANDLE REJECTED STUDENTS ================= */

    if ($status === 'rejected') {

        $studentQuery = $conn->prepare("
            SELECT student_id
            FROM hostel_applications
            WHERE id = ?
        ");

        $studentQuery->bind_param("i", $app_id);
        $studentQuery->execute();

        $student = $studentQuery->get_result()->fetch_assoc();

        if ($student) {

            $student_id = $student['student_id'];

            $resetStmt = $conn->prepare("
                UPDATE students
                SET hostel_id = NULL,
                    room_id = NULL
                WHERE student_id = ?
            ");

            $resetStmt->bind_param("s", $student_id);
            $resetStmt->execute();
        }
    }


    /* ================= STAGE 1 : MOVE APPROVED STUDENT ================= */

    if ($status === 'approved') {

        /* GET APPLICATION DATA */

        $appQuery = $conn->prepare("
            SELECT student_id, full_name, personal_email, phone, password_hash
            FROM hostel_applications
            WHERE id = ?
        ");

        $appQuery->bind_param("i", $app_id);
        $appQuery->execute();

        $appData = $appQuery->get_result()->fetch_assoc();

        $student_id = $appData['student_id'];
        $name = $appData['full_name'];
        $email = $appData['personal_email'];
        $phone = $appData['phone'];
        $password_hash = $appData['password_hash'];


        /* CHECK IF USER ALREADY EXISTS */

        $checkUser = $conn->prepare("
            SELECT user_id
            FROM users
            WHERE username = ?
        ");

        $checkUser->bind_param("s", $student_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();


        if ($userResult->num_rows == 0) {

            /* INSERT INTO USERS */

            $insertUser = $conn->prepare("
                INSERT INTO users (username, password, role)
                VALUES (?, ?, 'student')
            ");

            $insertUser->bind_param("ss", $student_id, $password_hash);
            $insertUser->execute();

            $user_id = $insertUser->insert_id;

        } else {

            $userRow = $userResult->fetch_assoc();
            $user_id = $userRow['user_id'];

        }


        /* CHECK IF STUDENT ALREADY EXISTS */

        $checkStudent = $conn->prepare("
            SELECT id
            FROM students
            WHERE student_id = ?
        ");

        $checkStudent->bind_param("s", $student_id);
        $checkStudent->execute();

        $studentResult = $checkStudent->get_result();


        if ($studentResult->num_rows == 0) {

            /* INSERT INTO STUDENTS */

            $insertStudent = $conn->prepare("
                INSERT INTO students
                (student_id, user_id, name, email, phone, hostel_id, room_id)
                VALUES (?, ?, ?, ?, ?, NULL, NULL)
            ");

            $insertStudent->bind_param(
                "sisss",
                $student_id,
                $user_id,
                $name,
                $email,
                $phone
            );

            $insertStudent->execute();
        }

    }

}


/* ================= SUCCESS ================= */

header("Location: application_approval.php?updated=1");
exit;

?>