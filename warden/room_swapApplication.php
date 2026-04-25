<h2>Room Swap Applications</h2>

<?php
$result2 = mysqli_query($conn, "SELECT * FROM room_swap_requests ORDER BY submitted_at DESC");

while($row = mysqli_fetch_assoc($result2)) {
?>

<div style="background:#fff; padding:20px; margin:15px 0; border-radius:10px;">

<h3><?php echo $row['student_name']; ?></h3>
<p><b>Current Room:</b> <?php echo $row['current_room']; ?></p>
<p><b>Requested Room:</b> <?php echo $row['requested_room']; ?></p>
<p><b>Reason:</b> <?php echo $row['reason']; ?></p>
<p><b>Status:</b> <?php echo $row['status']; ?></p>

<a href="approve_swap.php?id=<?php echo $row['id']; ?>">Approve</a> |
<a href="reject_swap.php?id=<?php echo $row['id']; ?>">Reject</a>

</div>

<?php } ?>