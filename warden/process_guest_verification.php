<?php

session_start();
require_once '../database/db_connect.php';

$request_id = $_POST['request_id'];
$decision = $_POST['decision'];
$reason = $_POST['reject_reason'] ?? '';

$admin_id = 1; // admin user id

/* WARDEN APPROVED */

if($decision === "approved"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET warden_status='approved',
overall_status='admin_review'
WHERE id=?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

/* notify admin */

$title = "Guest Request Awaiting Approval";
$message = "Warden approved a guest stay request.";

$type = "guest_admin_review";

$stmt = $conn->prepare("
INSERT INTO notifications (user_id,title,message,type,reference_id)
VALUES (?,?,?,?,?)
");

$stmt->bind_param("isssi",$admin_id,$title,$message,$type,$request_id);
$stmt->execute();

}


/* WARDEN REJECTED */

if($decision === "rejected"){

$stmt = $conn->prepare("
UPDATE guest_requests
SET warden_status='rejected',
overall_status='rejected_by_warden',
warden_remark=?
WHERE id=?
");

$stmt->bind_param("si",$reason,$request_id);
$stmt->execute();

/* notify admin */

$title = "Guest Request Rejected by Warden";
$message = "Warden rejected a guest stay request.";

$type = "guest_admin_review";

$stmt = $conn->prepare("
INSERT INTO notifications (user_id,title,message,type,reference_id)
VALUES (?,?,?,?,?)
");

$stmt->bind_param("isssi",$admin_id,$title,$message,$type,$request_id);
$stmt->execute();

}

header("Location: guest_requests.php");
exit;

?>