<?php

session_start();
require_once '../database/db_connect.php';
require_once '../utils/send_email.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    exit;
}

$user_id = $_SESSION['user_id'];

$request_id = $_POST['request_id'];
$decision   = $_POST['decision'];


/* GET STUDENT ID */

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

$stmt->bind_param("sis",$decision,$request_id,$student_id);
$stmt->execute();


/* CHECK IF UPDATE WORKED */

if($stmt->affected_rows == 0){
    echo "no_update";
    exit;
}


/* IF REJECTED → END PROCESS */

if($decision == "rejected"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET inmate_status='rejected',
overall_status='rejected'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();



/* GET GUEST DETAILS */
$stmt = $conn->prepare("
SELECT guest_name, guest_email, room_number, stay_from, stay_to
FROM guest_requests
WHERE id = ?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$res = $stmt->get_result();
$guest = $res->fetch_assoc();

$name  = $guest['guest_name'];
$email = $guest['guest_email'];
$room  = $guest['room_number'];
$from  = $guest['stay_from'];
$to    = $guest['stay_to'];

/* EMAIL */

$subject = "Guest Stay Request Rejected by Roommates";

$message = "
<h2>Guest Stay Request Update ❌</h2>

<p>Dear <b>$name</b>,</p>

<p>Your request to stay in the hostel has been reviewed by the inmates of Room <b>$room</b>.</p>

<p><b>Status:</b> Rejected</p>

<p>One or more inmates did not approve your request. As per hostel policy, approval from all inmates is mandatory.</p>

<hr>

<p><b>Requested Stay:</b></p>
<ul>
<li>From: $from</li>
<li>To: $to</li>
</ul>

<hr>

<p>If you have any concerns, please contact the hostel office.</p>

<p>Regards,<br>
Hostel Management System</p>
";

sendEmail($email,$subject,$message);
exit;

}


/* CHECK IF ALL ROOMMATES APPROVED */

$stmt = $conn->prepare("
SELECT COUNT(*) AS pending
FROM guest_roommate_approvals
WHERE request_id = ?
AND approval_status != 'approved'
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();


/* IF NO PENDING → SEND TO WARDEN */

if($row['pending'] == 0){

$stmt = $conn->prepare("
UPDATE guest_requests
SET inmate_status='approved',
overall_status='warden_review'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

}


echo "approved";

?>