<?php
session_start();
include '../database/db_connect.php';

$request_id = $_POST['request_id'];
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

/* GET STUDENT */
$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id FROM students WHERE user_id='$user_id'
"));

$student_id = $student['student_id'];

/* GET REQUEST */
$req = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM room_swap_requests WHERE id='$request_id'
"));

/* ================= ACCEPT ================= */
if($action == "accept"){

    if($req['student_a_id'] == $student_id){
        mysqli_query($conn,"
        UPDATE room_swap_requests SET a_status='accepted' WHERE id='$request_id'
        ");
    }

    if($req['student_b_id'] == $student_id){
        mysqli_query($conn,"
        UPDATE room_swap_requests SET b_status='accepted' WHERE id='$request_id'
        ");
    }

    /* REFETCH UPDATED DATA */
    $updated = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM room_swap_requests WHERE id='$request_id'
    "));

    /* BOTH ACCEPTED → NOTIFY WARDEN */
    if($updated['a_status'] == 'accepted' && $updated['b_status'] == 'accepted'){

        $warden = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT user_id FROM wardens WHERE hostel_id='{$updated['hostel_id']}'
        "));

        $warden_id = $warden['user_id'];

        mysqli_query($conn,"
        INSERT INTO notifications (user_id, hostel_id, title, message, type, reference_id, isRead, created_at)
        VALUES (
        '$warden_id',
        '{$updated['hostel_id']}',
        'Room Swap Approval Needed',
        'Two students have requested a room swap. Please review.',
        'room_swap_warden',
        '$request_id',
        0,
        NOW()
        )
        ");
    }

}

/* ================= REJECT ================= */
if($action == "reject"){

    mysqli_query($conn,"
    UPDATE room_swap_requests 
    SET request_status='rejected' 
    WHERE id='$request_id'
    ");

    /* FIND OTHER STUDENT */
    $other_id = ($req['student_a_id'] == $student_id)
        ? $req['student_b_id']
        : $req['student_a_id'];

    $other = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT user_id FROM students WHERE student_id='$other_id'
    "));

    mysqli_query($conn,"
    INSERT INTO notifications (user_id, hostel_id, title, message, type, reference_id, isRead, created_at)
    VALUES (
    '{$other['user_id']}',
    '{$req['hostel_id']}',
    'Room Swap Rejected',
    'The other student has rejected the room swap request.',
    'room_swap',
    '$request_id',
    0,
    NOW()
    )
    ");

}

echo "done";
?>