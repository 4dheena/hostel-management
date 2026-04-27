<?php
session_start();
include '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id, hostel_id FROM students WHERE user_id='$user_id'
"));

$student_id = $student['student_id'];
$hostel_id = $student['hostel_id'];

$wardens = mysqli_query($conn,"
SELECT user_id, full_name FROM wardens WHERE hostel_id='$hostel_id'
");

$complaints = mysqli_query($conn,"
SELECT * FROM complaints WHERE student_id='$student_id' ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Complaints</title>

<style>
body{font-family:Segoe UI;background:#f4f6f9;margin:0;padding:20px;}

.main{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
max-width:1100px;
margin:auto;
}

.section{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

input, textarea{
width:100%;
padding:10px;
margin-top:10px;
border-radius:8px;
border:1px solid #ddd;
}

/* RECEIVER BOX */
.receiver-box{
margin-top:10px;
padding:12px;
background:#f9fafc;
border-radius:10px;
border:1px solid #e5e7eb;
}

/* ALIGN CHECKBOX + TEXT */
.receiver-item{
display:flex;
align-items:center;
gap:10px;
margin-bottom:10px;
font-size:14px;
cursor:pointer;
}

.receiver-item input{
width:16px;
height:16px;
}

/* BUTTON SECTION */
.form-bottom{
display:flex;
justify-content:space-between;
gap:10px;
margin-top:15px;
}

/* NORMAL BUTTON */
.btn-normal{
flex:1;
background:#2563eb;
color:white;
padding:10px;
border:none;
border-radius:20px;
cursor:pointer;
}

/* ANONYMOUS BUTTON */
.btn-anon{
flex:1;
background:#64748b;
color:white;
padding:10px;
border:none;
border-radius:20px;
cursor:pointer;
}

.btn-normal:hover{ background:#1d4ed8; }
.btn-anon:hover{ background:#475569; }

.card{
margin-top:15px;
padding:15px;
background:#f9fafc;
border-radius:10px;
border:1px solid #e5e7eb;
}

.status{
padding:4px 10px;
border-radius:20px;
font-size:12px;
}

.not_read{background:#fee2e2;}
.reviewing{background:#fef3c7;}
.resolved{background:#dcfce7;}
</style>

</head>

<body>

<div class="main">

<div class="section">
<h3>📝 Write Complaint</h3>

<form action="complaint_action.php" method="POST">

<input type="hidden" name="action" value="submit">

<input type="text" name="title" placeholder="Title" required>
<textarea name="message" placeholder="Describe issue..." required></textarea>

<label>Select Receivers</label>

<div class="receiver-box">
<?php while($w = mysqli_fetch_assoc($wardens)): ?>
<label class="receiver-item">
<input type="checkbox" name="receivers[]" value="<?= $w['user_id']; ?>">
<?= $w['full_name']; ?>
</label>
<?php endwhile; ?>

<label class="receiver-item">
<input type="checkbox" name="receivers[]" value="admin"> Admin
</label>
</div>

<div class="form-bottom">
<button type="submit" name="type" value="normal" class="btn-normal">Submit Complaint</button>
<button type="submit" name="type" value="anonymous" class="btn-anon">Submit Anonymously</button>
</div>

</form>
</div>

<div class="section">
<h3>📌 My Complaints</h3>

<?php while($c = mysqli_fetch_assoc($complaints)): ?>

<div class="card">

<form action="complaint_action.php" method="POST">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="complaint_id" value="<?= $c['id']; ?>">

<button style="background:#ef4444;color:white;border:none;padding:6px 10px;border-radius:20px;cursor:pointer;">
✖ Delete
</button>
</form>

<h4><?= $c['title']; ?></h4>
<p><?= $c['message']; ?></p>

<span class="status <?= $c['status']; ?>">
<?= strtoupper($c['status']); ?>
</span>

</div>

<?php endwhile; ?>

</div>

</div>

</body>
</html>