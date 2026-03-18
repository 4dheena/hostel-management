<?php
require_once "../database/db_connect.php";

$id = $_POST['id'];
$decision = $_POST['decision'];

mysqli_query($conn,"
UPDATE stay_permissions 
SET status='$decision' 
WHERE permission_id=$id
");