<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$bill_id = $_GET['bill_id'];
$razorpay_payment_id = $_GET['payment_id'];
$student_id = $_SESSION['user_id'];

// Update bill
$query = "UPDATE bills SET status='paid', payment_date=CURDATE() WHERE bill_id=$bill_id AND student_id='$student_id'";
mysqli_query($conn, $query);

// Insert payment record
$bill_query = "SELECT amount FROM bills WHERE bill_id=$bill_id";
$result = mysqli_query($conn, $bill_query);
$bill = mysqli_fetch_assoc($result);
$amount = $bill['amount'];

$payment_query = "INSERT INTO payments (student_id, amount, payment_date, status, razorpay_payment_id) VALUES ('$student_id', $amount, CURDATE(), 'completed', '$razorpay_payment_id')";
mysqli_query($conn, $payment_query);

// Redirect back to bill.php with success
header("Location: bill.php?success=1");
?>