<?php

session_start();
require_once '../database/db_connect.php';
require_once '../utils/send_email.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
exit;
}

$request_id = $_POST['request_id'];
$action     = $_POST['action'];
$reason     = $_POST['reason'] ?? '';

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


/* GET FULL GUEST DETAILS (COMMON) */

$stmt2 = $conn->prepare("
SELECT guest_email, guest_name, guest_student_id, room_number, stay_from, stay_to, warden_status
FROM guest_requests
WHERE id = ?
");

$stmt2->bind_param("i",$request_id);
$stmt2->execute();

$res2 = $stmt2->get_result();
$data = $res2->fetch_assoc();

$email = $data['guest_email'];
$name  = $data['guest_name'];
$student_id = $data['guest_student_id'];
$room = $data['room_number'];
$from = $data['stay_from'];
$to   = $data['stay_to'];
$warden_status = $data['warden_status'];


/* ================= NORMAL APPROVE ================= */

if($action === "approved"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status='approved',
overall_status='approved'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

/* CREATE LOGIN */

$password = "Student@123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt3 = $conn->prepare("
INSERT INTO users (username, password, role)
VALUES (?, ?, 'student')
");

$stmt3->bind_param("ss", $student_id, $hashed_password);
$stmt3->execute();

$new_user_id = $conn->insert_id;

$stmt4 = $conn->prepare("
INSERT INTO students (student_id, user_id)
VALUES (?, ?)
");

$stmt4->bind_param("si", $student_id, $new_user_id);
$stmt4->execute();

/* EMAIL */

$subject = "Confirmation of Guest Stay Approval | Hostel Administration";

$message = "
<h2>Guest Stay Request – Approved</h2>

<p>Dear <b>$name</b>,</p>

<p>
We are pleased to inform you that your request for temporary accommodation as a guest within the hostel premises has been <b>successfully approved</b> by the administration after completing all necessary levels of verification.
</p>

<hr>

<h3>📌 Approved Stay Details</h3>
<ul>
<li><b>Room Number:</b> $room</li>
<li><b>Stay Duration:</b> From $from to $to</li>
</ul>

<hr>

<h3>📊 Review Summary</h3>
<ul>
<li><b>Inmate Approval:</b> Approved</li>
<li><b>Warden Decision:</b> $warden_status</li>
<li><b>Final Administrative Decision:</b> Approved</li>
</ul>

<hr>

<h3>🔐 System Access Credentials</h3>
<p>
As part of the hostel management system, a temporary student account has been created for you with the following credentials:
</p>

<ul>
<li><b>Username (Student ID):</b> $student_id</li>
<li><b>Temporary Password:</b> $password</li>
</ul>

<p style='color:red;'>
<b>⚠️ Important Security Instruction:</b><br>
For security and privacy reasons, it is <b>mandatory</b> that you change your password immediately upon your first login.<br>
Failure to do so may result in restricted access to the system or potential security risks.
</p>

<hr>

<p>
You are expected to strictly adhere to all hostel rules, regulations, and code of conduct during your stay. Any violation may lead to immediate termination of your stay privileges.
</p>

<p>
If you face any difficulties or require assistance, please contact the hostel administration office.
</p>

<p>
Regards,<br>
<b>Hostel Administration</b><br>
Hostel Management System
</p>
";
sendEmail($email,$subject,$message);

}


/* ================= NORMAL REJECT ================= */

if($action === "rejected"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status='rejected',
overall_status='rejected',
admin_remark=?
WHERE id=?
");

$stmt->bind_param("si",$reason,$request_id);
$stmt->execute();

$subject = "Update on Guest Stay Request | Rejected by Administration";

$message = "
<h2>Guest Stay Request – Not Approved</h2>

<p>Dear <b>$name</b>,</p>

<p>
We regret to inform you that your request for guest stay in the hostel has been carefully reviewed and <b>has not been approved</b> by the administration.
</p>

<hr>

<h3>📊 Review Summary</h3>
<ul>
<li><b>Inmate Approval:</b> Approved</li>
<li><b>Warden Decision:</b> $warden_status</li>
<li><b>Final Administrative Decision:</b> Rejected</li>
</ul>

<hr>

<h3>❗ Reason for Rejection</h3>
<p>
$reason
</p>

<hr>

<h3>📌 Requested Stay Details</h3>
<ul>
<li><b>Room Number:</b> $room</li>
<li><b>Requested Duration:</b> From $from to $to</li>
</ul>

<hr>

<p>
This decision has been taken after careful consideration of hostel policies and administrative guidelines.
</p>

<p>
If you believe this decision requires further clarification, you may contact the hostel administration office for assistance.
</p>

<p>
Regards,<br>
<b>Hostel Administration</b>
</p>
";

sendEmail($email,$subject,$message);

}


/* ================= ACCEPT WARDEN ================= */

if($action === "accept_warden"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status=?, overall_status=?
WHERE id=?
");

$stmt->bind_param("ssi",$warden_status,$warden_status,$request_id);
$stmt->execute();

if($warden_status === "approved"){

$password = "Student@123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt3 = $conn->prepare("
INSERT INTO users (username, password, role)
VALUES (?, ?, 'student')
");

$stmt3->bind_param("ss", $student_id, $hashed_password);
$stmt3->execute();

$new_user_id = $conn->insert_id;

$stmt4 = $conn->prepare("
INSERT INTO students (student_id, user_id)
VALUES (?, ?)
");

$stmt4->bind_param("si", $student_id, $new_user_id);
$stmt4->execute();

$subject = "Guest Stay Request Update | Decision Based on Warden Review";

$message = "
<h2>Guest Stay Request – Final Decision</h2>

<p>Dear <b>$name</b>,</p>

<p>
Your guest stay request has been reviewed at all required levels. The administration has decided to proceed in accordance with the warden’s recommendation.
</p>

<hr>

<h3>📊 Review Summary</h3>
<ul>
<li><b>Inmate Approval:</b> Approved</li>
<li><b>Warden Decision:</b> $warden_status</li>
<li><b>Final Administrative Decision:</b> Accepted Warden Decision</li>
</ul>

<hr>

<p>
Please consider the warden’s decision as final in this case.
</p>

<p>
For further clarification, you may contact the hostel office.
</p>

<p>
Regards,<br>
<b>Hostel Administration</b>
</p>
";

sendEmail($email,$subject,$message);

}else{

$subject = "Guest Stay Rejected";

$message = "Your request was rejected based on warden decision.";

sendEmail($email,$subject,$message);

}

}


/* ================= OVERRIDE APPROVE ================= */

if($action === "override_approve"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status='approved',
overall_status='approved',
admin_remark=?
WHERE id=?
");

$stmt->bind_param("si",$reason,$request_id);
$stmt->execute();

$subject = "Guest Stay Approved (Administrative Override)";

$message = "
<h2>Guest Stay Request – Approved (Override Decision)</h2>

<p>Dear <b>$name</b>,</p>

<p>
Your guest stay request has undergone an additional administrative review. Based on further evaluation, the administration has decided to <b>approve your request by overriding the warden’s initial decision</b>.
</p>

<hr>

<h3>📊 Review Summary</h3>
<ul>
<li><b>Inmate Approval:</b> Approved</li>
<li><b>Warden Decision:</b> Rejected</li>
<li><b>Final Administrative Decision:</b> Approved (Override)</li>
</ul>

<hr>

<h3>📝 Administrative Remark</h3>
<p>$reason</p>

<hr>

<p>
This approval has been granted under special consideration. You are expected to comply strictly with all hostel rules and maintain discipline during your stay.
</p>

<p>
Any misconduct may result in immediate cancellation of this approval.
</p>

<p>
Regards,<br>
<b>Hostel Administration</b>
</p>
";
sendEmail($email,$subject,$message);

}


/* ================= OVERRIDE REJECT ================= */

if($action === "override_reject"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET admin_status='rejected',
overall_status='rejected',
admin_remark=?
WHERE id=?
");

$stmt->bind_param("si",$reason,$request_id);
$stmt->execute();

$subject = "Guest Stay Request Rejected (Administrative Override)";

$message = "
<h2>Guest Stay Request – Rejected (Override Decision)</h2>

<p>Dear <b>$name</b>,</p>

<p>
Your guest stay request has been reviewed at the administrative level. Although it was previously approved by the warden, the administration has decided to <b>reject the request after further evaluation</b>.
</p>

<hr>

<h3>📊 Review Summary</h3>
<ul>
<li><b>Inmate Approval:</b> Approved</li>
<li><b>Warden Decision:</b> Approved</li>
<li><b>Final Administrative Decision:</b> Rejected (Override)</li>
</ul>

<hr>

<h3>📝 Administrative Remark</h3>
<p>$reason</p>

<hr>

<p>
This decision has been taken in accordance with hostel policies and administrative regulations, which take precedence over intermediate approvals.
</p>

<p>
We appreciate your understanding in this matter.
</p>

<p>
Regards,<br>
<b>Hostel Administration</b>
</p>
";

sendEmail($email,$subject,$message);

}


/* ================= NOTIFICATION ================= */

if($guest_user_id){

$title = "Guest Request Update";
$message = "Admin has reviewed your request.";

$type = "guest_final_status";

$stmt = $conn->prepare("
INSERT INTO notifications (user_id,title,message,type,reference_id)
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

echo "SUCCESS";
?>