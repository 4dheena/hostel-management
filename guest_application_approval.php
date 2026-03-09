<?php

$conn = mysqli_connect("localhost","root","","hostel_management");

$student_id = 23; // from login session

$result = mysqli_query($conn,"
SELECT gr.request_id,gr.guest_name,gr.guest_student_id,r.room_number,ia.approval_status
FROM inmate_approvals ia
JOIN guest_requests gr ON ia.request_id = gr.request_id
JOIN rooms r ON gr.room_id = r.room_id
WHERE ia.student_id='$student_id'
");

while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $row['guest_name']; ?></td>
<td><?php echo $row['guest_student_id']; ?></td>
<td><?php echo $row['room_number']; ?></td>
<td><?php echo $row['approval_status']; ?></td>

<td>

<a href="approve.php?id=<?php echo $row['request_id']; ?>">Approve</a>

<a href="reject.php?id=<?php echo $row['request_id']; ?>">Reject</a>

</td>

</tr>

<?php } ?>