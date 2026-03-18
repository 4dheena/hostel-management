<?php
require_once "../database/db_connect.php";

$id = $_GET['id'];

$q = mysqli_query($conn,"
SELECT * FROM stay_permissions WHERE permission_id=$id
");

$data = mysqli_fetch_assoc($q);

echo json_encode($data);