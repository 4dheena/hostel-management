<?php
session_start();
include '../database/db_connect.php';

$student_id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT s.name, u.profile_image, h.hostel_name, r.room_number
FROM users u
JOIN students s ON u.username = s.student_id
JOIN hostels h ON s.hostel_id = h.hostel_id
JOIN rooms r ON s.room_id = r.room_id
WHERE u.user_id = '$student_id'
"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
html,body{
margin:0;
height:100%;
display:flex;
background:#eef3f9;
font-family:sans-serif;
}

.sidebar{
width:240px;
padding:25px;
box-sizing:border-box;
background:linear-gradient(180deg,#0f2027,#203a43,#2c5364);
color:white;
}

.sidebar a{
display:block;
color:white;
text-decoration:none;
padding:12px;
margin-bottom:6px;
border-radius:8px;
}
.sidebar a:hover{background:rgba(255,255,255,0.15);}

.main{
flex:1;
padding:25px;
overflow-y:auto;
}

.card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:20px;
}

.top{
display:flex;
justify-content:space-between;
align-items:center;
}

.profile{
display:flex;
gap:15px;
align-items:center;
}

.profile img{
width:70px;
height:70px;
border-radius:50%;
}

.btn{
padding:10px 14px;
border:none;
border-radius:8px;
color:white;
cursor:pointer;
}
.absent{background:#ef4444;}

.grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:20px;
}

.calendar-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:10px;
}

.weekdays{
display:grid;
grid-template-columns:repeat(7,1fr);
text-align:center;
font-weight:600;
margin-bottom:10px;
}

.calendar{
display:grid;
grid-template-columns:repeat(7,1fr);
gap:10px;
}

.day{
height:60px;
background:#dbe4ef;
border-radius:10px;
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
bottom:6px;
}

.before-admission::after{background:gray;}
.present-day::after{background:green;}
.absent-day::after{background:red;}
.future-day{opacity:0.4;}

#barChart{
height:260px !important;
width:100%;
}

.modal{
display:none;
position:fixed;
top:100px;
right:30px;
z-index:999;
}

.modal-box{
background:white;
padding:20px;
border-radius:12px;
width:300px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.close{cursor:pointer;float:right;}

textarea,input,select{
width:100%;
margin:5px 0;
}


</style>
</head>

<body>

<div class="sidebar">
<h2>ARUVI</h2>
<a href="dashboard.php">Dashboard</a>
<a href="attendance.php">Attendance</a>
<a href="mess.php">Mess</a>
<a href="gbm.php">GBM</a>
<a href="complaints.php">Complaints</a>
<a href="fees.php">Fees</a>
<a href="feedback.php">Feedback</a>
<a href="services.php">Services</a>
<a href="notifications.php">Notifications</a>
<a href="../auth/logout.php">Logout</a>
</div>

<div class="main">

<div class="card top">
<div class="profile">
<img src="<?php echo !empty($student['profile_image']) ? $student['profile_image'] : '../assets/images/default_profile.jpg'; ?>">
<div>
<h2><?php echo $student['name']; ?></h2>
<p><?php echo $student['hostel_name']; ?> - Room <?php echo $student['room_number']; ?></p>
</div>
</div>

<div>
<h3>Mark Absence</h3>
<button class="btn absent" onclick="openModal()">Mark Absence</button>
</div>
</div>

<div class="grid">

<div class="card">
<div class="calendar-header">
<button onclick="changeMonth(-1)">◀</button>
<h3 id="monthTitle"></h3>
<button onclick="changeMonth(1)">▶</button>
</div>

<div class="weekdays">
<div>Sun</div><div>Mon</div><div>Tue</div>
<div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
</div>

<div id="calendar" class="calendar"></div>
</div>

<div>

<div class="card">
<h2>Monthly Attendance Summary</h2>    
<p>Total: <span id="total"></span></p>
<p>Present: <span id="present"></span></p>
<p>Absent: <span id="absent"></span></p>
<p>Percentage: <span id="percent"></span></p>
</div>

<div class="card">
<canvas id="barChart"></canvas>
</div>

</div>
</div>

</div>

<div id="absenceModal" class="modal">
<div class="modal-box">

<span class="close" onclick="closeModal()">✖</span>

<label>Type</label>
<select id="type" onchange="toggleDates()">
<option value="single">Single Day</option>
<option value="vacation">Vacation</option>
</select>

<div id="singleDateBox">
<label>Date</label>
<input type="date" id="singleDate">
</div>

<div id="dateSection" style="display:none;">
<label>Start</label>
<input type="date" id="startDate">
<label>End</label>
<input type="date" id="endDate">
</div>

<label>Reason</label>
<textarea id="reason"></textarea>

<button onclick="submitAbsent()">Submit</button>

</div>
</div>

<script>
let currentDate = new Date();

function loadCalendar(){

let year = currentDate.getFullYear();
let month = currentDate.getMonth();

fetch(`attendance_fetch.php?month=${month}&year=${year}`)
.then(res=>res.json())
.then(data=>{
console.log("admission date", data.admission_date);
let calendar=document.getElementById("calendar");
calendar.innerHTML="";

let today=new Date();
today.setHours(0,0,0,0);

let firstDay=new Date(year,month,1).getDay();
let days=new Date(year,month+1,0).getDate();

const months=["January","February","March","April","May","June",
"July","August","September","October","November","December"];

document.getElementById("monthTitle").innerText =
months[month]+" "+year;

/* 🔥 SAFE ADMISSION DATE PARSE */
let admissionDate = null;

if(data.admission_date){
    let parts = data.admission_date.split("-");
    admissionDate = new Date(parts[0], parts[1]-1, parts[2]);
    admissionDate.setHours(0,0,0,0);
}

/* empty cells */
for(let i=0;i<firstDay;i++){
calendar.appendChild(document.createElement("div"));
}

/* days */
for(let i=1;i<=days;i++){

let div=document.createElement("div");
div.classList.add("day");
div.innerText=i;

let dateStr=year+"-"+String(month+1).padStart(2,'0')+"-"+String(i).padStart(2,'0');

let current=new Date(year,month,i);
current.setHours(0,0,0,0); // 🔥 IMPORTANT

let found=(data.calendar||[]).find(d=>d.date===dateStr);

/* 🔥 FIXED DOT LOGIC */
if(admissionDate && current.getTime() < admissionDate.getTime()){
    div.classList.add("before-admission");
}
else if(current > today){
    div.classList.add("future-day");
}
else if(found){
    div.classList.add("absent-day");
}
else{
    div.classList.add("present-day");
}

calendar.appendChild(div);
}

/* SUMMARY */
total.innerText = (data.total !== undefined) ? data.total : "-";
present.innerText = (data.present !== undefined) ? data.present : "-";
absent.innerText = (data.absent !== undefined) ? data.absent : "-";
percent.innerText = (data.percent !== undefined) ? data.percent+"%" : "-";

/* ===== CHART FIX ===== */
if(window.barChart && typeof window.barChart.destroy === 'function'){
window.barChart.destroy();
}

let canvas=document.getElementById("barChart");

window.barChart=new Chart(canvas,{
type:'bar',
data:{
labels:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
datasets:[{
data: (data.monthly || []).map(m => Number(m.percentage) || 0),
backgroundColor:[
'#3b82f6','#22c55e','#f59e0b','#ef4444',
'#8b5cf6','#06b6d4','#84cc16','#f97316',
'#10b981','#6366f1','#ec4899','#14b8a6'
]
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
plugins:{legend:{display:false}},
scales:{y:{beginAtZero:true,max:100}}
}
});

});
}

function changeMonth(step){
currentDate.setMonth(currentDate.getMonth()+step);
loadCalendar();
}

function openModal(){
document.getElementById("absenceModal").style.display="block";
}

function closeModal(){
document.getElementById("absenceModal").style.display="none";
}

function toggleDates(){
document.getElementById("dateSection").style.display =
(type.value==="vacation")?"block":"none";

document.getElementById("singleDateBox").style.display =
(type.value==="single")?"block":"none";
}
loadCalendar();
function submitAbsent(){

let data={
status:'absent',
reason:reason.value,
type:type.value
};

if(type.value==="single"){
data.date=singleDate.value;
}

if(type.value==="vacation"){
data.start=startDate.value;
data.end=endDate.value;
}

fetch('mark_attendance.php',{
method:'POST',
body:new URLSearchParams(data)
})
.then(res => res.json())
.then(res => {

if(res.status === "success"){
    alert("Marked successfully ✅");
    closeModal();
    clearModal();
    loadCalendar();
}else{
    alert(res.msg || "Something went wrong ❌");
}

})
.catch(err=>{
console.error(err);
alert("Server error ❌");
});
}
function clearModal(){
singleDate.value = "";
startDate.value = "";
endDate.value = "";
reason.value = "";
}


</script>

</body>
</html>