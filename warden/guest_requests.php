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

$stmt->bind_param("i",$user_id);
$stmt->execute();

$res = $stmt->get_result();
$warden = $res->fetch_assoc();

$hostel_id = $warden['hostel_id'];

/* FETCH REQUESTS FOR THIS HOSTEL */

$stmt = $conn->prepare("
SELECT *
FROM guest_requests
WHERE overall_status='warden_review'
AND hostel_id=?
ORDER BY submitted_at DESC
");

$stmt->bind_param("i",$hostel_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Guest Approval</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:40px;
}

h2{
margin-bottom:25px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
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

.view-btn{
padding:6px 12px;
background:#007bff;
color:white;
border:none;
border-radius:4px;
cursor:pointer;
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

</style>

</head>

<body>

<h2>Guest Stay Requests (Warden Verification)</h2>

<?php if($result->num_rows == 0): ?>

<p>No guest requests pending.</p>

<?php else: ?>

<table>

<tr>
<th>Guest Name</th>
<th>Student ID</th>
<th>Room</th>
<th>Stay From</th>
<th>Stay To</th>
<th>ID Proof</th>
<th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($row['guest_name']) ?></td>

<td><?= htmlspecialchars($row['guest_student_id']) ?></td>

<td><?= htmlspecialchars($row['room_number']) ?></td>

<td><?= htmlspecialchars($row['stay_from']) ?></td>

<td><?= htmlspecialchars($row['stay_to']) ?></td>

<td>

<?php if(!empty($row['id_proof_path'])): ?>

<a href="../<?= $row['id_proof_path'] ?>" target="_blank">
📄 View ID
</a>

<?php else: ?>

No ID

<?php endif; ?>

</td>

<td>

<?php if($row['warden_status'] == 'approved' || $row['warden_status'] == 'rejected'): ?>

<button class="view-btn" disabled style="background:gray;">
Reviewed
</button>

<?php else: ?>

<button class="view-btn" onclick="openGuestModal(<?= $row['id'] ?>)">
Review
</button>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>


<!-- MODAL -->

<div id="guestModal" class="modal">

<div class="modal-content">

<span class="close-btn" onclick="closeModal()">&times;</span>

<h3>Guest Request Details</h3>

<div id="guestDetails"></div>

<form action="process_guest_verification.php" method="POST">

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

fetch("../guest_module/fetch_guest_request.php?id=" + id)

.then(response => response.json())

.then(data => {

if(data.error){
alert(data.error);
return;
}

document.getElementById("modalRequestId").value = id;

let idProofSection = "";

if(data.id_proof_path){
idProofSection = `
<p><b>ID Proof:</b> 
<a href="../${data.id_proof_path}" target="_blank">View Document</a>
</p>
`;
}

document.getElementById("guestDetails").innerHTML = `
<p><b>Name:</b> ${data.guest_name}</p>
<p><b>Email:</b> ${data.guest_email}</p>
<p><b>Phone:</b> ${data.guest_phone}</p>
<p><b>Room:</b> ${data.room_number}</p>
<p><b>Stay From:</b> ${data.stay_from}</p>
<p><b>Stay To:</b> ${data.stay_to}</p>
<p><b>Message:</b> ${data.request_message ?? "None"}</p>
${idProofSection}
`;

document.getElementById("guestModal").style.display = "block";

})

.catch(error => {
console.error("Fetch error:", error);
alert("Failed to load guest request.");
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