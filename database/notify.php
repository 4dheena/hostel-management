<?php

function createNotification($conn, $user_id, $hostel_id, $title, $message, $type, $reference_id = null){

$stmt = $conn->prepare("
INSERT INTO notifications
(user_id, hostel_id, title, message, type, reference_id)
VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
"iisssi",
$user_id,
$hostel_id,
$title,
$message,
$type,
$reference_id
);

$stmt->execute();

}

?>