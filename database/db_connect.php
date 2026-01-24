<?php
// db_connect.php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "hostel_management";

$conn = new mysqli($servername, $dbusername, $dbpassword, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// set charset
$conn->set_charset("utf8mb4");
?>
