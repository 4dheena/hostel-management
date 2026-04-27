<?php
session_start();
include '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

$complaints = mysqli_query($conn,"
SELECT 
c.id,
c.title,
c.message,
c.status,
c.created_at,
c.is_anonymous,

s.student_id,
s.name AS student_name,
s.room_id,

r.room_number

FROM complaints c

JOIN complaint_receivers cr 
ON c.id = cr.complaint_id

LEFT JOIN students s 
ON c.student_id = s.student_id

LEFT JOIN rooms r
ON s.room_id = r.room_id

WHERE cr.receiver_id='$user_id' 
OR cr.receiver_role='admin'

ORDER BY c.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Complaints</title>

<style>
body{
font-family:"Segoe UI", sans-serif;
background:#f4f6f9;
margin:0;
}

/* WRAPPER */
.wrapper{
margin:40px auto;
padding:20px;
}

/* HEADER */
.header{
background:#ffffff;
color:#1e3a8a;
padding:18px 25px;
border-radius:12px 12px 0 0;
font-size:20px;
font-weight:600;
}

/* CONTAINER */
.container{
background:white;
border-radius:0 0 12px 12px;
box-shadow:0 6px 20px rgba(0,0,0,0.08);
overflow:hidden;
}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
}

th{
background:#0e2f3a;
color:white;
padding:14px;
text-align:left;
}

td{
padding:14px;
border-bottom:1px solid #eee;
}

tr:hover{
background:#f1f5f9;
}

/* STATUS */
.status{
padding:5px 12px;
border-radius:20px;
font-size:12px;
font-weight:500;
}

.not_read{background:#fee2e2;color:#b91c1c;}
.reviewing{background:#fef3c7;color:#92400e;}
.resolved{background:#dcfce7;color:#166534;}

/* BUTTON */
button{
padding:6px 14px;
border:none;
border-radius:20px;
background:#2563eb;
color:white;
cursor:pointer;
}

button:hover{
background:#1d4ed8;
}

/* MODAL */
#modal{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
}

.modal-box{
background:white;
width:420px;
margin:10% auto;
padding:20px;
border-radius:12px;
}

.student-box{
background:#f1f5f9;
padding:10px;
border-radius:8px;
margin-top:10px;
font-size:14px;
}

select{
width:100%;
padding:10px;
margin-top:10px;
border-radius:8px;
border:1px solid #ddd;
}
</style>

</head>

<body>

<div class="wrapper">

<div class="header">
📋 Complaints
</div>

<div class="container">

<table>
<tr>
<th>Date</th>
<th>Title</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($c = mysqli_fetch_assoc($complaints)): ?>

<tr>
<td><?= $c['created_at']; ?></td>
<td><?= $c['title']; ?></td>

<td>
<span class="status <?= $c['status']; ?>">
<?= strtoupper($c['status']); ?>
</span>
</td>

<td>
<button onclick="openModal(
<?= $c['id']; ?>,
'<?= addslashes($c['title']); ?>',
'<?= addslashes($c['message']); ?>',
'<?= $c['status']; ?>',
<?= $c['is_anonymous']; ?>,
'<?= addslashes($c['student_name']); ?>',
'<?= $c['student_id']; ?>',
'<?= $c['room_number']; ?>'
)">
Inspect
</button>
</td>

</tr>

<?php endwhile; ?>

</table>

</div>

</div>

<!-- MODAL -->
<div id="modal">
<div class="modal-box">

<h3 id="m_title"></h3>
<p id="m_msg"></p>

<div id="m_student" class="student-box"></div>

<select id="m_status">
<option value="not_read">Not Read</option>
<option value="reviewing">Reviewing</option>
<option value="resolved">Resolved</option>
</select>

<br><br>

<button onclick="updateStatus()">Update</button>
<button onclick="closeModal()">Close</button>

</div>
</div>

<script>
let currentId=0;

function openModal(id,title,msg,status,isAnon,name,sid,room){

document.getElementById("modal").style.display="block";

document.getElementById("m_title").innerText=title;
document.getElementById("m_msg").innerText=msg;
document.getElementById("m_status").value=status;

/* 🔥 FIXED PART */
if(isAnon == 1){
document.getElementById("m_student").innerHTML =
"<i>Anonymous Complaint</i>";
}else{
document.getElementById("m_student").innerHTML =
"<b>Name:</b> " + (name || "N/A") +
"<br><b>ID:</b> " + (sid || "N/A") +
"<br><b>Room:</b> " + (room || "N/A");
}

currentId=id;
}

function closeModal(){
document.getElementById("modal").style.display="none";
}

function updateStatus(){
let status=document.getElementById("m_status").value;

fetch("complaint_action.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`action=update_status&complaint_id=${currentId}&status=${status}`
}).then(()=>location.reload());
}
</script>

</body>
</html>