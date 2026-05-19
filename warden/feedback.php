<?php

session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


/* ================= GET WARDEN HOSTEL ================= */

$wardenQuery = $conn->prepare("
SELECT hostel_id, full_name
FROM wardens
WHERE user_id = ?
LIMIT 1
");

$wardenQuery->bind_param("i", $user_id);
$wardenQuery->execute();

$warden = $wardenQuery->get_result()->fetch_assoc();


if (!$warden) {

    die("Warden record not found.");
}

$hostel_id = $warden['hostel_id'];
$warden_name = $warden['full_name'];


/* ================= FETCH FEEDBACK ================= */

$feedbackQuery = $conn->prepare("
SELECT *
FROM feedback
WHERE hostel_id = ?
ORDER BY created_at DESC
");

$feedbackQuery->bind_param("i", $hostel_id);
$feedbackQuery->execute();

$feedbacks = $feedbackQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Hostel Feedback</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f4f6f9;
    padding:30px;
}

.page-title{
    margin-bottom:30px;
}

.page-title h1{
    color:#333;
    margin-bottom:8px;
}

.page-title p{
    color:#666;
}

.feedback-container{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
    gap:25px;
}

.feedback-card{
    background:white;
    border-radius:14px;
    padding:25px;

    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

.top-row{
    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:20px;
}

.student-name{
    font-weight:bold;
    color:#333;
    font-size:18px;
}

.date{
    color:#888;
    font-size:14px;
}

.section{
    margin-bottom:18px;
}

.section h3{
    margin-bottom:10px;
    color:#444;
    font-size:16px;
}

.rating{
    color:#ffc107;
    font-size:22px;
    margin-bottom:8px;
}

.suggestion-box{
    background:#f8f9fa;
    padding:15px;
    border-radius:8px;
    line-height:1.7;
    color:#444;
}

.no-feedback{
    background:white;
    padding:30px;
    border-radius:12px;
    text-align:center;
    color:#666;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

</style>

</head>

<body>

<div class="page-title">

    <h1>Hostel Feedback</h1>

    <p>
        Welcome <?= htmlspecialchars($warden_name) ?>.
        Here are the latest feedback submissions from your hostel students.
    </p>

</div>


<?php if($feedbacks->num_rows > 0): ?>

<div class="feedback-container">

<?php while($row = $feedbacks->fetch_assoc()): ?>

<div class="feedback-card">

    <!-- TOP ROW -->

    <div class="top-row">

        <div class="student-name">

            <?php if($row['is_anonymous'] == 1): ?>

                Anonymous Student

            <?php else: ?>

                <?= htmlspecialchars($row['student_id']) ?>

            <?php endif; ?>

        </div>

        <div class="date">
            <?= date("d M Y", strtotime($row['created_at'])) ?>
        </div>

    </div>


    <!-- CLEANLINESS -->

    <div class="section">

        <h3>Cleanliness</h3>

        <div class="rating">
            <?= str_repeat("★", $row['cleanliness_rating']) ?>
        </div>

    </div>


    <!-- STAFF -->

    <div class="section">

        <h3>Staff Behaviour</h3>

        <div class="rating">
            <?= str_repeat("★", $row['staff_rating']) ?>
        </div>

    </div>


    <!-- WARDEN -->

    <div class="section">

        <h3>Warden Support</h3>

        <div class="rating">
            <?= str_repeat("★", $row['warden_rating']) ?>
        </div>

    </div>

    <!-- FACILITIES -->

    <div class="section">

        <h3>Facilities</h3>

        <div class="rating">
            <?= str_repeat("★", $row['facility_rating']) ?>
        </div>

    </div>

    <!-- SECURITY -->

    <div class="section">

        <h3>Security</h3>

        <div class="rating">
            <?= str_repeat("★", $row['security_rating']) ?>
        </div>

    </div>

    <!-- SUGGESTIONS -->

    <div class="section">

        <h3>Suggestions / Feedback</h3>

        <div class="suggestion-box">

            <?= nl2br(htmlspecialchars($row['suggestions'])) ?>

        </div>

    </div>

</div>

<?php endwhile; ?>

</div>

<?php else: ?>

<div class="no-feedback">

    <h2>No Feedback Yet</h2>

    <p>
        No students have submitted feedback for this hostel yet.
    </p>

</div>

<?php endif; ?>

</body>
</html>