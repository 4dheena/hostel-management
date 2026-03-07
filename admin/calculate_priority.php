<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$now = date('Y-m-d H:i:s');

/* LOAD SETTINGS */
$settings = $conn->query("
    SELECT start_date, end_date, edit_start, edit_end
    FROM application_settings
    WHERE id = 1
")->fetch_assoc();

/* BLOCK IF WINDOWS OPEN */
if (
    ($now >= $settings['start_date'] && $now <= $settings['end_date']) ||
    (!empty($settings['edit_start']) &&
     $now >= $settings['edit_start'] &&
     $now <= $settings['edit_end'])
) {
    $_SESSION['message'] = "❌ Cannot calculate while window is open.";
    header("Location: dashboard.php");
    exit;
}

/* SCORING FUNCTIONS */
function distanceScore($d) {
    if ($d >= 500) return 40;
    if ($d >= 300) return 30;
    if ($d >= 150) return 20;
    if ($d >= 50)  return 10;
    return 0;
}

function incomeScore($i) {
    if ($i <= 100000) return 40;
    if ($i <= 200000) return 30;
    if ($i <= 400000) return 20;
    if ($i <= 600000) return 10;
    return 0;
}

function pwdScore($s, $p) {
    if ($s !== 'Yes' || $p === null) return 0;
    if ($p >= 60) return 20;
    if ($p >= 40) return 15;
    if ($p > 0)   return 10;
    return 0;
}

/* FETCH SUBMITTED */
$result = $conn->query("
    SELECT id, distance_km, annual_income, pwd_status, disability_percentage
    FROM hostel_applications
    WHERE submitted_at IS NOT NULL
");

while ($row = $result->fetch_assoc()) {

    $score =
        distanceScore($row['distance_km']) +
        incomeScore($row['annual_income']) +
        pwdScore($row['pwd_status'], $row['disability_percentage']);

    $update = $conn->prepare("
        UPDATE hostel_applications
        SET priority_score = ?
        WHERE id = ?
    ");
    $update->bind_param("ii", $score, $row['id']);
    $update->execute();
}

$_SESSION['message'] = "✅ Priority calculated successfully!";
header("Location: dashboard.php");
exit;