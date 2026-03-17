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

h2{
margin-bottom:25px;
}

.notification-card{
background:white;
padding:18px;
border-radius:8px;
margin-bottom:15px;
border:1px solid #ddd;
box-shadow:0 2px 5px rgba(0,0,0,0.05);
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

.reviewed{
background:gray;
cursor:not-allowed;
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
padding:8px;
}

.section{
margin-top:15px;
padding-top:10px;
border-top:1px solid #eee;
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

<?php
$req = $conn->prepare("
SELECT admin_status
FROM guest_requests
WHERE id = ?
");

$req->bind_param("i",$row['reference_id']);
$req->execute();
$r = $req->get_result()->fetch_assoc();

$isReviewed = $r && ($r['admin_status'] == 'approved' || $r['admin_status'] == 'rejected');
?>

<button
class="review-btn <?= $isReviewed ? 'reviewed' : '' ?>"
<?= $isReviewed ? 'disabled' : '' ?>
onclick="openGuestModal('<?= $row['reference_id'] ?>')">

<?= $isReviewed ? 'Reviewed' : 'Review' ?>

</button>

<?php endif; ?>

<div class="notification-time">
<?= date("d M Y H:i",strtotime($row['created_at'])) ?>
</div>

</div>

<?php endwhile; ?>



<div id="guestModal" class="modal">

<div class="modal-content">

<span class="close-btn" onclick="closeModal()">&times;</span>

<h3>Guest Request Review</h3>

<div id="guestDetails"></div>

<input type="hidden" id="modalRequestId">

<div id="normalActions" class="section">

<button type="button" class="approve" onclick="approveFinal()">Approve</button>

<button type="button" class="reject" onclick="showRejectBox()">Reject</button>

</div>

<div id="wardenRejectActions" class="section" style="display:none">

<button type="button" class="approve" onclick="acceptWardenDecision()">Approve Warden Decision</button>

<button type="button" class="reject" onclick="showOverrideBox()">Override Decision</button>

</div>

<div id="rejectBox" class="section" style="display:none">

<label>Reject Reason:</label>
<textarea id="rejectReason"></textarea>

<br><br>

<button type="button" class="reject" onclick="submitReject()">Submit</button>

</div>

<div id="overrideBox" class="section" style="display:none">

<label>Reason for Override:</label>
<textarea id="overrideReason"></textarea>

<br><br>

<button type="button" class="approve" onclick="overrideApprove()">Approve Student</button>
<button type="button" class="reject" onclick="overrideReject()">Reject Student</button>

</div>

</div>

</div>



<script>

let currentRequest = null;

function openGuestModal(id){

currentRequest = id;

fetch("fetch_guest_request_admin.php?id=" + id)
.then(res => res.text())
.then(text => {

let data = JSON.parse(text);

if(data.warden_status === "rejected"){
document.getElementById("normalActions").style.display = "none";
document.getElementById("wardenRejectActions").style.display = "block";
}else{
document.getElementById("normalActions").style.display = "block";
document.getElementById("wardenRejectActions").style.display = "none";
}

document.getElementById("guestDetails").innerHTML=`
<p><b>Name:</b> ${data.guest_name}</p>
<p><b>Email:</b> ${data.guest_email}</p>
<p><b>Phone:</b> ${data.guest_phone}</p>
<p><b>Room:</b> ${data.room_number}</p>
<p><b>Stay From:</b> ${data.stay_from}</p>
<p><b>Stay To:</b> ${data.stay_to}</p>
<p><b>Warden:</b> ${data.warden_name}</p>
<p><b>Warden Remark:</b> ${data.warden_remarks ?? "None"}</p>
`;

document.getElementById("guestModal").style.display="block";

});
}

function closeModal(){
document.getElementById("guestModal").style.display="none";
}

function processAdmin(action, reason){

fetch("process_admin_guest_review.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`request_id=${currentRequest}&action=${action}&reason=${reason}`
})
.then(res => res.text())
.then(data => {
alert("Action completed successfully");
location.reload();
});
}

function approveFinal(){
processAdmin("approved","");
}

function showRejectBox(){
document.getElementById("rejectBox").style.display="block";
}

function submitReject(){
let reason = document.getElementById("rejectReason").value;
if(!reason){ alert("Please enter reason"); return; }
processAdmin("rejected", reason);
}

function acceptWardenDecision(){
processAdmin("accept_warden","");
}

function showOverrideBox(){
document.getElementById("overrideBox").style.display="block";
}

function overrideApprove(){
let reason = document.getElementById("overrideReason").value;
if(!reason){ alert("Enter reason"); return; }
processAdmin("override_approve", reason);
}

function overrideReject(){
let reason = document.getElementById("overrideReason").value;
if(!reason){ alert("Enter reason"); return; }
processAdmin("override_reject", reason);
}

</script>

</body>
</html>