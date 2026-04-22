<?php

session_start();
require_once '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

/* AUTO REJECT REQUESTS OLDER THAN 7 DAYS */

$conn->query("
UPDATE guest_requests
SET overall_status='rejected',
    inmate_status='rejected'
WHERE overall_status='inmate_review'
AND submitted_at <= NOW() - INTERVAL 7 DAY
");

/* FETCH GUEST REQUEST NOTIFICATIONS */

$stmt = $conn->prepare("
SELECT DISTINCT n.reference_id, n.title, n.message,
gra.approval_status
FROM notifications n
LEFT JOIN guest_roommate_approvals gra 
ON gra.request_id = n.reference_id
LEFT JOIN students s 
ON s.student_id = gra.student_id
WHERE n.user_id = ?
AND n.type = 'guest_request'
AND s.user_id = ?
ORDER BY n.reference_id DESC
");

$stmt->bind_param("ii",$user_id,$user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>

<title>Guest Notifications</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:40px;
}

.notification-card{
background:white;
padding:15px;
margin-bottom:15px;
border-radius:8px;
box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

button{
padding:8px 14px;
border:none;
border-radius:5px;
cursor:pointer;
}

.review-btn{
background:#007bff;
color:white;
}

.reviewed{
background:#6c757d;
color:white;
cursor:not-allowed;
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
width:400px;
margin:10% auto;
border-radius:8px;
position:relative;
}

.close-btn{
position:absolute;
top:10px;
right:15px;
font-size:24px;
cursor:pointer;
font-weight:bold;
}

.close-btn:hover{
color:red;
}

.approve{
background:#28a745;
color:white;
}

.reject{
background:#dc3545;
color:white;
}

</style>

</head>

<body>

<h2>Guest Stay Requests</h2>

<?php if($result->num_rows == 0): ?>
<p>No guest requests.</p>
<?php endif; ?>

<?php while($row = $result->fetch_assoc()): ?>

<div class="notification-card">

<h4><?= htmlspecialchars($row['title']); ?></h4>
<p><?= htmlspecialchars($row['message']); ?></p>

<?php if($row['approval_status'] == 'pending' || $row['approval_status'] == NULL): ?>

<button
class="review-btn"
data-request="<?= $row['reference_id']; ?>"
onclick="openGuestModal(<?= $row['reference_id']; ?>, this)">
Review
</button>

<?php else: ?>

<button class="reviewed" disabled>
Reviewed
</button>

<?php endif; ?>

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
let currentButton = null;

function openGuestModal(requestId,button){

currentRequest = requestId;
currentButton = button;

fetch("fetch_guest_request.php?id=" + requestId)
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
processDecision("approved");
}

function rejectGuest(){
processDecision("rejected");
}

function processDecision(decision){

fetch("process_guest_review.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`request_id=${currentRequest}&decision=${decision}`
})
.then(res => res.text())
.then(data => {

alert("Review submitted successfully.");

if(currentButton){
currentButton.textContent = "Reviewed";
currentButton.classList.remove("review-btn");
currentButton.classList.add("reviewed");
currentButton.disabled = true;
}

closeModal();

});

}

window.onclick = function(event){

let modal = document.getElementById("guestModal");

if(event.target == modal){
modal.style.display="none";
}

}

</script>

</body>
</html>