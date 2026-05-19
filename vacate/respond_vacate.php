<?php

require_once '../database/db_connect.php';

$request_id = $_POST['request_id'] ?? 0;

$stmt = $conn->prepare("
UPDATE vacate_requests
SET request_status = 'confirmed'
WHERE id = ?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

echo "success";
?>