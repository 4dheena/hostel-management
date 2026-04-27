<?php
session_start();
include '../database/db_connect.php';

$warden_id = $_SESSION['user_id'];

/* GET WARDEN HOSTEL */
$warden = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT hostel_id FROM wardens WHERE user_id='$warden_id'
"));

$hostel_id = $warden['hostel_id'];

/* GET STUDENTS */
$students = mysqli_query($conn, "
SELECT 
s.user_id,
s.name,
r.room_number,
COUNT(a.date) AS absent
FROM students s
LEFT JOIN rooms r ON s.room_id = r.room_id
LEFT JOIN attendance a ON s.user_id = a.user_id
WHERE s.hostel_id = '$hostel_id'
GROUP BY s.user_id, s.name, r.room_number
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Warden Attendance</title>

<style>
body{margin:0;background:#eef3f9;font-family:sans-serif;}
.main{padding:25px;}

.card{
background:white;
padding:20px;
border-radius:15px;
}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
}
th,td{
padding:12px;
text-align:left;
border-bottom:1px solid #ddd;
}

/* INSPECT BUTTON */
.inspect-btn{
background:#10b981;
color:white;
padding:8px 14px;
border:none;
border-radius:8px;
cursor:pointer;
}
.inspect-btn:hover{
background:#059669;
}

/* MODAL */
.modal{
display:none;
position:fixed;
top:50%;
left:50%;
transform:translate(-50%,-50%);
background:white;
padding:20px;
border-radius:12px;
width:600px;
z-index:999;
box-shadow:0 10px 25px rgba(0,0,0,0.3);
}

/* HEADER NAV */
.nav{
display:flex;
justify-content:center;
align-items:center;
gap:20px;
margin:10px 0;
}

.nav-arrow{
font-size:20px;
cursor:pointer;
color:#6b7280;
user-select:none;
}
.nav-arrow:hover{
color:#111827;
}

/* CALENDAR */
.calendar{
display:grid;
grid-template-columns:repeat(7,1fr);
gap:8px;
margin-top:10px;
}

.day{
height:50px;
background:#e5edf7;
border-radius:8px;
display:flex;
align-items:center;
justify-content:center;
position:relative;
}

.day::after{
content:'';
width:6px;
height:6px;
border-radius:50%;
position:absolute;
bottom:5px;
}

/* STATES */
.present-day::after{background:green;}
.absent-day::after{background:red;}
.before-admission::after{background:gray;}

.future-day{opacity:0.4;}
.before-admission{opacity:0.4;}
</style>

</head>
<body>

<div class="main">

<div class="card">
<h2>Hostel Attendance</h2>

<table>
<tr>
<th>Name</th>
<th>Room</th>
<th>Absent</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($students)){ ?>
<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['room_number']; ?></td>
<td><?php echo $row['absent']; ?></td>
<td>
<button class="inspect-btn"
onclick="openInspect('<?php echo $row['user_id']; ?>','<?php echo $row['name']; ?>')">
Inspect
</button>
</td>
</tr>
<?php } ?>

</table>
</div>
</div>

<!-- MODAL -->
<div id="inspectModal" class="modal">

<h3 id="studentName"></h3>

<div class="nav">
<span class="nav-arrow" onclick="changeMonth(-1)">&#10094;</span>
<span id="monthTitle"></span>
<span class="nav-arrow" onclick="changeMonth(1)">&#10095;</span>
</div>

<div id="calendar" class="calendar"></div>

<br>
<button onclick="closeInspect()">Close</button>

</div>

<script>
let selectedUser = null;
let currentDate = new Date();

function openInspect(userId, name){
selectedUser = userId;
document.getElementById("studentName").innerText = name;
document.getElementById("inspectModal").style.display="block";
loadCalendar();
}

function closeInspect(){
document.getElementById("inspectModal").style.display="none";
}

function changeMonth(step){
currentDate.setMonth(currentDate.getMonth()+step);
loadCalendar();
}

function loadCalendar(){

let year = currentDate.getFullYear();
let month = currentDate.getMonth();

fetch(`../student/attendance_fetch.php?month=${month}&year=${year}&user_id=${selectedUser}`)
.then(res=>res.json())
.then(data=>{

let cal = document.getElementById("calendar");
cal.innerHTML="";

let today = new Date();
today.setHours(0,0,0,0);

let admissionDate = new Date(data.admission_date);
admissionDate.setHours(0,0,0,0);

let firstDay = new Date(year,month,1).getDay();
let days = new Date(year,month+1,0).getDate();

const months=["Jan","Feb","Mar","Apr","May","Jun",
"Jul","Aug","Sep","Oct","Nov","Dec"];

document.getElementById("monthTitle").innerText =
months[month]+" "+year;

/* empty cells */
for(let i=0;i<firstDay;i++){
cal.appendChild(document.createElement("div"));
}

/* days */
for(let i=1;i<=days;i++){

let div=document.createElement("div");
div.classList.add("day");
div.innerText=i;

let current = new Date(year,month,i);

let dateStr = year+"-"+String(month+1).padStart(2,'0')+"-"+String(i).padStart(2,'0');

let found = (data.calendar||[]).find(d=>d.date===dateStr);

if(current < admissionDate){
div.classList.add("before-admission");
}
else if(current > today){
div.classList.add("future-day");
}
else if(found){
div.classList.add("absent-day");
div.title = found.reason || "No reason";
}
else{
div.classList.add("present-day");
}

cal.appendChild(div);
}

});
}
</script>

</body>
</html>