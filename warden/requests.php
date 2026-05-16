
<?php
session_start();
require_once '../database/db_connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden'){
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* GET WARDEN HOSTEL */
$stmt = $conn->prepare("
SELECT hostel_id 
FROM wardens 
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$res = $stmt->get_result();
$warden = $res->fetch_assoc();

$hostel_id = $warden['hostel_id'];

/* ================= GUEST REQUESTS ================= */

$stmt_guest = $conn->prepare("
SELECT * FROM guest_requests 
WHERE overall_status='warden_review' 
AND hostel_id=? 
ORDER BY submitted_at DESC
");

$stmt_guest->bind_param("i", $hostel_id);
$stmt_guest->execute();

$guest_result = $stmt_guest->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Requests</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:40px;
}

h2{
margin-bottom:20px;
}

select{
padding:10px;
border-radius:8px;
margin-bottom:25px;
border:1px solid #ccc;
}

table{
width:100%;
border-collapse:collapse;
background:white;
margin-bottom:20px;
border-radius:8px;
overflow:hidden;
}

th,td{
padding:12px;
border:1px solid #ddd;
text-align:center;
}

th{
background:#2c5364;
color:white;
}

button{
padding:8px 14px;
border:none;
border-radius:6px;
cursor:pointer;
margin:5px;
}

.view-btn{
background:#007bff;
color:white;
}

.reviewed{
background:#6c757d;
color:white;
cursor:not-allowed;
}

.approve{
background:#28a745;
color:white;
}

.reject{
background:#dc3545;
color:white;
}

button:hover{
opacity:0.9;
}

.notification-card{
background:white;
padding:18px;
border-radius:10px;
margin-bottom:15px;
box-shadow:0 2px 6px rgba(0,0,0,0.08);
border-left:5px solid #007bff;
}

.modal{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
z-index:1000;
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

<h2>Requests</h2>

<select id="filterType" onchange="filterRequests()">

<option value="guest">Guest Requests</option>

<option value="swap">Room Swap Requests</option>

<option value="vacate">Vacate Requests</option>

</select>

<!-- ================= GUEST SECTION ================= -->

<div id="guestSection">

<h2>Guest Stay Requests</h2>

<?php if($guest_result->num_rows == 0): ?>

<p>No guest requests pending.</p>

<?php else: ?>

<table>

<tr>
<th>Guest Name</th>
<th>Student ID</th>
<th>Room</th>
<th>Stay</th>
<th>ID Proof</th>
<th>Action</th>
</tr>

<?php while($row = $guest_result->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($row['guest_name']) ?></td>

<td><?= htmlspecialchars($row['guest_student_id']) ?></td>

<td><?= htmlspecialchars($row['room_number']) ?></td>

<td>
<?= $row['stay_from'] ?> → <?= $row['stay_to'] ?>
</td>

<td>

<?php if(!empty($row['id_proof_path'])): ?>

<a href="../<?= $row['id_proof_path'] ?>" target="_blank">
View
</a>

<?php else: ?>

None

<?php endif; ?>

</td>

<td>

<?php if(
$row['inmate_status'] == 'approved' ||
$row['inmate_status'] == 'rejected'
): ?>

<button class="reviewed" disabled>
Reviewed
</button>

<?php else: ?>

<button class="view-btn"
onclick="openGuestModal(<?= $row['id'] ?>)">
Review
</button>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>

</div>

<!-- ================= ROOM SWAP SECTION ================= -->

<div id="swapSection" style="display:none;">

<h2>Room Swap Requests</h2>

<?php

$swap_query = "
SELECT * FROM room_swap_requests 
WHERE hostel_id='$hostel_id' 
AND a_status='accepted' 
AND b_status='accepted' 
ORDER BY id DESC
";

$swap_res = mysqli_query($conn, $swap_query);

if(mysqli_num_rows($swap_res) == 0){
echo "<p>No room swap requests pending.</p>";
}

while($srow = mysqli_fetch_assoc($swap_res)):

$res_a = mysqli_query($conn,"
SELECT name 
FROM students 
WHERE student_id='{$srow['student_a_id']}'
");

$res_b = mysqli_query($conn,"
SELECT name 
FROM students 
WHERE student_id='{$srow['student_b_id']}'
");

$a_data = mysqli_fetch_assoc($res_a);
$b_data = mysqli_fetch_assoc($res_b);

?>

<div class="notification-card">

<h4>Swap Request #<?= $srow['id'] ?></h4>

<p>

<b><?= $a_data['name'] ?></b>
(Room <?= $srow['room_a'] ?>)

<br>⬇⬆<br>

<b><?= $b_data['name'] ?></b>
(Room <?= $srow['room_b'] ?>)

</p>

<p>
<b>Reason:</b>
<?= htmlspecialchars($srow['reason']) ?>
</p>

<?php if(
$srow['warden_status'] == 'approved' ||
$srow['warden_status'] == 'rejected'
): ?>

<button class="reviewed" disabled>
Reviewed
</button>

<?php else: ?>

<button onclick="approveSwap(<?= $srow['id'] ?>)"
class="approve">
Approve
</button>

<button onclick="rejectSwap(<?= $srow['id'] ?>)"
class="reject">
Reject
</button>

<?php endif; ?>

</div>

<?php endwhile; ?>

</div>

<!-- ================= VACATE SECTION ================= -->

<div id="vacateSection" style="display:none;">

<h2>Vacate Requests</h2>

<?php

$vacate = mysqli_query($conn,"
SELECT * FROM vacate_requests
WHERE hostel_id='$hostel_id'
ORDER BY id DESC
");

if(mysqli_num_rows($vacate) == 0){
echo "<p>No vacate requests.</p>";
}

while($row = mysqli_fetch_assoc($vacate)):

$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT name 
FROM students 
WHERE student_id='{$row['student_id']}'
"));

?>

<div class="notification-card">

<p>
<b><?= $student['name']; ?></b>
wants to vacate room
</p>

<p>
<b>Vacate Date:</b>
<?= $row['vacate_date']; ?>
</p>

<p>
<b>Reason:</b>
<?= $row['reason']; ?>
</p>

<?php if(
$row['warden_status'] == 'approved' ||
$row['warden_status'] == 'rejected'
): ?>

<button class="reviewed" disabled>
Reviewed
</button>

<?php else: ?>

<button onclick="approveVacate(<?= $row['id']; ?>)"
class="approve">
Approve
</button>

<button onclick="rejectVacate(<?= $row['id']; ?>)"
class="reject">
Reject
</button>

<?php endif; ?>

</div>

<?php endwhile; ?>

</div>

<!-- ================= GUEST MODAL ================= -->

<div id="guestModal" class="modal">

<div class="modal-content">

<span class="close-btn"
onclick="closeModal()">
&times;
</span>

<h3>Guest Details</h3>

<div id="guestDetails"></div>

<form action="process_guest_verification.php" method="POST">

<input type="hidden"
name="request_id"
id="modalRequestId">

<br>

<button class="approve"
name="decision"
value="approved">
Approve
</button>

<button type="button"
class="reject"
onclick="showReject()">
Reject
</button>

<div id="rejectBox" style="display:none">

<label>Reason:</label>

<textarea name="reject_reason"></textarea>

<br><br>

<button class="reject"
name="decision"
value="rejected">
Submit
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

<p><b>Room:</b> ${data.room_number}</p>

<p><b>Stay:</b>
${data.stay_from} to ${data.stay_to}
</p>

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

function filterRequests(){

let type=document.getElementById("filterType").value;

document.getElementById("guestSection").style.display=
(type=="guest") ? "block" : "none";

document.getElementById("swapSection").style.display=
(type=="swap") ? "block" : "none";

document.getElementById("vacateSection").style.display=
(type=="vacate") ? "block" : "none";

}

function approveSwap(id){

if(confirm("Approve this swap?")){

window.location.href=
"process_swap.php?action=approve&id="+id;

}

}

function rejectSwap(id){

let reason=prompt("Enter rejection reason:");

if(reason){

window.location.href=
"process_swap.php?action=reject&id="+id+
"&reason="+encodeURIComponent(reason);

}

}

function approveVacate(id){

if(confirm("Approve this vacate request?")){

window.location.href=
"../vacate/process_vacate.php?action=approve&id="+id;

}

}

function rejectVacate(id){

let reason = prompt("Enter rejection reason:");

if(reason){

window.location.href=
"../vacate/process_vacate.php?action=reject&id="+id+
"&reason="+encodeURIComponent(reason);

}

}

</script>

</body>
</html>
