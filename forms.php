<?php if(isset($_GET['success'])): ?>
<div style="background:#d4edda;color:#155724;padding:10px;margin:20px;border-radius:6px;">
Guest request submitted successfully.
</div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Forms | ARUVI Hostel</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
margin:0;
font-family:Arial, Helvetica, sans-serif;
background:#f4f6f9;
}

/* NAVBAR */

.navbar{
background:#0f2d4a;
padding:15px 60px;
display:flex;
justify-content:space-between;
align-items:center;
}

.logo{
color:white;
font-size:22px;
font-weight:bold;
}

.nav-links a{
color:white;
text-decoration:none;
margin-left:25px;
font-size:15px;
}

.nav-links a:hover{
border-bottom:2px solid white;
padding-bottom:3px;
}

/* HERO */

.hero{
background:
linear-gradient(rgba(15,45,74,0.7),rgba(15,45,74,0.7)),
url("assets/images/hostel.jpeg");

background-size:cover;
background-position:center;
height:250px;

display:flex;
align-items:center;
justify-content:center;

color:white;
text-align:center;
}

.hero h1{
font-size:40px;
margin-bottom:10px;
}

.hero p{
font-size:18px;
opacity:0.9;
}

/* SECTION */

.section{
max-width:1200px;
margin:auto;
padding:50px 20px;
}

.section h2{
color:#243b55;
margin-bottom:30px;
}

/* CARDS */

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:30px;
}

.card{
background:white;
border-radius:12px;
padding:25px;
text-align:center;
box-shadow:0 8px 20px rgba(0,0,0,0.08);
transition:0.3s;
}

.card:hover{
transform:translateY(-5px);
}

.form-icon{
font-size:38px;
color:#2b6cb0;
background:#eaf2fb;
padding:15px;
border-radius:10px;
margin-bottom:15px;
display:inline-block;
}

.card:hover .form-icon{
transform:scale(1.1);
transition:0.3s;
}

.card h3{
margin-bottom:10px;
}

.card p{
font-size:14px;
color:#555;
margin-bottom:20px;
}

.btn{
display:inline-block;
background:#2b6cb0;
color:white;
padding:10px 20px;
border-radius:6px;
text-decoration:none;
font-size:14px;
cursor:pointer;
}

.btn:hover{
background:#1e4f85;
}

.guideline{
display:block;
margin-top:15px;
font-size:13px;
color:#444;
text-decoration:none;
}

.guideline:hover{
text-decoration:underline;
}


/* MODAL */

.modal{
display:none;
position:fixed;
z-index:999;
left:0;
top:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
}

.modal-content{
background:white;
margin:5% auto;
padding:20px;
width:80%;
max-width:900px;
border-radius:10px;
position:relative;
}

.close-btn{
position:absolute;
right:20px;
top:15px;
font-size:22px;
cursor:pointer;
}

iframe{
width:100%;
height:600px;
border:none;
}

</style>
</head>


<body>

<div class="navbar">
<div class="logo">ARUVI</div>

<div class="nav-links">
<a href="index.php">Home</a>
<a href="about.php">About</a>
<a href="facilities.php">Facilities</a>
<a href="contact.php">Contact</a>
<a href="announcement.php">Announcements</a>
<a href="rules.php">Rules</a>
<a href="forms.php">Forms</a>
</div>
</div>


<div class="hero">
<div>
<h1>Student Forms & Applications</h1>
<p>Submit requests and applications easily through the available forms.</p>
</div>
</div>


<div class="section">

<h2>Available Forms</h2>

<div class="cards">

<div class="card">

<i class="fa-solid fa-user-group form-icon"></i>

<h3>Guest Stay Application</h3>

<p>Apply for permission to allow a guest to stay temporarily in your hostel room.</p>

<button class="btn"
onclick="openForm('forms/guest_application.php')">

Apply Now

</button>

<a class="guideline"
href="uploads/guidelines/guest_guidelines.pdf"
target="_blank">

Download Guidelines (PDF)

</a>

</div>


<div class="card">

<i class="fa-solid fa-calendar-days form-icon"></i>

<h3>Hostel Vacating Form</h3>

<p>Submit your hostel vacating declaration before the end of the academic year as required by hostel regulations.</p>

<button class="btn"
onclick="openForm('forms/vacate.php')">

Submit Request

</button>

<a class="guideline"
href="uploads/guidelines/vacate_guidelines.pdf"
target="_blank">

Download Guidelines (PDF)

</a>

</div>


<div class="card">

<i class="fa-solid fa-right-left form-icon"></i>

<h3>Room Swap Request</h3>

<p>Request a swap of rooms with another student with approval from administration.</p>

<button class="btn"
onclick="openForm('forms/room_swap.php')">

Request Swap

</button>

<a class="guideline"
href="uploads/guidelines/swap_guidelines.pdf"
target="_blank">

Download Guidelines (PDF)

</a>

</div>

</div>
</div>


<!-- MODAL -->

<div id="formModal" class="modal">

<div class="modal-content">

<span class="close-btn" onclick="closeModal()">×</span>

<iframe id="formFrame"></iframe>

</div>

</div>


<script>

function openForm(url){

document.getElementById("formModal").style.display="block";

document.getElementById("formFrame").src=url;

}

function closeModal(){

document.getElementById("formModal").style.display="none";

document.getElementById("formFrame").src="";

}

window.onclick=function(event){

let modal=document.getElementById("formModal");

if(event.target==modal){

closeModal();

}

}

</script>


</body>
</html>