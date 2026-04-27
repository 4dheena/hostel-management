<?php
session_start();
include '../database/db_connect.php';

/* 🔹 GET FORM DATA */
$student_a_id = $_POST['student_a_id'] ?? '';
$student_b_id = $_POST['student_b_id'] ?? '';
$reason = $_POST['reason'] ?? '';

/* 🔹 BASIC CHECK */
if ($student_a_id == '' || $student_b_id == '' || $reason == '') {
    echo "<script>
            alert('All fields are required');
            window.location.href='../forms/room_swap.php';
          </script>";
    exit();
}

/* 🔹 FETCH STUDENT A */
$a = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT s.student_id, s.hostel_id, s.room_id, r.room_number, s.user_id
FROM students s
JOIN rooms r ON s.room_id = r.room_id
WHERE s.student_id = '$student_a_id'
"));

/* 🔹 FETCH STUDENT B */
$b = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT s.student_id, s.hostel_id, s.room_id, r.room_number, s.user_id
FROM students s
JOIN rooms r ON s.room_id = r.room_id
WHERE s.student_id = '$student_b_id'
"));

/* 🔹 VALIDATIONS */

if (!$a || !$b) {
    $msg = "Invalid Student Details";
    header("Location: ../forms/room_swap.php?msg=" . urlencode($msg));
    exit();
}


if ($student_a_id == $student_b_id) {
    $msg = "Cannot swap with Yourself";


    header("Location: ../forms/room_swap.php?msg=" . urlencode($msg));
    exit();
}

if ($a['hostel_id'] != $b['hostel_id']) {
    $msg = "Students must be in same hostel";


    header("Location: ../forms/room_swap.php?msg=" . urlencode($msg));
    exit();}

if ($a['room_id'] == $b['room_id']) {
    $msg = "Both students are already in the same room";
    header("Location: ../forms/room_swap.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 CHECK ACTIVE REQUEST */
$check = mysqli_query($conn, "
SELECT * FROM room_swap_requests
WHERE (
    student_a_id='$student_a_id' OR 
    student_b_id='$student_a_id' OR
    student_a_id='$student_b_id' OR
    student_b_id='$student_b_id'
)
AND request_status='active'
");

if (mysqli_num_rows($check) > 0) {
    $msg = "One of the students already has an active request";


    header("Location: ../forms/room_swap.php?msg=" . urlencode($msg));
    exit();
}

/* 🔹 INSERT REQUEST */
$insert = mysqli_query($conn, "
INSERT INTO room_swap_requests
(student_a_id, student_b_id, room_a, room_b, hostel_id, reason, a_status, b_status, warden_status, request_status, created_at)
VALUES (
'$student_a_id',
'$student_b_id',
'{$a['room_number']}',
'{$b['room_number']}',
'{$a['hostel_id']}',
'$reason',
'pending',
'pending',
'pending',
'active',
NOW()
)
");

if (!$insert) {
  $msg = "Database error while submitting";

echo "
<form id='f' method='POST' action='../forms/room_swap.php'>
    <input type='hidden' name='msg' value=\"$msg\">
</form>

<script>
document.getElementById('f').submit();
</script>
";
exit();
}

/* 🔥 GET REQUEST ID */
$request_id = mysqli_insert_id($conn);

/* 🔔 NOTIFICATIONS */

$title = "Room Swap Request";

$messageA = "Room swap request created. Please check and respond.";
$messageB = "A student requested a room swap with you. Please check.";

/* Student A notification */
mysqli_query($conn, "
INSERT INTO notifications (user_id, hostel_id, title, message, type, reference_id, is_read, created_at)
VALUES (
'{$a['user_id']}',
'{$a['hostel_id']}',
'$title',
'$messageA',
'room_swap',
'$request_id',
0,
NOW()
)
");

/* Student B notification */
mysqli_query($conn, "
INSERT INTO notifications (user_id, hostel_id, title, message, type, reference_id, is_read, created_at)
VALUES (
'{$b['user_id']}',
'{$a['hostel_id']}',
'$title',
'$messageB',
'room_swap',
'$request_id',
0,
NOW()
)
");

/* ✅ SUCCESS */
$msg = "Room swap request submitted successfully";


    header("Location: ../forms/room_swap.php?msg=" . urlencode($msg));
    exit();
?>