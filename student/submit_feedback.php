<?php

session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$user_id = $_SESSION['user_id'];


/* ================= GET STUDENT DETAILS ================= */

$stmt = $conn->prepare("
SELECT student_id, hostel_id
FROM students
WHERE user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();


if (!$student) {

    die("Student record not found.");
}

$student_id = $student['student_id'];
$hostel_id = $student['hostel_id'];


/* ================= GET FORM DATA ================= */

$mess_rating = $_POST['mess_rating'];

$cleanliness_rating = $_POST['cleanliness_rating'];

$staff_rating = $_POST['staff_rating'];

$warden_rating = $_POST['warden_rating'];

$suggestions = trim($_POST['suggestions']);

$is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;


/* ================= INSERT FEEDBACK ================= */

$insert = $conn->prepare("
INSERT INTO feedback (

    student_id,
    hostel_id,

    mess_rating,
    cleanliness_rating,
    staff_rating,
    warden_rating,

    suggestions,

    is_anonymous

)

VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");


$insert->bind_param(
    "siiiiisi",

    $student_id,
    $hostel_id,

    $mess_rating,
    $cleanliness_rating,
    $staff_rating,
    $warden_rating,

    $suggestions,

    $is_anonymous
);


if ($insert->execute()) {

    $_SESSION['message'] = "✅ Feedback submitted successfully.";

} else {

    $_SESSION['message'] = "⚠ Failed to submit feedback.";
}


/* ================= REDIRECT ================= */

header("Location: feedback.php");
exit;

?>