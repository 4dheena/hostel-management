<?php
session_start();
require_once "../database/db_connect.php";

/* check login */
if (!isset($_SESSION['user_id'])) {
header("Location: ../index.php");
exit();
}

$user_id = $_SESSION['user_id'];

/* get hostel id */

$stmt = $conn->prepare("
SELECT hostel_id
FROM students
WHERE user_id = ?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$hostel_id = $student['hostel_id'];


/* fetch notifications */
/* fetch notifications */

$query = $conn->prepare("
SELECT 
MAX(id) AS id,
MAX(title) AS title,
MAX(message) AS message,
type,
reference_id,
MAX(created_at) AS created_at,
MAX(is_read) AS is_read
FROM notifications
WHERE user_id = ?
GROUP BY reference_id, type
ORDER BY created_at DESC
");

$query->bind_param("i",$user_id);
$query->execute();

$notifications = $query->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Notifications</title>

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

.unread{
border-left:5px solid #007bff;
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

.review-btn{
margin-top:10px;
padding:6px 12px;
background:#007bff;
color:white;
border:none;
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
width:420px;
margin:10% auto;
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
border:none;
padding:8px 14px;
margin-right:10px;
border-radius:4px;
}

.reject{
background:#dc3545;
color:white;
border:none;
padding:8px 14px;
border-radius:4px;
}

</style>

</head>

<body>

<h2>Notifications</h2>

<?php if($notifications->num_rows == 0): ?>

<p>No notifications available.</p>

<?php endif; ?>


<?php while($row = $notifications->fetch_assoc()): ?>

<div class="notification-card <?= $row['is_read'] == 0 ? 'unread' : '' ?>">

<div class="notification-title">

<?= htmlspecialchars($row['title']) ?>

</div>

<div>

<?= htmlspecialchars($row['message']) ?>

</div>

<?php if($row['type'] == 'guest_request'): ?>

<button class="review-btn"
onclick="openGuestModal(<?= $row['reference_id'] ?>)">
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

<h3>Guest Request Details</h3>

<div id="guestDetails"></div>

<br>

<button onclick="approveGuest()" class="approve">Approve</button>
<button onclick="rejectGuest()" class="reject">Reject</button>

</div>

</div>


<script>

let currentRequest = null;

function openGuestModal(requestId){

currentRequest = requestId;

fetch("../guest_module/fetch_guest_request.php?id=" + requestId)
.then(res => res.json())
.then(data => {

document.getElementById("guestDetails").innerHTML = `
<p><b>Name:</b> ${data.guest_name}</p>
<p><b>Email:</b> ${data.guest_email}</p>
<p><b>Phone:</b> ${data.guest_phone}</p>
<p><b>Message:</b> ${data.request_message}</p>
`;

document.getElementById("guestModal").style.display="block";

});

}

function closeModal(){
document.getElementById("guestModal").style.display="none";
}

function approveGuest(){
alert("Approving guest request...");
processDecision("approved");
}

function rejectGuest(){
alert("Rejecting guest request...");
processDecision("rejected");
}

function processDecision(decision){
alert("Submitting your decision...");
fetch("../guest_module/process_guest_review.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`request_id=${currentRequest}&decision=${decision}`
})
.then(res => res.text())
.then(data => {

console.log("Server response:", data);

alert("Review submitted successfully.");

if(currentButton){
currentButton.textContent = "Reviewed";
currentButton.disabled = true;
}

closeModal();

})
.catch(error=>{
console.error("Fetch error:", error);
alert("Something went wrong while submitting review.");
});

}

</script>

</body>
</html>