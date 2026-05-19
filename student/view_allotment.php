<?php

session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


/* ================= FETCH STUDENT DATA ================= */

$stmt = $conn->prepare("
SELECT 
    s.student_id,
    s.name,
    s.email,
    s.phone,

    h.hostel_name,
    r.room_number,

    ha.allotment_status

FROM students s

LEFT JOIN hostels h 
ON s.hostel_id = h.hostel_id

LEFT JOIN rooms r 
ON s.room_id = r.room_id

LEFT JOIN hostel_applications ha
ON s.student_id = ha.student_id

WHERE s.user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();


/* ================= CHECK ALLOTMENT ================= */

if (!$data || empty($data['hostel_name'])) {

    $_SESSION['message'] = "⚠ You have not been allotted a hostel yet.";
    header("Location: dashboard.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Allotment</title>

<style>

body{
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 30px;
}

.container{
    max-width: 800px;
    margin: auto;
}

.card{
    background: white;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.top-bar{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.title{
    font-size: 28px;
    font-weight: bold;
}

.print-btn{
    background: #007bff;
    color: white;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: bold;
}

.section{
    margin-top: 25px;
}

.section h3{
    margin-bottom: 15px;
    color: #333;
}

.detail{
    margin-bottom: 12px;
    font-size: 16px;
}

.label{
    font-weight: bold;
    display: inline-block;
    width: 180px;
}

.actions{
    margin-top: 40px;
    display: flex;
    gap: 20px;
}

.accept-btn{
    background: #28a745;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}

.reject-btn{
    background: #dc3545;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}

.status-box{
    margin-top: 30px;
    padding: 14px;
    border-radius: 6px;
    font-weight: bold;
    background: #d4edda;
    color: #155724;
}

</style>

</head>

<body>

<div class="container">

<div class="card">

    <!-- TOP BAR -->

    <div class="top-bar">

        <div class="title">
            Hostel Allotment Slip
        </div>

        <a href="download_allotment.php" class="print-btn">
            Print Slip
        </a>

    </div>


    <!-- STUDENT DETAILS -->

    <div class="section">

        <h3>Student Information</h3>

        <div class="detail">
            <span class="label">Student ID:</span>
            <?= $data['student_id'] ?>
        </div>

        <div class="detail">
            <span class="label">Name:</span>
            <?= $data['name'] ?>
        </div>

        <div class="detail">
            <span class="label">Email:</span>
            <?= $data['email'] ?>
        </div>

        <div class="detail">
            <span class="label">Phone:</span>
            <?= $data['phone'] ?>
        </div>

    </div>


    <!-- HOSTEL DETAILS -->

    <div class="section">

        <h3>Allotment Details</h3>

        <div class="detail">
            <span class="label">Hostel Name:</span>
            <?= $data['hostel_name'] ?>
        </div>

        <div class="detail">
            <span class="label">Room Number:</span>
            <?= $data['room_number'] ?>
        </div>

    </div>


    <!-- ACTION BUTTONS -->

    <?php if($data['allotment_status'] == 'allotted'): ?>

    <div class="actions">

        <a href="accept_allotment.php" class="accept-btn">
            Accept Allotment
        </a>

        <a href="reject_allotment.php" class="reject-btn">
            Reject Allotment
        </a>

    </div>

    <?php elseif($data['allotment_status'] == 'accepted'): ?>

    <div class="status-box">
        ✅ You have accepted this allotment.
    </div>

    <?php endif; ?>

</div>

</div>

</body>
</html>