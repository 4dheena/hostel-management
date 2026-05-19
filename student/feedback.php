<?php

session_start();
require_once '../database/db_connect.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
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
    min-height:100vh;
    display:flex;
}

.left-section{
    width:50%;
    background:
    linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)),
    url('../assets/images/hostel.jpeg');

    background-size:cover;
    background-position:center;

    display:flex;
    justify-content:flex-start;
    align-items:flex-end;

    padding:60px;

    color:white;
}

.left-content {
    position: relative;
    z-index: 2;
}

.left-content h1 {
    font-size: 3.5rem;
    margin-bottom: 1rem;
}

.left-content p {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 500px;
}

.right-section{
    width:50%;
    background:#f8f9fa;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:40px;
}

.form-card{
    background:white;
    width:100%;
    max-width:600px;

    padding:40px;

    border-radius:14px;

    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.form-card h2{
    margin-bottom:30px;
    color:#333;
}

.rating-group{
    margin-bottom:25px;
}

.rating-group label{
    display:block;
    margin-bottom:10px;
    font-weight:bold;
    color:#444;
}

.stars{
    display:flex;
    flex-direction:row-reverse;
    justify-content:flex-end;
    gap:8px;
}

.stars input{
    display:none;
}

.stars label{
    font-size:30px;
    color:#ccc;
    cursor:pointer;
    transition:0.2s;
}

/* HOVER EFFECT */

.stars label:hover,
.stars label:hover ~ label{
    color:#ffc107;
}

/* SELECTED STARS */

.stars input:checked ~ label{
    color:#ffc107;
}

textarea{
    width:100%;
    min-height:140px;

    padding:15px;

    border:1px solid #ccc;
    border-radius:8px;

    resize:none;

    margin-top:10px;
}

button{
    width:100%;

    padding:14px;

    background:#007bff;
    color:white;

    border:none;
    border-radius:8px;

    font-size:16px;
    font-weight:bold;

    cursor:pointer;

    margin-top:25px;
}

button:hover{
    background:#0056b3;
}

@media(max-width:900px){

    body{
        flex-direction:column;
    }

    .left-section,
    .right-section{
        width:100%;
    }

    .left-section{
        min-height:300px;
    }
}

</style>

</head>

<body>

<!-- LEFT SIDE -->

<div class="left-section">

    <div class="left-content">

        <h1>We Value Your Feedback</h1>

        <p>
            Your feedback helps us improve hostel facilities,
            student experience, cleanliness, food quality,
            and overall hostel life.
        </p>

    </div>

</div>


<!-- RIGHT SIDE -->

<div class="right-section">

    <div class="form-card">

        <h2>Hostel Feedback Form</h2>
        <div class="rating-group">

    <label style="font-size:18px;">
        How would you describe your hostel experience overall?
    </label>

    <p style="
    color:#666;
    margin-top:8px;
    line-height:1.6;">
        Please rate the following facilities and services based on your experience.
    </p>

</div>
        <form action="submit_feedback.php" method="POST">

            <!-- MESS -->

            <div class="rating-group">

                <label>Mess Food</label>

                <div class="stars">

                    <?php for($i=5;$i>=1;$i--): ?>

                    <input type="radio" 
                    name="mess_rating" 
                    value="<?= $i ?>" 
                    id="mess<?= $i ?>" required>

                    <label for="mess<?= $i ?>">★</label>

                    <?php endfor; ?>

                </div>

            </div>


            <!-- CLEANLINESS -->

            <div class="rating-group">

                <label>Cleanliness</label>

                <div class="stars">

                    <?php for($i=5;$i>=1;$i--): ?>

                    <input type="radio" 
                    name="cleanliness_rating" 
                    value="<?= $i ?>" 
                    id="clean<?= $i ?>" required>

                    <label for="clean<?= $i ?>">★</label>

                    <?php endfor; ?>

                </div>

            </div>


            <!-- STAFF -->

            <div class="rating-group">

                <label>Staff Behaviour</label>

                <div class="stars">

                    <?php for($i=5;$i>=1;$i--): ?>

                    <input type="radio" 
                    name="staff_rating" 
                    value="<?= $i ?>" 
                    id="staff<?= $i ?>" required>

                    <label for="staff<?= $i ?>">★</label>

                    <?php endfor; ?>

                </div>

            </div>


            <!-- WARDEN -->

            <div class="rating-group">

                <label>Warden Support</label>

                <div class="stars">

                    <?php for($i=5;$i>=1;$i--): ?>

                    <input type="radio" 
                    name="warden_rating" 
                    value="<?= $i ?>" 
                    id="warden<?= $i ?>" required>

                    <label for="warden<?= $i ?>">★</label>

                    <?php endfor; ?>

                </div>

            </div>


            <!-- SUGGESTIONS -->

            <div class="rating-group">

                <label>Suggestions / Feedback</label>

                <textarea 
                name="suggestions"
                placeholder="Share your suggestions or concerns..."></textarea>

            </div>
           <div class="rating-group">

    <label style="
    display:flex;
    align-items:center;
    gap:10px;
    cursor:pointer;
    font-weight:normal;">

        <input 
        type="checkbox" 
        name="is_anonymous"
        value="1"
        style="
        width:18px;
        height:18px;
        cursor:pointer;">

        Submit feedback anonymously

    </label>

</div>

            <button type="submit">
                Submit Feedback
            </button>

        </form>

    </div>

</div>

</body>
</html>