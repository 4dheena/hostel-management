<?php
session_start();
include '../database/db_connect.php';

header('Content-Type: application/json');

/* 🔒 hide PHP errors from breaking JSON */
ini_set('display_errors', 0);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id'] ?? '';

if(!$user_id){
    echo json_encode(["status"=>"error","msg"=>"Session expired"]);
    exit();
}

/* 🔥 USE user_id DIRECTLY */
$user_id = $_SESSION['user_id'] ;

/* ===== INPUT ===== */
$status = $_POST['status'] ?? '';
$type   = $_POST['type'] ?? 'single';
$reason = $_POST['reason'] ?? '';

/* 🔥 prevent SQL break */
$reason = mysqli_real_escape_string($conn, $reason);

$today = date('Y-m-d');

/* ===== ABSENT ===== */
if ($status === "absent") {

    /* ===== SINGLE DAY ===== */
    if ($type === "single") {

        $date = $_POST['date'] ?? '';

        if (!$date || $date > $today) {
            echo json_encode(["status"=>"error","msg"=>"Invalid date"]);
            exit();
        }

        $q = mysqli_query($conn, "
        INSERT INTO attendance (user_id, date, reason)
        VALUES ('$user_id','$date','$reason')
        ON DUPLICATE KEY UPDATE reason='$reason'
        ");

        if(!$q){
            echo json_encode(["status"=>"error","msg"=>mysqli_error($conn)]);
            exit();
        }
    }

    /* ===== VACATION ===== */
    if ($type === "vacation") {

        $start = $_POST['start'] ?? '';
        $end   = $_POST['end'] ?? '';

        if (!$start || !$end || $start > $end) {
            echo json_encode(["status"=>"error","msg"=>"Invalid date range"]);
            exit();
        }

        $current = strtotime($start);
        $endDate = strtotime($end);

        while ($current <= $endDate) {

            $date = date('Y-m-d', $current);

            if ($date <= $today) {

                $q = mysqli_query($conn, "
                INSERT INTO attendance (user_id, date, reason)
                VALUES ('$user_id','$date','$reason')
                ON DUPLICATE KEY UPDATE reason='$reason'
                ");

                if(!$q){
                    echo json_encode(["status"=>"error","msg"=>mysqli_error($conn)]);
                    exit();
                }
            }

            $current = strtotime("+1 day", $current);
        }
    }
}

/* ===== PRESENT ===== */
if ($status === "present") {

    $date = $_POST['date'] ?? '';

    if ($date) {

        $q = mysqli_query($conn, "
        DELETE FROM attendance
        WHERE user_id='$user_id' AND date='$date'
        ");

        if(!$q){
            echo json_encode(["status"=>"error","msg"=>mysqli_error($conn)]);
            exit();
        }
    }
}

/* ===== SUCCESS ===== */
echo json_encode(["status"=>"success"]);
exit();
?>