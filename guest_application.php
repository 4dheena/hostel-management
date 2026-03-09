<?php

$conn = mysqli_connect("localhost","root","","hostel_management");

$guest_name = $_POST['guestName'];
$guest_id = $_POST['guestID'];
$room_number = $_POST['room'];

# get room id
$room_query = mysqli_query($conn,"SELECT room_id FROM rooms WHERE room_number='$room_number'");
$room = mysqli_fetch_assoc($room_query);
$room_id = $room['room_id'];

# insert guest request
mysqli_query($conn,"
INSERT INTO guest_requests(guest_name,guest_student_id,room_id)
VALUES('$guest_name','$guest_id','$room_id')
");

$request_id = mysqli_insert_id($conn);

# find inmates of that room
$students = mysqli_query($conn,"
SELECT student_id FROM students WHERE room_id='$room_id'
");

while($row = mysqli_fetch_assoc($students)){

$student_id = $row['student_id'];

mysqli_query($conn,"
INSERT INTO inmate_approvals(request_id,student_id)
VALUES('$request_id','$student_id')
");

}

echo "Request sent to inmates successfully";

?>