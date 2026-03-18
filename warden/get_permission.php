<?php
require_once '../database/db_connect.php';

$id = $_GET['id'];

$q = mysqli_query($conn,
    "SELECT * FROM stay_permissions WHERE permission_id = $id");

$data = mysqli_fetch_assoc($q);
?>

<h3>Stay Request</h3>

<p><b>Guest:</b> <?php echo $data['guest_name']; ?></p>
<p><b>From:</b> <?php echo $data['from_date']; ?></p>
<p><b>To:</b> <?php echo $data['to_date']; ?></p>
<p><b>Reason:</b> <?php echo $data['reason']; ?></p>

<br>

<a href="approve.php?id=<?php echo $id; ?>">
    <button style="background:green;color:white;">Approve</button>
</a>

<a href="reject.php?id=<?php echo $id; ?>">
    <button style="background:red;color:white;">Reject</button>
</a>

<br><br>

<button onclick="document.getElementById('modal').style.display='none'">
Close
</button>