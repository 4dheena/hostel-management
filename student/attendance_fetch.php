<?php
session_start();
include '../database/db_connect.php';

/* 🔒 prevent warnings breaking JSON */
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');

/* 🔥 SUPPORT WARDEN + STUDENT */
$user_id = $_GET['user_id'] ?? $_SESSION['user_id'] ?? '';

if(!$user_id){
    echo json_encode(["status"=>"error"]);
    exit();
}

/* ===== GET MONTH FROM FRONTEND ===== */
$month = isset($_GET['month']) ? intval($_GET['month']) + 1 : date('n');
$year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

/* ===== CURRENT DATE ===== */
$current_month = date('n');
$current_year  = date('Y');

/* ===== GET ADMISSION DATE ===== */
$studentData = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT admission_date FROM students WHERE user_id='$user_id'
"));

$start_date = $studentData['admission_date'] ?? null;

if($start_date){
    $start_day   = date('j', strtotime($start_date));
    $start_month = date('n', strtotime($start_date));
    $start_year  = date('Y', strtotime($start_date));
}else{
    $start_day = 0;
    $start_month = 0;
    $start_year = 0;
}

/* ===== ABSENT DATA ===== */
$calendar = [];
$absent = 0;

$q = mysqli_query($conn, "
SELECT date, reason FROM attendance
WHERE user_id='$user_id'
AND MONTH(date)='$month'
AND YEAR(date)='$year'
");

while($row = mysqli_fetch_assoc($q)){
    $calendar[] = $row;
    $absent++;
}

/* ===== TOTAL + PRESENT ===== */
$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

/* BEFORE ADMISSION */
if ($year < $start_year || ($year == $start_year && $month < $start_month)) {
    $present = 0;
    $absent = 0;
    $percent = 0;
}

/* FUTURE MONTH */
elseif ($year > $current_year || ($year == $current_year && $month > $current_month)) {
    $present = 0;
    $absent = 0;
    $percent = 0;
}

/* ADMISSION MONTH */
elseif ($year == $start_year && $month == $start_month) {

    $days_considered = ($month == $current_month && $year == $current_year)
        ? date('j')
        : $total_days;

    $initial_absent = $start_day - 1;

    $absent += $initial_absent;

    $present = max(0, $days_considered - $absent);

    $percent = ($days_considered > 0)
        ? round(($present/$days_considered)*100)
        : 0;
}

/* NORMAL MONTH */
else {

    $days_considered = ($month == $current_month && $year == $current_year)
        ? date('j')
        : $total_days;

    $present = max(0, $days_considered - $absent);

    $percent = ($days_considered > 0)
        ? round(($present/$days_considered)*100)
        : 0;
}

/* ===== MONTHLY GRAPH ===== */
$monthly = [];

/* fetch absences */
$q2 = mysqli_query($conn, "
SELECT MONTH(date) as month, COUNT(*) as absent
FROM attendance
WHERE user_id='$user_id'
AND YEAR(date)='$year'
GROUP BY MONTH(date)
");

$absMap = [];
while($row = mysqli_fetch_assoc($q2)){
    $absMap[intval($row['month'])] = intval($row['absent']);
}

/* calculate */
for($i=1;$i<=12;$i++){

    /* BEFORE ADMISSION */
    if($year < $start_year || ($year == $start_year && $i < $start_month)){
        $monthly[$i] = 0;
        continue;
    }

    /* FUTURE */
    if($year > $current_year || ($year == $current_year && $i > $current_month)){
        $monthly[$i] = 0;
        continue;
    }

    $days = cal_days_in_month(CAL_GREGORIAN, $i, $year);

    if($i == $current_month && $year == $current_year){
        $days = date('j');
    }

    $abs = $absMap[$i] ?? 0;

    /* admission month fix */
    if($i == $start_month && $year == $start_year){
        $abs += ($start_day - 1);
    }

    $present_days = max(0, $days - $abs);

    $monthly[$i] = ($days > 0)
        ? round(($present_days/$days)*100)
        : 0;
}

/* convert */
$monthly_arr = [];
for($i=1;$i<=12;$i++){
    $monthly_arr[] = [
        "month"=>$i,
        "percentage"=>$monthly[$i]
    ];
}

/* ===== OUTPUT ===== */
echo json_encode([
    "total"=>$total_days,
    "present"=>$present,
    "absent"=>$absent,
    "percent"=>$percent,
    "calendar"=>$calendar,
    "monthly"=>$monthly_arr,
    "admission_date"=>$start_date
]);
exit();
?>