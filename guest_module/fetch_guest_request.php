<?php

require_once '../database/db_connect.php';

if(!isset($_GET['id'])){
    echo json_encode(["error" => "No request ID"]);
    exit;
}

$request_id = $_GET['id'];

/* FETCH GUEST REQUEST DETAILS */

$stmt = $conn->prepare("
SELECT guest_name, guest_email, guest_phone, request_message
FROM guest_requests
WHERE id = ?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    echo json_encode(["error" => "Request not found"]);
    exit;
}

$row = $result->fetch_assoc();

/* RETURN JSON */

echo json_encode($row);

?>