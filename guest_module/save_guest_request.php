<?php

require_once '../database/db_connect.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: ../forms.php");
    exit;
}

/* GET FORM DATA */

$guest_student_id = $_POST['guest_student_id'];
$guest_name = $_POST['guest_name'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$hostel_id = $_POST['hostel_id'];
$room_number = $_POST['room_number'];
$stay_from = $_POST['stay_from'];
$stay_to = $_POST['stay_to'];
$request_message = $_POST['request_message'] ?? '';

$email_updates = isset($_POST['email_updates']) ? 1 : 0;

/* HANDLE ID PROOF UPLOAD */

$id_proof_path = NULL;

if(isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] === UPLOAD_ERR_OK){

    $upload_dir = "../uploads/guest_ids/";

    if(!is_dir($upload_dir)){
        mkdir($upload_dir,0777,true);
    }

    $file_tmp = $_FILES['id_proof']['tmp_name'];
    $file_name = $_FILES['id_proof']['name'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    /* ALLOW ONLY PDF */

    if($ext !== "pdf"){
        die("Only PDF files are allowed for ID proof.");
    }

    $new_name = uniqid().".pdf";

    $destination = $upload_dir.$new_name;

    move_uploaded_file($file_tmp,$destination);

    $id_proof_path = "uploads/guest_ids/".$new_name;
}


/* FIND ROOM ID */

$stmt = $conn->prepare("
SELECT room_id
FROM rooms
WHERE room_number = ?
AND hostel_id = ?
");

$stmt->bind_param("si",$room_number,$hostel_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Invalid room number.");
}

$room = $result->fetch_assoc();
$room_id = $room['room_id'];


/* INSERT INTO guest_requests */

$stmt = $conn->prepare("
INSERT INTO guest_requests
(guest_student_id,guest_name,guest_email,guest_phone,gender,hostel_id,room_number,stay_from,stay_to,request_message,email_updates,id_proof_path)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
"sssssissssis",
$guest_student_id,
$guest_name,
$email,
$phone,
$gender,
$hostel_id,
$room_number,
$stay_from,
$stay_to,
$request_message,
$email_updates,
$id_proof_path
);

$stmt->execute();

$request_id = $conn->insert_id;


/* FIND ROOMMATES */

$stmt = $conn->prepare("
SELECT DISTINCT student_id, user_id
FROM students
WHERE room_id = ?
");

$stmt->bind_param("i",$room_id);
$stmt->execute();

$roommates = $stmt->get_result();


/* PREPARE NOTIFICATION MESSAGE */

$title = "Guest Stay Request";
$message = "A student requested to stay in your room. Please review.";


/* INSERT APPROVAL ROWS + SEND NOTIFICATIONS */

while($row = $roommates->fetch_assoc()){

    $student_id = $row['student_id'];
    $user_id = $row['user_id'];

    /* CHECK IF APPROVAL RECORD ALREADY EXISTS */

    $check = $conn->prepare("
    SELECT id
    FROM guest_roommate_approvals
    WHERE request_id=? AND student_id=?
    ");

    $check->bind_param("is",$request_id,$student_id);
    $check->execute();
    $exists = $check->get_result();


    if($exists->num_rows == 0){

        $stmt2 = $conn->prepare("
        INSERT INTO guest_roommate_approvals
        (request_id,student_id)
        VALUES (?,?)
        ");

        $stmt2->bind_param("is",$request_id,$student_id);
        $stmt2->execute();
    }


    /* CHECK IF NOTIFICATION ALREADY EXISTS */

    $check2 = $conn->prepare("
    SELECT id
    FROM notifications
    WHERE user_id=? AND reference_id=? AND type='guest_request'
    ");

    $check2->bind_param("ii",$user_id,$request_id);
    $check2->execute();
    $exists2 = $check2->get_result();


    if($exists2->num_rows == 0){

        $stmt3 = $conn->prepare("
        INSERT INTO notifications
        (user_id,hostel_id,title,message,type,reference_id)
        VALUES (?,?,?,?,?,?)
        ");

        $type = "guest_request";

        $stmt3->bind_param(
            "iisssi",
            $user_id,
            $hostel_id,
            $title,
            $message,
            $type,
            $request_id
        );

        $stmt3->execute();
    }

}


/* SUCCESS */

header("Location: ../forms.php?success=guest_request_submitted");
exit;

?>