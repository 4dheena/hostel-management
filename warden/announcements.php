<?php
require_once '../database/db_connect.php';

$query = "
SELECT title, message, file_path, created_at
FROM announcements
WHERE target='warden'
   OR target='hostel'
   OR target='general'
ORDER BY created_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Announcements</title>

<style>

body{
font-family: Arial;
padding:40px;
background:#f4f6f9;
}

h2{
margin-bottom:30px;
}

.announcement-card{
background:white;
border:1px solid #ddd;
padding:20px;
margin-bottom:20px;
border-radius:8px;
box-shadow:0 2px 5px rgba(0,0,0,0.05);
}

.announcement-title{
font-size:18px;
font-weight:bold;
margin-bottom:8px;
}

.announcement-date{
color:gray;
font-size:13px;
margin-bottom:10px;
}

.new-tag{
background:#28a745;
color:white;
font-size:11px;
padding:3px 7px;
border-radius:4px;
margin-left:8px;
}

.download-btn{
display:inline-block;
margin-top:10px;
padding:8px 14px;
background:#007bff;
color:white;
text-decoration:none;
border-radius:5px;
font-size:14px;
}

.download-btn:hover{
background:#0056b3;
}

</style>

</head>

<body>

<h2>Announcements</h2>

<?php if($result->num_rows == 0): ?>

<p>No announcements available.</p>

<?php endif; ?>

<?php while($row = $result->fetch_assoc()): ?>

<div class="announcement-card">

<div class="announcement-title">

<?php echo htmlspecialchars($row['title']); ?>

<?php
$announcementDate = strtotime($row['created_at']);
if(time() - $announcementDate < 86400){
echo "<span class='new-tag'>NEW</span>";
}
?>

</div>

<div class="announcement-date">
📅 <?php echo date("d M Y", strtotime($row['created_at'])); ?>
</div>

<p><?php echo htmlspecialchars($row['message']); ?></p>

<?php if(!empty($row['file_path'])): ?>

<a class="download-btn"
href="uploads/announcements/<?php echo $row['file_path']; ?>"
target="_blank">
📄 Download PDF
</a>

<?php endif; ?>

</div>

<?php endwhile; ?>

</body>
</html>