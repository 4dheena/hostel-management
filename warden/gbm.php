<?php
session_start();
include '../database/db_connect.php';

$user_id = $_SESSION['user_id'];

$warden = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT hostel_id FROM wardens WHERE user_id='$user_id'
"));

$hostel_id = $warden['hostel_id'];

$polls = mysqli_query($conn,"
SELECT * FROM gbm_polls 
WHERE hostel_id='$hostel_id'
ORDER BY id DESC
");

$suggestions = mysqli_query($conn,"
SELECT * FROM gbm_suggestions 
WHERE hostel_id='$hostel_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>GBM Dashboard</title>

<style>
body{
font-family:"Segoe UI", sans-serif;
background:#f4f6f9;
padding:25px;
}

/* 🌿 MAIN GRID */
.main{
display:grid;
grid-template-columns: 1fr 1fr;
gap:25px;
max-width:1200px;
margin:auto;
}

/* LEFT COLUMN STACK */
.left{
display:flex;
flex-direction:column;
gap:20px;
}

/* SECTION */
.section{
background:white;
padding:20px;
border-radius:14px;
box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* INPUT */
input{
width:100%;
padding:10px;
margin-top:10px;
border-radius:8px;
border:1px solid #ddd;
}

/* BUTTON */
button{
padding:10px 14px;
border:none;
border-radius:20px;
cursor:pointer;
margin-top:10px;
}

.add-btn{ background:#22c55e; color:white; }
.submit-btn{ background:#3b82f6; color:white; }

/* CARD */
.card{
margin-top:15px;
padding:15px;
border-radius:10px;
background:#f9fafc;
}

/* PROGRESS BAR */
.bar-container{
margin-top:8px;
background:#e5e7eb;
border-radius:20px;
overflow:hidden;
height:12px;
}

.bar{
height:100%;
background:#3b82f6;
}

/* WINNER */
.winner .bar{
background:#22c55e;
}

/* SUGGESTION */
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
.main{
grid-template-columns:1fr;
}
}
</style>

<script>
function addOption(){
let div = document.createElement("div");
div.innerHTML = `<input type="text" name="options[]" placeholder="Option" required>`;
document.getElementById("options").appendChild(div);
}
</script>

</head>

<body>

<div class="main">

<!-- 🌿 LEFT SIDE -->
<div class="left">

<!-- CREATE POLL -->
<div class="section">
<h3>➕ Create Poll</h3>

<form action="gbm_action.php" method="POST">
<input type="hidden" name="action" value="create_poll">

<input type="text" name="question" placeholder="Enter question" required>

<div id="options">
<input type="text" name="options[]" placeholder="Option 1" required>
<input type="text" name="options[]" placeholder="Option 2" required>
</div>

<button type="button" class="add-btn" onclick="addOption()">+ Add Option</button>
<button type="submit" class="submit-btn">Create Poll</button>

</form>
</div>

<!-- POLL RESULTS -->
<div class="section">
<h3>📊 Poll Results</h3>

<?php while($poll = mysqli_fetch_assoc($polls)): ?>

<div class="card">
<h4><?= $poll['question']; ?></h4>

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

$max = 0;
foreach($data as $d){
if($d['votes'] > $max) $max = $d['votes'];
}
?>

<?php foreach($data as $opt): 
$percent = ($total > 0) ? round(($opt['votes']/$total)*100) : 0;
?>

<div class="<?= ($opt['votes']==$max && $max>0)?'winner':'' ?>">

<div style="display:flex;justify-content:space-between;">
<span><?= $opt['option_text']; ?></span>
<span><?= $opt['votes']; ?> (<?= $percent ?>%)</span>
</div>

<div class="bar-container">
<div class="bar" style="width:<?= $percent ?>%"></div>
</div>

</div>

<?php endforeach; ?>

</div>

<?php endwhile; ?>

</div>

</div>


<!-- 🌿 RIGHT SIDE -->
<div class="section">
<h3>💡 Suggestions</h3>

<?php while($row = mysqli_fetch_assoc($suggestions)): ?>

<?php
$up = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM gbm_reactions WHERE suggestion_id='{$row['id']}' AND reaction='up'
"));

$down = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM gbm_reactions WHERE suggestion_id='{$row['id']}' AND reaction='down'
"));

$isTrending = ($up > $down && $up > 3);
?>

<div class="card <?= $isTrending ? 'winner' : '' ?>">

<h4><?= htmlspecialchars($row['title']); ?></h4>
<p><?= htmlspecialchars($row['description']); ?></p>

<div class="reactions">
<span class="up">👍 <?= $up ?></span>
<span class="down">👎 <?= $down ?></span>
</div>

</div>

<?php endwhile; ?>

</div>

</div>

</body>
</html>