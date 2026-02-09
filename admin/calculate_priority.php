<?php
/**
 * ADMIN – CALCULATE PRIORITY SCORE
 * --------------------------------
 * Rules enforced:
 * 1. Application window must be CLOSED
 * 2. Edit window must be CLOSED
 * 3. Score calculation allowed ONLY after edit window ends
 */

require_once '../database/db_connect.php';

date_default_timezone_set('Asia/Kolkata');
$now = date('Y-m-d H:i:s');

/* ================= LOAD WINDOW SETTINGS ================= */
$settingsQuery = "
    SELECT start_date, end_date, edit_start, edit_end
    FROM application_settings
    WHERE id = 1
";
$settings = $conn->query($settingsQuery)->fetch_assoc();

/* ================= SAFETY CHECKS ================= */

/* 1️⃣ Block if application window is still open */
if ($now >= $settings['start_date'] && $now <= $settings['end_date']) {
    die(
        "❌ Priority score calculation is not allowed.<br>" .
        "Application window is still OPEN until <b>{$settings['end_date']}</b>."
    );
}

/* 2️⃣ Block if edit window is still open */
if (
    !empty($settings['edit_start']) &&
    !empty($settings['edit_end']) &&
    $now >= $settings['edit_start'] &&
    $now <= $settings['edit_end']
) {
    die(
        "❌ Priority score calculation is not allowed.<br>" .
        "Edit window is OPEN until <b>{$settings['edit_end']}</b>."
    );
}

/* ================= SCORING FUNCTIONS ================= */

/* Distance score (max 40) */
function distanceScore($distance) {
    if ($distance >= 500) return 40;
    if ($distance >= 300) return 30;
    if ($distance >= 150) return 20;
    if ($distance >= 50)  return 10;
    return 0;
}

/* Income score (max 40) */
function incomeScore($income) {
    if ($income <= 100000) return 40;
    if ($income <= 200000) return 30;
    if ($income <= 400000) return 20;
    if ($income <= 600000) return 10;
    return 0;
}

/* PWD score (max 20) */
function pwdScore($status, $percentage) {
    if ($status !== 'Yes') return 0;

    if ($percentage >= 60) return 20;
    if ($percentage >= 40) return 15;
    if ($percentage > 0)   return 10;
    return 0;
}

/* ================= FETCH SUBMITTED APPLICATIONS ================= */

$query = "
    SELECT
        application_id,
        distance_km,
        annual_income,
        pwd_status,
        disability_percentage
    FROM hostel_applications
    WHERE submitted_at IS NOT NULL
";

$result = $conn->query($query);

if (!$result) {
    die("❌ Failed to fetch applications.");
}

/* ================= CALCULATE & UPDATE SCORES ================= */

while ($row = $result->fetch_assoc()) {

    $score =
        distanceScore($row['distance_km']) +
        incomeScore($row['annual_income']) +
        pwdScore($row['pwd_status'], $row['disability_percentage']);

    $update = $conn->prepare(
        "UPDATE hostel_applications
         SET priority_score = ?
         WHERE application_id = ?"
    );
    $update->bind_param("ii", $score, $row['application_id']);
    $update->execute();
}

/* ================= SUCCESS MESSAGE ================= */

echo "✅ Priority score calculation completed successfully.<br>";
echo "📌 Scores were calculated only after application and edit windows were closed.";
