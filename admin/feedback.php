<?php

session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}


/* ================= FETCH HOSTELS ================= */

$hostelQuery = $conn->query("
SELECT hostel_id, hostel_name
FROM hostels
ORDER BY hostel_name
");


/* ================= FILTER ================= */

$selected_hostel = isset($_GET['hostel_id']) 
? $_GET['hostel_id'] 
: '';


/* ================= FETCH FEEDBACK ================= */

if (!empty($selected_hostel)) {

    $feedbackQuery = $conn->prepare("
    SELECT 
        f.*,
        h.hostel_name
    FROM feedback f

    LEFT JOIN hostels h
    ON f.hostel_id = h.hostel_id

    WHERE f.hostel_id = ?

    ORDER BY f.created_at DESC
    ");

    $feedbackQuery->bind_param("i", $selected_hostel);
    $feedbackQuery->execute();

    $feedbacks = $feedbackQuery->get_result();

} else {

    $feedbacks = $conn->query("
    SELECT 
        f.*,
        h.hostel_name
    FROM feedback f

    LEFT JOIN hostels h
    ON f.hostel_id = h.hostel_id

    ORDER BY f.created_at DESC
    ");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Feedback</title>

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
    margin-bottom:10px;
}


/* FILTER */

.filter-bar{
    margin-bottom:30px;
}

.filter-bar form{
    display:flex;
    gap:15px;
    align-items:center;
}

.filter-bar select{
    padding:10px 14px;
    border:1px solid #ccc;
    border-radius:8px;
    min-width:220px;
    font-size:15px;
}


/* FEEDBACK GRID */

.feedback-container{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
    gap:25px;
}


/* CARD */

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

.hostel-name{
    margin-top:5px;
    color:#007bff;
    font-size:14px;
}


/* SECTIONS */

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
}

.suggestion-box{
    background:#f8f9fa;
    padding:15px;
    border-radius:8px;
    line-height:1.7;
    color:#444;
}


/* EMPTY */

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

    <h1>Student Feedback Overview</h1>

</div>


<!-- FILTER -->

<div class="filter-bar">

    <form method="GET">

        <select name="hostel_id" onchange="this.form.submit()">

            <option value="">
                All Hostels
            </option>

            <?php while($hostel = $hostelQuery->fetch_assoc()): ?>

            <option 
                value="<?= $hostel['hostel_id'] ?>"

                <?= ($selected_hostel == $hostel['hostel_id']) 
                ? 'selected' 
                : '' ?>>

                <?= htmlspecialchars($hostel['hostel_name']) ?>

            </option>

            <?php endwhile; ?>

        </select>

    </form>

</div>


<!-- FEEDBACK -->

<?php if($feedbacks->num_rows > 0): ?>

<div class="feedback-container">

<?php while($row = $feedbacks->fetch_assoc()): ?>

<div class="feedback-card">

    <!-- TOP -->

    <div class="top-row">

        <div>

            <div class="student-name">

                <?php if($row['is_anonymous'] == 1): ?>

                    Anonymous Student

                <?php else: ?>

                    <?= htmlspecialchars($row['student_id']) ?>

                <?php endif; ?>

            </div>

            <div class="hostel-name">

                🏠 <?= htmlspecialchars($row['hostel_name']) ?>

            </div>

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

    <h2>No Feedback Found</h2>

    <p>
        No feedback submissions available for the selected hostel.
    </p>

</div>

<?php endif; ?>

</body>
</html>