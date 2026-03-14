<?php

session_start();
require_once '../database/db_connect.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo "invalid";
    exit;
}

$user_id = $_SESSION['user_id'];

$request_id = $_POST['request_id'];
$decision   = $_POST['decision'];


/* FIND STUDENT ID OF CURRENT USER */

$stmt = $conn->prepare("
SELECT student_id
FROM students
WHERE user_id = ?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "student_not_found";
    exit;
}

$row = $result->fetch_assoc();
$student_id = $row['student_id'];


/* UPDATE ROOMMATE APPROVAL */

$stmt = $conn->prepare("
UPDATE guest_roommate_approvals
SET approval_status = ?, approved_at = NOW()
WHERE request_id = ?
AND student_id = ?
");

$stmt->bind_param("sii",$decision,$request_id,$student_id);
$stmt->execute();


/* IF REJECTED → UPDATE MAIN REQUEST */

if($decision == "rejected"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET inmate_status='rejected', overall_status='rejected'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

echo "rejected";
exit;

}


/* CHECK IF ALL ROOMMATES APPROVED */

$stmt = $conn->prepare("
SELECT COUNT(*) AS pending
FROM guest_roommate_approvals
WHERE request_id=?
AND approval_status != 'approved'
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row['pending'] == 0){

$stmt = $conn->prepare("
UPDATE guest_requests
SET inmate_status='approved', overall_status='warden_review'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

}

echo "approved";

?>