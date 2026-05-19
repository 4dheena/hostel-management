<?php

session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


/* ================= GET STUDENT ================= */

$stmt = $conn->prepare("
SELECT student_id
FROM students
WHERE user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();


if (!$student) {

    $_SESSION['message'] = "⚠ Student record not found.";
    header("Location: dashboard.php");
    exit;
}

$student_id = $student['student_id'];


/* ================= UPDATE STATUS ================= */

$update = $conn->prepare("
UPDATE hostel_applications
SET allotment_status = 'accepted'
WHERE student_id = ?
");

$update->bind_param("s", $student_id);
$update->execute();


/* ================= SUCCESS ================= */

$_SESSION['message'] = "✅ Hostel allotment accepted successfully.";

header("Location: view_allotment.php");
exit;

?>