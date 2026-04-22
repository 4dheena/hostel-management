<?php

session_start();
require_once '../database/db_connect.php';

if(!isset($_SESSION['user_id'])){
header("Location: ../index.php");
exit;
}

if(!isset($_GET['id'])){
echo "Invalid request";
exit;
}

$request_id = $_GET['id'];

/* FETCH REQUEST */

$stmt = $conn->prepare("
SELECT *
FROM guest_requests
WHERE id = ?
");

$stmt->bind_param("i",$request_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
echo "Request not found";
exit;
}

$request = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>

<title>Guest Request Details</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:40px;
}

.container{
background:white;
padding:30px;
border-radius:8px;
width:600px;
margin:auto;
box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

h2{
margin-bottom:20px;
}

.detail{
margin-bottom:12px;
}

label{
font-weight:bold;
}

button{
padding:10px 18px;
border:none;
border-radius:5px;
cursor:pointer;
margin-right:10px;
}

.approve{
background:#28a745;
color:white;
}

.reject{
background:#dc3545;
color:white;
}

textarea{
width:100%;
height:80px;
margin-top:10px;
padding:8px;
}

</style>

</head>

<body>

<div class="container">

<h2>Guest Request Details</h2>

<div class="detail">
<label>Guest Name:</label>
<?= htmlspecialchars($request['guest_name']) ?>
</div>

<div class="detail">
<label>Student ID:</label>
<?= htmlspecialchars($request['guest_student_id']) ?>
</div>

<div class="detail">
<label>Email:</label>
<?= htmlspecialchars($request['guest_email']) ?>
</div>

<div class="detail">
<label>Phone:</label>
<?= htmlspecialchars($request['guest_phone']) ?>
</div>

<div class="detail">
<label>Room:</label>
<?= htmlspecialchars($request['room_number']) ?>
</div>

<div class="detail">
<label>Stay From:</label>
<?= htmlspecialchars($request['stay_from']) ?>
</div>

<div class="detail">
<label>Stay To:</label>
<?= htmlspecialchars($request['stay_to']) ?>
</div>

<div class="detail">
<label>Message:</label>
<?= htmlspecialchars($request['request_message']) ?>
</div>

<form action="process_guest_verification.php" method="POST">

<input type="hidden" name="request_id" value="<?= $request_id ?>">

<br>

<button class="approve" name="decision" value="approved">
Approve
</button>

<button class="reject" type="button" onclick="showReject()">
Reject
</button>

<div id="rejectBox" style="display:none">

<label>Reject Reason:</label>

<textarea name="reject_reason"></textarea>

<br><br>

<button class="reject" name="decision" value="rejected">
Submit Rejection
</button>

</div>

</form>

</div>

<script>

function showReject(){
document.getElementById("rejectBox").style.display="block";
}

</script>

</body>
</html>