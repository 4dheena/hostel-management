<?php

session_start();
require_once '../database/db_connect.php';

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