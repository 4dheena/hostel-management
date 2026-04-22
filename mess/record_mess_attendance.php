<?php
header('Content-Type: application/json');
require_once "database/db_connect.php";

$studentId = trim($_POST['student_id'] ?? '');
if ($studentId === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Student ID is required.',
    ]);
    exit;
}

// verify student exists
$stmt = $conn->prepare("SELECT student_id, name FROM students WHERE student_id = ? LIMIT 1");
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo json_encode([
        'success' => false,
        'message' => 'Student ID not found.',
    ]);
    exit;
}

$attendanceDate = date('Y-m-d');

// insert attendance only once per day
$insert = $conn->prepare(
    "INSERT INTO mess_attendance (student_id, attendance_date) VALUES (?, ?)"
);
$insert->bind_param("ss", $studentId, $attendanceDate);
$created = false;
$insertMessage = '';

if ($insert->execute()) {
    $created = true;
    $insertMessage = 'Attendance recorded successfully.';
} else {
    if ($conn->errno === 1062) {
        $insertMessage = 'Attendance for today has already been recorded.';
    } else {
        $insertMessage = 'Unable to record attendance: ' . $conn->error;
    }
}

$countStmt = $conn->prepare(
    "SELECT COUNT(DISTINCT attendance_date) AS attendance_days FROM mess_attendance WHERE student_id = ?"
);
$countStmt->bind_param("s", $studentId);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$attendanceDays = intval($countResult['attendance_days'] ?? 0);

$billAmount = $attendanceDays * 160;

$response = [
    'success' => $created || $insertMessage === 'Attendance for today has already been recorded.',
    'message' => sprintf("%s Student: %s (ID: %s).", $insertMessage, $student['name'], $studentId),
    'attendance_days' => $attendanceDays,
    'bill_amount' => $billAmount,
    'today_recorded' => $created,
];

echo json_encode($response);
