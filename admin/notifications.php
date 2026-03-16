<?php
session_start();
require_once "../database/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
header("Location: ../index.php");
exit();
}

$user_id = $_SESSION['user_id'];

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

.notification-card{
background:white;
padding:18px;
border-radius:8px;
margin-bottom:15px;
border:1px solid #ddd;
}

.notification-title{
font-weight:bold;
margin-bottom:6px;
}

.notification-time{
font-size:12px;
color:gray;
margin-top:6px;
}

.review-btn{
background:#007bff;
color:white;
border:none;
padding:6px 12px;
border-radius:4px;
cursor:pointer;
}

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
top:10px;
right:15px;
font-size:22px;
cursor:pointer;
}

.approve{
background:#28a745;
color:white;
padding:8px 16px;
border:none;
border-radius:4px;
cursor:pointer;
}

.reject{
background:#dc3545;
color:white;
padding:8px 16px;
border:none;
border-radius:4px;
cursor:pointer;
}

textarea{
width:100%;
height:80px;
margin-top:10px;
}

</style>

</head>

<body>

<h2>Notifications</h2>

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

fetch("fetch_guest_request.php?id="+id)
.then(res=>res.json())
.then(data=>{

document.getElementById("modalRequestId").value=id;

document.getElementById("guestDetails").innerHTML=`

<p><b>Guest Name:</b> ${data.guest_name}</p>

<p><b>Email:</b> ${data.guest_email}</p>

<p><b>Phone:</b> ${data.guest_phone}</p>

<p><b>Room:</b> ${data.room_number}</p>

<p><b>Stay From:</b> ${data.stay_from}</p>

<p><b>Stay To:</b> ${data.stay_to}</p>

<p><b>Warden:</b> ${data.warden_name}</p>

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