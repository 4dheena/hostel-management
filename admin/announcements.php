<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Announcements</title>

<style>

body{
font-family: Arial;
padding:40px;
}

h2{
margin-bottom:20px;
}

form{
margin-bottom:40px;
}

input,textarea,select{
width:100%;
padding:8px;
margin-top:5px;
margin-bottom:15px;
}

button{
background:#007bff;
color:white;
border:none;
padding:10px 16px;
border-radius:5px;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th,td{
border:1px solid #ccc;
padding:8px;
text-align:center;
}

th{
background:#f4f4f4;
}

</style>

</head>

<body>

<h2>Create Announcement</h2>

<form action="save_announcement.php" method="POST" enctype="multipart/form-data">

<label>Title</label>
<input type="text" name="title" required>

<label>Message</label>
<textarea name="message" rows="4" required></textarea>

<label>Target Audience</label>

<select name="target" required>
<option value="student">Students</option>
<option value="hostel">Hostel (Students + Wardens)</option>
</select>

<label>Upload PDF (optional)</label>
<input type="file" name="file" accept="application/pdf">

<button type="submit">Publish Announcement</button>

</form>

<h2>Existing Announcements</h2>

<table>

<tr>
<th>Title</th>
<th>Date</th>
<th>File</th>
<th>Delete</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?php echo htmlspecialchars($row['title']); ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>

<?php if(!empty($row['file_path'])): ?>

<a href="../uploads/ranklists/<?php echo $row['file_path']; ?>" target="_blank">Download</a>

<?php else: ?>

No File

<?php endif; ?>

</td>

<td>

<a href="delete_announcement.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this announcement?')">
Delete
</a>

</td>

</tr>

<?php endwhile; ?>

</table>

</body>
</html>