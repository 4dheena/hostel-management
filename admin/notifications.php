<?php

session_start();
require_once "../database/db_connect.php";

/* check login */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
header("Location: ../index.php");
exit();
}

$user_id = $_SESSION['user_id'];

/* fetch notifications */

$stmt = $conn->prepare("
SELECT *
FROM notifications
WHERE user_id = ?
ORDER BY created_at DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$notifications = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Notifications</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:40px;
}

h2{
margin-bottom:30px;
}

.notification-card{
background:white;
border:1px solid #ddd;
padding:20px;
margin-bottom:15px;
border-radius:8px;
box-shadow:0 2px 5px rgba(0,0,0,0.05);
}

.notification-title{
font-weight:bold;
margin-bottom:5px;
}

.notification-time{
font-size:12px;
color:gray;
margin-top:6px;
}

button{
padding:6px 12px;
border:none;
border-radius:4px;
cursor:pointer;
}

.review-btn{
background:#007bff;
color:white;
}

.approve{
background:#28a745;
color:white;
}

.reject{
background:#dc3545;
color:white;
}

/* MODAL */

.modal{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
}

.modal-content{
background:white;
padding:20px;
width:500px;
margin:8% auto;
border-radius:8px;
position:relative;
}

.close-btn{
position:absolute;
right:15px;
top:10px;
font-size:22px;
cursor:pointer;
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

<h2>Notifications</h2>

<?php if($notifications->num_rows == 0): ?>

<p>No notifications available.</p>

<?php endif; ?>


<?php while($row = $notifications->fetch_assoc()): ?>

<div class="notification-card">

<div class="notification-title">

<?= htmlspecialchars($row['title']) ?>

</div>

<div>

<?= htmlspecialchars($row['message']) ?>

</div>


<?php if($row['type'] == "guest_admin_review"): ?>

<br>

<button 
class="review-btn"
onclick="openGuestModal(<?= $row['reference_id']?>)">
Review
</button>

<?php endif; ?>


<div class="notification-time">

<?= date("d M Y H:i",strtotime($row['created_at'])) ?>

</div>

</div>

<?php endwhile; ?>


<!-- MODAL -->

<div id="guestModal" class="modal">

<div class="modal-content">

<span class="close-btn" onclick="closeModal()">&times;</span>

<h3>Guest Request Review</h3>

<div id="guestDetails"></div>

<form action="process_admin_guest_review.php" method="POST">

<input type="hidden" name="request_id" id="modalRequestId">

<br>

<button class="approve" name="decision" value="approved">
Approve
</button>

<button type="button" class="reject" onclick="showReject()">
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

</div>


<script>

function openGuestModal(id){

fetch("../guest_module/fetch_guest_request.php?id="+id)
.then(res=>res.json())
.then(data=>{

document.getElementById("modalRequestId").value=id;

document.getElementById("guestDetails").innerHTML=`
<p><b>Name:</b> ${data.guest_name}</p>
<p><b>Email:</b> ${data.guest_email}</p>
<p><b>Phone:</b> ${data.guest_phone}</p>
<p><b>Room:</b> ${data.room_number}</p>
<p><b>Stay From:</b> ${data.stay_from}</p>
<p><b>Stay To:</b> ${data.stay_to}</p>
<p><b>Message:</b> ${data.request_message}</p>
<p><b>Warden Remark:</b> ${data.warden_remark ?? "None"}</p>
`;

document.getElementById("guestModal").style.display="block";

});

}

function closeModal(){
document.getElementById("guestModal").style.display="none";
}

function showReject(){
document.getElementById("rejectBox").style.display="block";
}

</script>

</body>
</html>