<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch defaulters
$query = "SELECT s.name, s.email, s.phone, SUM(b.amount) as total_pending, COUNT(b.bill_id) as num_bills
          FROM students s
          JOIN bills b ON s.student_id = b.student_id
          WHERE b.status='pending'
          GROUP BY s.student_id
          ORDER BY total_pending DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Defaulters</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Students with Pending Bills</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Number of Pending Bills</th>
                <th>Total Pending Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['num_bills']; ?></td>
                <td>₹<?php echo number_format($row['total_pending'], 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>