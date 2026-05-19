<?php
session_start();
require_once '../database/db_connect.php';
require '../mailer/send_email.php';
/* ================= ADMIN SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}


/* ================= RESET OLD ALLOTMENT STATUS ================= */

$conn->query("
UPDATE hostel_applications
SET allotment_status = 'pending',
    allotted_hostel_id = NULL,
    allotted_room_id = NULL
WHERE application_status = 'approved'
");


/* ================= CLEAR OLD STUDENTS ================= */

$conn->query("DELETE FROM students");


/* ================= FETCH APPROVED STUDENTS ================= */

$studentsQuery = $conn->query("
SELECT id, gender, priority_score
FROM hostel_applications
WHERE application_status = 'approved'
AND allotment_status = 'pending'
ORDER BY priority_score DESC
");

$students = [];

while ($row = $studentsQuery->fetch_assoc()) {

    $students[] = [
        "id" => $row['id'],
        "gender" => $row['gender']
    ];
}


if (count($students) == 0) {

    $_SESSION['message'] = "⚠ No approved students available for allotment.";
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

    $application_id = $student['id'];
    $student_gender = $student['gender'];

    for ($i = 0; $i < $total_hostels; $i++) {

        $hid = $hostel_ids[$hostel_pointer];
        $hostel = &$hostels[$hid];

        foreach ($hostel['rooms'] as &$room) {

            /* ================= ROOM OCCUPANCY CHECK ================= */

            $occupancyCheck = $conn->query("
            SELECT COUNT(*) AS occupants
            FROM hostel_applications
            WHERE allotted_room_id = {$room['room_id']}
            AND allotment_status IN ('allotted', 'accepted')
            ");

            $occupancy = $occupancyCheck->fetch_assoc()['occupants'];

            if ($occupancy >= $hostel['sharing']) {
                continue;
            }


            /* ================= GENDER CHECK ================= */

            $genderCheck = $conn->query("
            SELECT gender
            FROM hostel_applications
            WHERE allotted_room_id = {$room['room_id']}
            AND allotment_status IN ('allotted', 'accepted')
            LIMIT 1
            ");

            $existing = $genderCheck->fetch_assoc();

            if ($existing && $existing['gender'] != $student_gender) {
                continue;
            }


            /* ================= ASSIGN ROOM ================= */

            $stmt = $conn->prepare("
            UPDATE hostel_applications
            SET
                allotment_status = 'allotted',
                allotted_hostel_id = ?,
                allotted_room_id = ?
            WHERE id = ?
            ");

            $stmt->bind_param("iii", $hid, $room['room_id'], $application_id);
            $stmt->execute();


            /* ================= GET APPLICATION DATA ================= */

            $appQuery = $conn->prepare("
            SELECT
                student_id,
                full_name,
                personal_email,
                phone,
                password_hash
            FROM hostel_applications
            WHERE id = ?
            ");

            $appQuery->bind_param("i", $application_id);
            $appQuery->execute();

            $appData = $appQuery->get_result()->fetch_assoc();

            $student_id = $appData['student_id'];
            $name = $appData['full_name'];
            $email = $appData['personal_email'];
            $phone = $appData['phone'];
            $password_hash = $appData['password_hash'];


            /* ================= CREATE USER IF NOT EXISTS ================= */

            $checkUser = $conn->prepare("
            SELECT user_id
            FROM users
            WHERE username = ?
            ");

            $checkUser->bind_param("s", $student_id);
            $checkUser->execute();

            $userResult = $checkUser->get_result();


            if ($userResult->num_rows == 0) {

                $insertUser = $conn->prepare("
                INSERT INTO users (username, password, role)
                VALUES (?, ?, 'student')
                ");

                $insertUser->bind_param("ss", $student_id, $password_hash);
                $insertUser->execute();

                $user_id = $insertUser->insert_id;

            } else {

                $userRow = $userResult->fetch_assoc();
                $user_id = $userRow['user_id'];
            }


            /* ================= INSERT INTO STUDENTS ================= */

            $insertStudent = $conn->prepare("
            INSERT INTO students
            (student_id, user_id, name, email, phone, hostel_id, room_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $insertStudent->bind_param(
                "sisssii",
                $student_id,
                $user_id,
                $name,
                $email,
                $phone,
                $hid,
                $room['room_id']
            );

            $insertStudent->execute();


            $assigned = true;
            /* ================= SEND ALLOTMENT EMAIL ================= */



$hostelQuery = $conn->query("
SELECT hostel_name
FROM hostels
WHERE hostel_id = $hid
");

$hostelData = $hostelQuery->fetch_assoc();

$hostel_name = $hostelData['hostel_name'];

$subject = "Hostel Allotment Confirmation";

$message = "
Dear $name,<br><br>

Congratulations! Your hostel allotment has been completed successfully.<br><br>

<b>Hostel:</b> $hostel_name <br>
<b>Room Number:</b> {$room['room_id']} <br><br>

You can now log in to the hostel portal using your student credentials and view your allotment slip.<br><br>

Regards,<br>
Hostel Administration
";

sendMail($email, $subject, $message);

            /* ================= MOVE POINTER ================= */

            $hostel_pointer = ($hostel_pointer + 1) % $total_hostels;

            break;
        }

        if ($assigned) {
            break;
        }

        $hostel_pointer = ($hostel_pointer + 1) % $total_hostels;
    }


    /* ================= WAITLIST ================= */

    if (!$assigned) {

        $waitStmt = $conn->prepare("
        UPDATE hostel_applications
        SET allotment_status = 'waitlisted'
        WHERE id = ?
        ");

        $waitStmt->bind_param("i", $application_id);
        $waitStmt->execute();
    }
}


/* ================= SUCCESS ================= */

$_SESSION['message'] = "✅ Allotment process completed successfully.";

header("Location: dashboard.php");
exit;

?>