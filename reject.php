<?php

$conn = mysqli_connect("localhost","root","","hostel_management");

$request_id = $_GET['id'];
$student_id = 23;

mysqli_query($conn,"
UPDATE inmate_approvals
SET approval_status='Rejected'
WHERE request_id='$request_id'
AND student_id='$student_id'
");

echo "Rejected";

?>