<?php
session_start();
require_once "../database/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* get warden hostel */
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

/* fetch notifications (same structure) */

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
WHERE hostel_id = ?
GROUP BY reference_id, type
ORDER BY created_at DESC
");

$query->bind_param("i",$hostel_id);
$query->execute();
$notifications = $query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Notifications</title>

<style>
/* SAME CSS AS YOURS */
body{font-family:Arial;background:#f4f6f9;padding:40px;}
.notification-card{background:white;border:1px solid #ddd;padding:20px;margin-bottom:15px;border-radius:8px;}
.unread{border-left:5px solid #007bff;}
.review-btn{margin-top:10px;padding:6px 12px;background:#007bff;color:white;border:none;border-radius:4px;}
.reviewed{margin-top:10px;padding:6px 12px;background:#6c757d;color:white;border:none;border-radius:4px;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;padding:20px;width:420px;margin:10% auto;border-radius:8px;}
.close-btn{position:absolute;top:10px;right:15px;font-size:22px;cursor:pointer;}
.approve{background:#28a745;color:white;border:none;padding:8px 14px;margin-right:10px;border-radius:4px;}
.reject{background:#dc3545;color:white;border:none;padding:8px 14px;border-radius:4px;}
</style>
</head>

<body>

<h2>Notifications</h2>

<?php while($row = $notifications->fetch_assoc()): ?>

<div class="notification-card <?= $row['is_read']==0?'unread':'' ?>">

<b><?= htmlspecialchars($row['title']) ?></b>

<div><?= htmlspecialchars($row['message']) ?></div>

<?php if($row['type'] == 'guest_approval'): ?>

<?php
/* check status */
$stmt2 = $conn->prepare("
SELECT status
FROM stay_permissions
WHERE permission_id = ?
");

$stmt2->bind_param("i",$row['reference_id']);
$stmt2->execute();

$res2 = $stmt2->get_result();
$status = $res2->fetch_assoc();

if($status && $status['status'] != 'pending'){
?>

<button class="reviewed" disabled>
Reviewed (<?= $status['status'] ?>)
</button>

<?php } else { ?>

<button 
class="review-btn"
onclick="openModal(<?= $row['reference_id']?>,this)">
Review
</button>

<?php } ?>

<?php endif; ?>

</div>

<?php endwhile; ?>


<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-content">

<span class="close-btn" onclick="closeModal()">&times;</span>

<h3>Stay Permission</h3>

<div id="details"></div>

<br>

<button onclick="approve()" class="approve">Approve</button>
<button onclick="reject()" class="reject">Reject</button>

</div>
</div>

<script>

let currentId=null;
let currentBtn=null;

function openModal(id,btn){
currentId=id;
currentBtn=btn;

fetch("fetch_permission.php?id="+id)
.then(res=>res.json())
.then(data=>{

document.getElementById("details").innerHTML=`
<p><b>Guest:</b> ${data.guest_name}</p>
<p><b>From:</b> ${data.from_date}</p>
<p><b>To:</b> ${data.to_date}</p>
<p><b>Reason:</b> ${data.reason}</p>
`;

document.getElementById("modal").style.display="block";

});
}

function closeModal(){
document.getElementById("modal").style.display="none";
}

function approve(){process("approved");}
function reject(){process("rejected");}

function process(decision){

fetch("process_permission.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`id=${currentId}&decision=${decision}`
})
.then(res=>res.text())
.then(data=>{

if(currentBtn){
currentBtn.textContent="Reviewed";
currentBtn.classList.remove("review-btn");
currentBtn.classList.add("reviewed");
currentBtn.disabled=true;
}

closeModal();

});

}

</script>

</body>
</html>