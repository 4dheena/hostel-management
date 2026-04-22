<?php
include("../database/db_connect.php");

$id = $_GET['id'];

mysqli_query($conn, "UPDATE room_swap_requests SET status='Rejected' WHERE id=$id");

header("Location: warden_dashboard.php");
?>