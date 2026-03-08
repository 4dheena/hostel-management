<?php
session_start();
require_once '../database/db_connect.php';

/* ================= ADMIN SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}


/* ================= SAFETY CHECK ================= */

$check = $conn->query("
SELECT COUNT(*) AS remaining
FROM students
WHERE hostel_id IS NULL
");

$row = $check->fetch_assoc();

if ($row['remaining'] == 0) {

    $_SESSION['message'] = "⚠ All students have already been allotted. No action taken.";
        header("Location: dashboard.php");
        exit;;

    exit;
}


/* ================= FETCH STUDENTS ================= */

$studentsQuery = $conn->query("
SELECT id, gender
FROM students
WHERE hostel_id IS NULL
ORDER BY id
");

$students = [];

while ($row = $studentsQuery->fetch_assoc()) {

    $students[] = [
        "id" => $row['id'],
        "gender" => $row['gender']
    ];
}

if (count($students) == 0) {

    $_SESSION['message'] = "⚠ No students available for allotment.";
    header("Location: dashboard.php");
    exit;
}


/* ================= LOAD HOSTELS + ROOMS ================= */

$hostelQuery = $conn->query("
SELECT hostel_id, room_sharing
FROM hostels
ORDER BY hostel_id
");

$hostels = [];

while ($row = $hostelQuery->fetch_assoc()) {

    $hid = $row['hostel_id'];

    $roomQuery = $conn->query("
    SELECT room_id
    FROM rooms
    WHERE hostel_id = $hid
    ORDER BY room_id
    ");

    $rooms = [];

    while ($r = $roomQuery->fetch_assoc()) {

        $rooms[] = [
            "room_id" => $r['room_id']
        ];
    }

    $hostels[$hid] = [
        "sharing" => $row['room_sharing'],
        "rooms" => $rooms
    ];
}


/* ================= ROUND ROBIN HOSTELS ================= */

$hostel_ids = array_keys($hostels);
$total_hostels = count($hostel_ids);
$hostel_pointer = 0;


/* ================= ALLOT STUDENTS ================= */

foreach ($students as $student) {

    $assigned = false;

    $student_id = $student['id'];
    $student_gender = $student['gender'];

    for ($i = 0; $i < $total_hostels; $i++) {

        $hid = $hostel_ids[$hostel_pointer];
        $hostel = &$hostels[$hid];

        foreach ($hostel['rooms'] as &$room) {

            /* ================= ROOM OCCUPANCY CHECK ================= */

            $occupancyCheck = $conn->query("
            SELECT COUNT(*) AS occupants
            FROM students
            WHERE room_id = {$room['room_id']}
            ");

            $occupancy = $occupancyCheck->fetch_assoc()['occupants'];

            if ($occupancy >= $hostel['sharing']) {
                continue;
            }


            /* ================= GENDER CHECK ================= */

            $genderCheck = $conn->query("
            SELECT gender
            FROM students
            WHERE room_id = {$room['room_id']}
            LIMIT 1
            ");

            $existing = $genderCheck->fetch_assoc();

            if ($existing && $existing['gender'] != $student_gender) {
                continue;
            }


            /* ================= ASSIGN ROOM ================= */

            $stmt = $conn->prepare("
            UPDATE students
            SET hostel_id = ?, room_id = ?
            WHERE id = ?
            ");

            $stmt->bind_param("iii", $hid, $room['room_id'], $student_id);
            $stmt->execute();

            $assigned = true;

            /* MOVE POINTER FOR BALANCED HOSTELS */

            $hostel_pointer = ($hostel_pointer + 1) % $total_hostels;

            break;
        }

        if ($assigned) break;

        $hostel_pointer = ($hostel_pointer + 1) % $total_hostels;
    }


    if (!$assigned) {

        $_SESSION['message'] = "⚠ No available rooms left. Please check hostel capacity.";
        header("Location: dashboard.php");
        exit;
    }
}


/* ================= SUCCESS ================= */

$_SESSION['message'] = "✅ Allotment completed successfully.";

header("Location: dashboard.php");
exit;

?>