<?php
session_start();
include '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id, hostel_id FROM students WHERE user_id='$user_id'
"));

$student_id = $student['student_id'];
$hostel_id = $student['hostel_id'];

/* ACTIVE POLL */
$poll = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM gbm_polls 
WHERE hostel_id='$hostel_id' 
AND status='active'
ORDER BY id DESC LIMIT 1
"));

/* CHECK VOTED */
$already = 0;
if($poll){
    $already = mysqli_num_rows(mysqli_query($conn,"
    SELECT * FROM gbm_votes 
    WHERE poll_id='{$poll['id']}' 
    AND student_id='$student_id'
    "));
}

/* SUGGESTIONS */
$suggestions = mysqli_query($conn,"
SELECT * FROM gbm_suggestions 
WHERE hostel_id='$hostel_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>GBM</title>

<style>
body{
font-family:"Segoe UI", sans-serif;
background:#f4f6f9;
padding:25px;
}

/* GRID */
.main{
display:grid;
grid-template-columns:1fr 1fr;
gap:25px;
max-width:1200px;
margin:auto;
}

.left{
display:flex;
flex-direction:column;
gap:20px;
}

.section{
background:white;
padding:20px;
border-radius:14px;
box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

input[type="text"],
textarea{
width:100%;
padding:10px;
margin-top:10px;
border-radius:8px;
border:1px solid #ddd;
}

/* BUTTONS */
button{
padding:10px 14px;
border:none;
border-radius:20px;
cursor:pointer;
margin-top:10px;
}

.vote-btn{ background:#3b82f6; color:white; }
.submit-btn{ background:#22c55e; color:white; }

.disabled{
background:gray;
cursor:not-allowed;
}

/* OPTION */
.option{
padding:12px;
margin:10px 0;
border:1px solid #ddd;
border-radius:10px;
background:#fff;
}

.option-top{
display:flex;
align-items:center;
gap:10px;
}

.option input[type="radio"]{
margin:0;
flex-shrink:0;
}

.option-info{
font-size:16px;
}

.bar-container{
width:100%;
height:10px;
background:#e5e7eb;
border-radius:10px;
overflow:hidden;
margin-top:10px;
}

.bar{
height:100%;
background:#3b82f6;
}

/* CARD */
.card{
position:relative;
margin-top:15px;
padding:15px;
border-radius:10px;
background:#f9fafc;
}

/* DELETE BUTTON */
.delete-btn{
position:absolute;
top:10px;
right:10px;
background:#ef4444;
color:white;
border:none;
padding:5px 10px;
border-radius:20px;
font-size:12px;
cursor:pointer;
}

.delete-btn:hover{
background:#dc2626;
}

/* REACTIONS */
.reactions{
margin-top:8px;
display:flex;
gap:10px;
}

.up{
background:#dcfce7;
color:#16a34a;
padding:4px 10px;
border-radius:20px;
}

.down{
background:#fee2e2;
color:#dc2626;
padding:4px 10px;
border-radius:20px;
}

/* RESPONSIVE */
@media(max-width:900px){
.main{grid-template-columns:1fr;}
}
</style>

</head>

<body>

<div class="main">

<!-- LEFT SIDE -->
<div class="left">

<div class="section">
<h3>📊 Active Poll</h3>

<?php if(!$poll): ?>

<p>No active poll available.</p>

<?php else: ?>

<h4><?= $poll['question']; ?></h4>

<form action="gbm_action.php" method="POST">
<input type="hidden" name="action" value="vote">
<input type="hidden" name="poll_id" value="<?= $poll['id']; ?>">

<?php
$options = mysqli_query($conn,"
SELECT o.id, o.option_text, COUNT(v.id) AS votes
FROM gbm_poll_options o
LEFT JOIN gbm_votes v ON o.id = v.option_id
WHERE o.poll_id='{$poll['id']}'
GROUP BY o.id
");

$total = 0;
$data = [];

while($row = mysqli_fetch_assoc($options)){
$total += $row['votes'];
$data[] = $row;
}
?>

<?php foreach($data as $opt):
$percent = ($total>0)?round(($opt['votes']/$total)*100):0;
?>

<div class="option">

<div class="option-top">

<input 
type="radio" 
name="option_id" 
value="<?= $opt['id']; ?>" 
required>

<div class="option-info">
<?= $opt['option_text']; ?> (<?= $percent ?>%)
</div>

</div>

<div class="bar-container">
<div class="bar" style="width:<?= $percent ?>%"></div>
</div>

</div>

<?php endforeach; ?>

<?php if($already): ?>
<button class="vote-btn disabled" disabled>Already Voted</button>
<?php else: ?>
<button type="submit" class="vote-btn">Vote</button>
<?php endif; ?>

</form>

<?php endif; ?>

</div>

</div>

<!-- RIGHT SIDE -->
<div class="section">

<h3>💡 Suggestions</h3>

<form action="gbm_action.php" method="POST">
<input type="hidden" name="action" value="suggest">

<input type="text" name="title" placeholder="Title" required>
<textarea name="description" placeholder="Your suggestion..." required></textarea>

<button type="submit" class="submit-btn">Submit</button>
</form>

<hr>

<?php while($row = mysqli_fetch_assoc($suggestions)): ?>

<?php
$up = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM gbm_reactions WHERE suggestion_id='{$row['id']}' AND reaction='up'
"));

$down = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM gbm_reactions WHERE suggestion_id='{$row['id']}' AND reaction='down'
"));
?>

<div class="card">

<!-- DELETE BUTTON (ONLY OWNER) -->
<?php if($row['student_id'] == $student_id): ?>
<form action="gbm_action.php" method="POST">
<input type="hidden" name="action" value="delete_suggestion">
<input type="hidden" name="suggestion_id" value="<?= $row['id']; ?>">

<button class="delete-btn" onclick="return confirm('Delete this suggestion?')">
✖
</button>
</form>
<?php endif; ?>

<h4><?= htmlspecialchars($row['title']); ?></h4>
<p><?= htmlspecialchars($row['description']); ?></p>

<form action="gbm_action.php" method="POST">
<input type="hidden" name="action" value="react">
<input type="hidden" name="suggestion_id" value="<?= $row['id']; ?>">

<div class="reactions">
<button name="reaction" value="up" class="up">👍 <?= $up ?></button>
<button name="reaction" value="down" class="down">👎 <?= $down ?></button>
</div>

</form>

</div>

<?php endwhile; ?>

</div>

</div>

</body>
</html>