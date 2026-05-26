<?php

session_start();
require_once '../database/db_connect.php';
require '../mailer/send_email.php';
/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


/* ================= GET STUDENT DETAILS ================= */

$stmt = $conn->prepare("
SELECT 
    s.student_id,
    s.hostel_id,
    s.room_id
FROM students s
WHERE s.user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();


if (!$student) {

    $_SESSION['message'] = "⚠ Student record not found.";
    header("Location: dashboard.php");
    exit;
}

$student_id = $student['student_id'];
$vacant_hostel_id = $student['hostel_id'];
$vacant_room_id = $student['room_id'];


/* ================= UPDATE CURRENT STUDENT STATUS ================= */

$update = $conn->prepare("
UPDATE hostel_applications
SET allotment_status = 'rejected_by_student'
WHERE student_id = ?
");

$update->bind_param("s", $student_id);
$update->execute();
/* ================= SEND REJECTION MAIL ================= */


$mailQuery = $conn->prepare("
SELECT full_name, personal_email
FROM hostel_applications
WHERE student_id = ?
LIMIT 1
");

$mailQuery->bind_param("s", $student_id);
$mailQuery->execute();

$mailData = $mailQuery->get_result()->fetch_assoc();

$name = $mailData['full_name'];
$email = $mailData['personal_email'];


/* EMAIL CONTENT */

$subject = "Hostel Allotment Rejected";

$message = "
Dear $name,<br><br>

Your hostel allotment has been cancelled as per your request.<br><br>

Your hostel portal access has also been removed successfully.<br><br>

If you wish to apply again in the future, please contact the hostel administration.<br><br>

Regards,<br>
Aruvi Hostels Administration
";


/* SEND MAIL */

sendMail($email, $subject, $message);

/* ================= DELETE FROM STUDENTS ================= */

$deleteStudent = $conn->prepare("
DELETE FROM students
WHERE student_id = ?
");

$deleteStudent->bind_param("s", $student_id);
$deleteStudent->execute();


/* ================= DELETE FROM USERS ================= */

$deleteUser = $conn->prepare("
DELETE FROM users
WHERE user_id = ?
");

$deleteUser->bind_param("i", $user_id);
$deleteUser->execute();


/* ========================================================= */
/* ================= WAITLIST PROMOTION ==================== */
/* ========================================================= */


/* ================= FIND NEXT WAITLIST STUDENT ================= */

$waitlistQuery = $conn->query("
SELECT *
FROM hostel_applications
WHERE application_status = 'approved'
AND allotment_status = 'waitlisted'
ORDER BY priority_score DESC
LIMIT 1
");

$waitlistStudent = $waitlistQuery->fetch_assoc();


if ($waitlistStudent) {

    $next_application_id = $waitlistStudent['id'];

    $next_student_id = $waitlistStudent['student_id'];

    $name = $waitlistStudent['full_name'];

    $email = $waitlistStudent['personal_email'];

    $phone = $waitlistStudent['phone'];

    $password_hash = $waitlistStudent['password_hash'];


    /* ================= UPDATE ALLOTMENT ================= */

    $promoteStmt = $conn->prepare("
    UPDATE hostel_applications
    SET
        allotment_status = 'allotted',
        allotted_hostel_id = ?,
        allotted_room_id = ?
    WHERE id = ?
    ");

    $promoteStmt->bind_param(
        "iii",
        $vacant_hostel_id,
        $vacant_room_id,
        $next_application_id
    );

    $promoteStmt->execute();


    /* ================= CREATE USER ================= */

    $insertUser = $conn->prepare("
    INSERT INTO users (username, password, role)
    VALUES (?, ?, 'student')
    ");

    $insertUser->bind_param(
        "ss",
        $next_student_id,
        $password_hash
    );

    $insertUser->execute();

    $new_user_id = $insertUser->insert_id;


    /* ================= INSERT INTO STUDENTS ================= */

    $insertStudent = $conn->prepare("
    INSERT INTO students
    (student_id, user_id, name, email, phone, hostel_id, room_id)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $insertStudent->bind_param(
        "sisssii",
        $next_student_id,
        $new_user_id,
        $name,
        $email,
        $phone,
        $vacant_hostel_id,
        $vacant_room_id
    );

    $insertStudent->execute();
}
/* ================= SEND WAITLIST PROMOTION MAIL ================= */




/* GET HOSTEL NAME */

$hostelQuery = $conn->query("
SELECT hostel_name
FROM hostels
WHERE hostel_id = $vacant_hostel_id
");

$hostelData = $hostelQuery->fetch_assoc();

$hostel_name = $hostelData['hostel_name'];


/* EMAIL CONTENT */

$subject = "Hostel Allotment Update";

$message = "
Dear $name,<br><br>

Congratulations! You have now been allotted a hostel room from the waitlist.<br><br>

<b>Hostel:</b> $hostel_name <br>
<b>Room Number:</b> $vacant_room_id <br><br>

Your hostel portal account has been activated. Please log in to view your allotment details and confirm your allotment.<br><br>

Regards,<br>
Aruvi Hostels Administration
";


/* SEND MAIL */

sendMail($email, $subject, $message);

/* ================= LOGOUT ================= */

session_destroy();


/* ================= REDIRECT ================= */

header("Location: ../index.php");
exit;

?>