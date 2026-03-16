<?php

require_once '../database/db_connect.php';

if(!isset($_GET['id'])){
    echo json_encode(["error"=>"No request ID"]);
    exit;
}

$request_id = $_GET['id'];

$stmt = $conn->prepare("
SELECT 
g.guest_name,
g.guest_email,
g.guest_phone,
g.room_number,
g.stay_from,
g.stay_to,
g.warden_remark,
w.full_name AS warden_name
FROM guest_requests g
LEFT JOIN wardens w 
ON g.warden_user_id = w.user_id
WHERE g.id = ?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    echo json_encode(["error"=>"Request not found"]);
    exit;
}

$data = $result->fetch_assoc();

echo json_encode($data);

?>