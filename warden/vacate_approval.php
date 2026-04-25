<?php
include("db_connect.php");

$result = mysqli_query($conn, "SELECT * FROM vacating_requests ORDER BY submitted_at DESC");
?>

<h2>Vacating Applications</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Student Name</th>
    <th>Room</th>
    <th>Date</th>
    <th>Reason</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
while($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $row['student_name']; ?></td>
    <td><?php echo $row['room_number']; ?></td>
    <td><?php echo $row['vacating_date']; ?></td>
    <td><?php echo $row['reason']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td>
        <a href="approve.php?id=<?php echo $row['id']; ?>">Approve</a> |
        <a href="reject.php?id=<?php echo $row['id']; ?>">Reject</a>
    </td>
</tr>
<?php } ?>
</table>