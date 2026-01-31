<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // your friend's database username
define('DB_PASS', '');            // your friend's database password
define('DB_NAME', 'hostel_management');  // your friend's existing database name

function get_db_conn() {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
  }
  $conn->set_charset('utf8mb4');
  return $conn;
}
?>
