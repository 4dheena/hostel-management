<?php

require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}


/* ================= PREPARE STATUS UPDATE ================= */

$stmt = $conn->prepare("
    UPDATE hostel_applications
    SET application_status = ?
    WHERE id = ?
");


/* ================= UPDATE DATABASE ================= */

foreach ($_POST['application_status'] as $app_id => $status) {

    /* ================= UPDATE APPLICATION STATUS ================= */

    $stmt->bind_param("si", $status, $app_id);
    $stmt->execute();


    /* ================= HANDLE REJECTED STUDENTS ================= */

    if ($status === 'rejected') {

        /* GET STUDENT ID */

        $appQuery = $conn->prepare("
            SELECT student_id
            FROM hostel_applications
            WHERE id = ?
        ");

        $appQuery->bind_param("i", $app_id);
        $appQuery->execute();

        $appData = $appQuery->get_result()->fetch_assoc();

        if ($appData) {

            $student_id = $appData['student_id'];

            /* ================= DELETE FROM STUDENTS ================= */

            $deleteStudent = $conn->prepare("
                DELETE FROM students
                WHERE student_id = ?
            ");

            $deleteStudent->bind_param("s", $student_id);
            $deleteStudent->execute();


            /* ================= DELETE USER ================= */

            $deleteUser = $conn->prepare("
                DELETE FROM users
                WHERE username = ?
            ");

            $deleteUser->bind_param("s", $student_id);
            $deleteUser->execute();
        }
    }
}


/* ================= SUCCESS ================= */

header("Location: application_approval.php?updated=1");
exit;

?>