<?php
include '../database/db_connect.php';

$id = $_GET['id'];

$req = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM room_swap_requests WHERE id='$id'
"));

$a = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT name FROM students WHERE student_id='{$req['student_a_id']}'
"));

$b = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT name FROM students WHERE student_id='{$req['student_b_id']}'
"));

echo json_encode([
"student_a"=>$a['name'],
"room_a"=>$req['room_a'],
"student_b"=>$b['name'],
"room_b"=>$req['room_b'],
"reason"=>$req['reason']
]);
?>