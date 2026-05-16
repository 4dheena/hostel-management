
<?php
session_start();
include '../database/db_connect.php';
require_once '../utils/send_email.php';

$id = $_GET['id'];
$action = $_GET['action'];
$reason = $_GET['reason'] ?? '';

/* 🔹 GET REQUEST */
$req = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM vacate_requests 
WHERE id='$id'
"));

if(!$req){
    exit("Invalid request");
}

/* 🔹 GET STUDENT */
$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM students 
WHERE student_id='{$req['student_id']}'
"));

if(!$student){
    exit("Student not found");
}

/* ================= APPROVE ================= */

if($action == "approve"){

    /* UPDATE REQUEST */
    mysqli_query($conn,"
    UPDATE vacate_requests 
    SET 
        warden_status='approved',
        request_status='completed',
        delete_after = DATE_ADD(NOW(), INTERVAL 10 DAY)
    WHERE id='$id'
    ");

    /* REMOVE ROOM */
    mysqli_query($conn,"
    UPDATE students 
    SET room_id=NULL
    WHERE student_id='{$student['student_id']}'
    ");

    /* DISABLE LOGIN */
    mysqli_query($conn,"
    UPDATE users 
SET status='inactive'
WHERE user_id='{$student['user_id']}'
    ");

    /* 🔔 NOTIFICATION */
    $msg_db = "Your vacate request has been approved. Please vacate within 10 days.";

    mysqli_query($conn,"
    INSERT INTO notifications 
    (
        user_id,
        hostel_id,
        title,
        message,
        type,
        reference_id,
        is_read,
        created_at
    )
    VALUES (
        '{$student['user_id']}',
        '{$req['hostel_id']}',
        'Vacate Request Approved',
        '$msg_db',
        'vacate',
        '$id',
        0,
        NOW()
    )
    ");

    /* 📧 EMAIL */
    $subject = "Vacate Request Approved – Hostel Management";

    $message = "
    <h2>Vacate Request Approved ✅</h2>

    <p>Dear Student,</p>

    <p>
    We would like to inform you that your 
    <b>vacate request</b> has been successfully 
    reviewed and approved by the hostel warden.
    </p>

    <p><b>Important Instructions:</b></p>

    <ul>
        <li>
        You are required to vacate your assigned 
        room within <b>10 days</b>.
        </li>

        <li>
        Please ensure all belongings are removed.
        </li>

        <li>
        Kindly return hostel property 
        (keys, ID cards, etc.)
        </li>

        <li>
        Clear any pending dues if applicable.
        </li>
    </ul>

    <p>
    Your access to the hostel system has now 
    been restricted and your account may be 
    removed after the vacate period.
    </p>

    <p>
    If you have any concerns, please contact 
    the hostel administration.
    </p>

    <br>

    <p>
    Wishing you all the best for your future.
    </p>

    <p>
    Regards,<br>
    Hostel Management System
    </p>
    ";

    sendEmail($student['email'], $subject, $message);


header("Location: ../warden/requests.php");
exit();
}

/* ================= REJECT ================= */

if($action == "reject"){

    mysqli_query($conn,"
    UPDATE vacate_requests 
    SET 
        warden_status='rejected',
        rejection_reason='$reason'
    WHERE id='$id'
    ");

    /* 🔔 NOTIFICATION */
    $msg_db = "Your vacate request was rejected. Reason: $reason";

    mysqli_query($conn,"
    INSERT INTO notifications 
    (
        user_id,
        hostel_id,
        title,
        message,
        type,
        reference_id,
        is_read,
        created_at
    )
    VALUES (
        '{$student['user_id']}',
        '{$req['hostel_id']}',
        'Vacate Request Rejected',
        '$msg_db',
        'vacate',
        '$id',
        0,
        NOW()
    )
    ");

    /* 📧 EMAIL */
    $subject = "Vacate Request Rejected – Hostel Management";

    $message = "
    <h2>Vacate Request Rejected ❌</h2>

    <p>Dear Student,</p>

    <p>
    Your request to vacate the hostel has been 
    reviewed by the warden.
    </p>

    <p><b>Status:</b> Rejected</p>

    <p><b>Reason Provided:</b></p>

    <p>$reason</p>

    <p>
    This decision may be due to pending hostel 
    dues, incomplete formalities, or other 
    administrative concerns.
    </p>

    <p>
    You are advised to contact the hostel office 
    for clarification or to resolve pending issues.
    </p>

    <br>

    <p>
    We appreciate your understanding.
    </p>

    <p>
    Regards,<br>
    Hostel Management System
    </p>
    ";

    sendEmail($student['email'], $subject, $message);

echo "
<script>
alert('Vacate request processed successfully');
window.location.href='../warden/requests.php';
</script>
";
exit();
}
?>