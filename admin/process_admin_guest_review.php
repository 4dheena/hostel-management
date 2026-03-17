<?php

session_start();
require_once '../database/db_connect.php';
require_once '../utils/send_email.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
header("Location: notifications.php");
exit;
}

$request_id = $_POST['request_id'];
$decision   = $_POST['decision'];
$reason     = $_POST['reject_reason'] ?? '';

/* GET GUEST USER */

$stmt = $conn->prepare("
SELECT u.user_id
FROM students s
JOIN users u ON s.user_id = u.user_id
JOIN guest_requests g ON g.guest_student_id = s.student_id
WHERE g.id = ?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$res = $stmt->get_result();
$row = $res->fetch_assoc();

$guest_user_id = $row['user_id'] ?? null;


/* ================= ADMIN APPROVES ================= */

if($decision === "approved"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status='approved',
overall_status='approved'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

require_once "../utils/send_email.php";

/* GET GUEST EMAIL */

$stmt2 = $conn->prepare("
SELECT guest_email
FROM guest_requests
WHERE id = ?
");
$stmt2 = $conn->prepare("
SELECT guest_email, guest_name, guest_student_id, room_number, stay_from, stay_to
FROM guest_requests
WHERE id = ?
");

$stmt2->bind_param("i",$request_id);
$stmt2->execute();

$res2 = $stmt2->get_result();
$row2 = $res2->fetch_assoc();

$email = $row2['guest_email'];
$name  = $row2['guest_name'];
$student_id = $row2['guest_student_id'];
$room = $row2['room_number'];
$from = $row2['stay_from'];
$to   = $row2['stay_to'];

/* DEFAULT PASSWORD */
$password = "Student@123";;

/* HASH PASSWORD (VERY IMPORTANT) */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* GET GUEST DETAILS AGAIN (if not already available) */
$student_id = $row2['guest_student_id'];

/* INSERT INTO USERS TABLE */

$stmt3 = $conn->prepare("
INSERT INTO users (username, password, role)
VALUES (?, ?, 'student')
");

$stmt3->bind_param("ss", $student_id, $hashed_password);
$stmt3->execute();

/* GET NEW USER ID */
$new_user_id = $conn->insert_id;


/* INSERT INTO STUDENTS TABLE */

$stmt4 = $conn->prepare("
INSERT INTO students (student_id, user_id)
VALUES (?, ?)
");

$stmt4->bind_param("si", $student_id, $new_user_id);
$stmt4->execute();

/* EMAIL CONTENT */

$subject = "Guest Stay Approved | Hostel Management System";

$message = "
<h2>Guest Stay Request Approved 🎉</h2>

<p>Dear <b>$name</b>,</p>

<p>We are pleased to inform you that your request for guest stay in the hostel has been <b>approved</b> by the administration.</p>

<hr>

<h3>📌 Stay Details</h3>
<ul>
<li><b>Room Number:</b> $room</li>
<li><b>Stay From:</b> $from</li>
<li><b>Stay To:</b> $to</li>
</ul>

<hr>

<h3>🔐 Login Credentials</h3>
<ul>
<li><b>Username:</b> $student_id</li>
<li><b>Password:</b> $password</li>
</ul>

<p style='color:red;'><b>⚠️ Important:</b> Please change your password immediately after your first login for security reasons.</p>

<hr>

<p>If you face any issues, please contact the hostel office.</p>

<p>Regards,<br>
<b>Hostel Management System</b></p>
";

sendEmail($email,$subject,$message);

if($guest_user_id){

$title = "Guest Stay Approved";
$message = "Admin approved your guest stay request.";

$type = "guest_final_status";

$stmt = $conn->prepare("
INSERT INTO notifications
(user_id,title,message,type,reference_id)
VALUES (?,?,?,?,?)
");

$stmt->bind_param(
"isssi",
$guest_user_id,
$title,
$message,
$type,
$request_id
);

$stmt->execute();

}

}



/* ================= ADMIN REJECTS ================= */

if($decision === "rejected"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status='rejected',
overall_status='rejected',
admin_remark=?
WHERE id=?
");

$stmt->bind_param("si",$reason,$request_id);
$stmt->execute();

/* GET DETAILS */
$stmt5 = $conn->prepare("
SELECT guest_name, guest_email, room_number, stay_from, stay_to, warden_status
FROM guest_requests
WHERE id = ?
");

$stmt5->bind_param("i",$request_id);
$stmt5->execute();

$res5 = $stmt5->get_result();
$row5 = $res5->fetch_assoc();

$name  = $row2['guest_name'];
$email = $row2['guest_email'];
$room  = $row2['room_number'];
$from  = $row2['stay_from'];
$to    = $row2['stay_to'];
$warden_status = $row2['warden_status'];

/* EMAIL MESSAGE */

$subject = "Guest Stay Request Rejected by Administration";

$message = "
<h2>Guest Stay Request Update ❌</h2>

<p>Dear <b>$name</b>,</p>

<p>Your guest stay request has been reviewed by the hostel administration.</p>

<p><b>Status:</b> Rejected</p>
";

/* CASE HANDLING */

if($warden_status === 'approved'){
$message .= "
<p>The request was initially approved by the warden but has been rejected by the admin.</p>
";
} else {
$message .= "
<p>The request was rejected by the warden and the admin has confirmed the same.</p>
";
}

/* ADD REASON */

$message .= "
<hr>

<p><b>Reason:</b> $reason</p>

<hr>

<p><b>Stay Details:</b></p>
<ul>
<li>Room: $room</li>
<li>From: $from</li>
<li>To: $to</li>
</ul>

<hr>

<p>If you have concerns, contact hostel office.</p>

<p>Regards,<br>
Hostel Administration</p>
";

sendEmail($email,$subject,$message);

if($guest_user_id){

$title = "Guest Stay Rejected";
$message = "Admin rejected your guest stay request. Reason: ".$reason;

$type = "guest_final_status";

$stmt = $conn->prepare("
INSERT INTO notifications
(user_id,title,message,type,reference_id)
VALUES (?,?,?,?,?)
");

$stmt->bind_param(
"isssi",
$guest_user_id,
$title,
$message,
$type,
$request_id
);

$stmt->execute();

}

}

header("Location: notifications.php");
exit;

?>