<?php
session_start();
require_once '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

/* FETCH NOTIFICATIONS */
$stmt = $conn->prepare("
SELECT notifications.*, guest_requests.inmate_status ,vacate_requests.request_status,room_swap_requests.a_status, room_swap_requests.b_status,room_swap_requests.student_a_id, room_swap_requests.student_b_id,students.student_id
FROM notifications
LEFT JOIN guest_requests
ON notifications.reference_id = guest_requests.id
LEFT JOIN vacate_requests
ON notifications.reference_id = vacate_requests.id
LEFT JOIN room_swap_requests
ON notifications.reference_id = room_swap_requests.id
LEFT JOIN students
ON notifications.user_id = students.user_id
WHERE notifications.user_id = ?
ORDER BY notifications.created_at DESC
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
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
margin-top:10px;
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

<h2>Notifications</h2>

<?php if($result->num_rows == 0): ?>
<p>No notifications.</p>
<?php endif; ?>

<?php while($row = $result->fetch_assoc()): ?>

<div class="notification-card">

<h4><?= htmlspecialchars($row['title']); ?></h4>
<p><?= htmlspecialchars($row['message']); ?></p>

<!-- 🔹 GUEST -->
<?php if($row['type'] == 'guest_request'): ?>

<?php if(
strtolower($row['inmate_status']) == 'approved' ||
strtolower($row['inmate_status']) == 'rejected'
): ?>

<button class="reviewed" disabled>
Reviewed
</button>

<?php else: ?>

<button
class="review-btn"
data-request="<?= $row['reference_id']; ?>"
onclick="openGuestModal(<?= $row['reference_id']; ?>, this)">
Review
</button>

<?php endif; ?>

<?php endif; ?>

<!-- 🔹 ROOM SWAP -->
<?php if($row['type'] == 'room_swap'): ?>

<!-- CURRENT USER IS A -->
<?php if(trim($row['student_id']) == trim($row['student_a_id'])): ?>

    <?php if(
    strtolower(trim($row['a_status'])) == 'accepted' ||
    strtolower(trim($row['a_status'])) == 'rejected'
    ): ?>

        <button class="reviewed" disabled>
        Reviewed
        </button>

    <?php else: ?>

        <button
        class="review-btn"
        onclick="openSwapModal(<?= $row['reference_id']; ?>, this)">
        Inspect
        </button>

    <?php endif; ?>


<!-- CURRENT USER IS B -->
<?php elseif(trim($row['student_id']) == trim($row['student_b_id'])): ?>

    <?php if(
    strtolower(trim($row['b_status'])) == 'accepted' ||
    strtolower(trim($row['b_status'])) == 'rejected'
    ): ?>

        <button class="reviewed" disabled>
        Reviewed
        </button>

    <?php else: ?>

        <button
        class="review-btn"
        onclick="openSwapModal(<?= $row['reference_id']; ?>, this)">
        Inspect
        </button>

    <?php endif; ?>

<?php endif; ?>

<?php endif; ?>

<!-- 🔹 VACATE -->
<?php if($row['type'] == 'vacate_request'): ?>

<?php if(
strtolower($row['request_status']) == 'confirmed'
): ?>

<button class="reviewed" disabled>
Reviewed
</button>    
<?php else: ?>
<button
class="review-btn"
onclick="openVacateModal(<?= $row['reference_id']; ?>, this)">
Confirm
</button>
<?php endif; ?>
<?php endif; ?>

</div>

<?php endwhile; ?>

<!-- ================= GUEST MODAL ================= -->
<div id="guestModal" class="modal">
<div class="modal-content">
<span class="close-btn" onclick="closeGuestModal()">&times;</span>

<h3>Guest Request Details</h3>
<div id="guestDetails"></div>

<br>

<button onclick="approveGuest()" class="approve">Approve</button>
<button onclick="rejectGuest()" class="reject">Reject</button>

</div>
</div>

<!-- ================= SWAP MODAL ================= -->
<div id="swapModal" class="modal">
<div class="modal-content">
<span class="close-btn" onclick="closeSwapModal()">&times;</span>

<h3>Room Swap Request</h3>
<div id="swapDetails"></div>

<br>

<button onclick="acceptSwap()" class="approve">Accept</button>
<button onclick="rejectSwap()" class="reject">Reject</button>

</div>
</div>

<!-- ================= VACATE MODAL ================= -->
<div id="vacateModal" class="modal">
<div class="modal-content">

<span class="close-btn" onclick="closeVacateModal()">&times;</span>

<h3>Vacate Request Confirmation</h3>

<p>Did you submit this vacate request?</p>

<br>

<button onclick="confirmVacate()" class="approve">Yes</button>
<button onclick="rejectVacate()" class="reject">No</button>

</div>
</div>

<script>

/* ================= VACATE ================= */

let currentVacate = null;
let currentVacateBtn = null;

function openVacateModal(id, btn){
currentVacate = id;
currentVacateBtn = btn;
document.getElementById("vacateModal").style.display = "block";
}

function closeVacateModal(){
document.getElementById("vacateModal").style.display="none";
}

function confirmVacate(){
processVacate("confirm");
}

function rejectVacate(){
processVacate("reject");
}

function processVacate(action){

fetch("../vacate/respond_vacate.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`request_id=${currentVacate}&action=${action}`
})
.then(res => res.text())
.then(() => {

if(currentVacateBtn){
currentVacateBtn.textContent = "Reviewed";
currentVacateBtn.classList.remove("review-btn");
currentVacateBtn.classList.add("reviewed");
currentVacateBtn.disabled = true;
}

closeVacateModal();

});

}

let currentRequest = null;
let currentButton = null;

function openGuestModal(requestId, button){

currentRequest = requestId;
currentButton = button;

fetch("../guest_module/fetch_guest_request.php?id=" + requestId)
.then(res => res.json())
.then(data => {

document.getElementById("guestDetails").innerHTML = `
<p><b>Name:</b> ${data.guest_name}</p>
<p><b>Email:</b> ${data.guest_email}</p>
<p><b>Phone:</b> ${data.guest_phone}</p>
<p><b>Stay From:</b> ${data.stay_from}</p>
<p><b>Stay To:</b> ${data.stay_to}</p>
<p><b>Message:</b></p>
<div style="white-space:pre-wrap;word-break:break-word;background:#f4f6f9;padding:10px;border-radius:6px;">${data.request_message}</div>
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

fetch("../guest_module/process_guest_review.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`request_id=${currentRequest}&decision=${decision}`
})
.then(res => res.text())
.then(data => {

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


let currentSwap = null;
let currentSwapBtn = null;

function openSwapModal(id, button){

currentSwap = id;
currentSwapBtn = button;

fetch("../room_swap/fetch_swap.php?id=" + id)
.then(res => res.json())
.then(data => {

document.getElementById("swapDetails").innerHTML = `
<p><b>Student A:</b> ${data.student_a}</p>
<p><b>Room A:</b> ${data.room_a}</p>
<p><b>Student B:</b> ${data.student_b}</p>
<p><b>Room B:</b> ${data.room_b}</p>
<p><b>Reason:</b> ${data.reason}</p>
`;

document.getElementById("swapModal").style.display="block";

});

}

function closeSwapModal(){
document.getElementById("swapModal").style.display="none";
}

function acceptSwap(){
processSwap("accept");
}

function rejectSwap(){
processSwap("reject");
}

function processSwap(action){

fetch("../room_swap/respond_swap.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`request_id=${currentSwap}&action=${action}`
})
.then(res => res.text())
.then(data => {

/* Update button UI */
if(currentSwapBtn){
currentSwapBtn.textContent = "Reviewed";
currentSwapBtn.classList.remove("review-btn");
currentSwapBtn.classList.add("reviewed");
currentSwapBtn.disabled = true;
}

/* Close modal */
closeSwapModal();

/* Optional message */
alert("Response submitted");

});

}




</script>

</body>
</html>