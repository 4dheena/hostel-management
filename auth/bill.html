<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monthly Billing Overview</title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    background: #eef1f5;
    margin: 0;
}

.container {
    width: 90%;
    margin: 20px auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h2 {
    margin: 0;
}

.date {
    font-size: 12px;
    color: gray;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-top: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.dropdown {
    margin-bottom: 15px;
}

select {
    padding: 5px;
}

.grid {
    display: flex;
    gap: 15px;
}

.box {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    position: relative;
    background: #fafafa;
}

.badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #3b82f6;
    color: white;
    padding: 5px 10px;
    border-radius: 50%;
    font-size: 12px;
}

.total {
    font-weight: bold;
    font-size: 18px;
}

.tabs {
    margin-top: 20px;
    border-bottom: 2px solid #ddd;
}

.tabs span {
    margin-right: 20px;
    padding-bottom: 5px;
    cursor: pointer;
}

.tabs .active {
    border-bottom: 3px solid #3b82f6;
    font-weight: bold;
}

.table-card {
    background: #fff;
    margin-top: 10px;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
}

thead {
    background: #f5f5f5;
}

.status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
}

.pending {
    background: #ffe5b4;
    color: #b76e00;
}

.paid {
    background: #d4edda;
    color: #155724;
}

.pay-btn {
    background: #2563eb;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.pay-btn:hover {
    background: #1e4ed8;
}

.link {
    color: #2563eb;
    cursor: pointer;
}

.completed-title {
    margin-top: 20px;
}
</style>

</head>

<body>

<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$current_month = date('m');
$current_year = date('Y');

// Fetch current bill
$query = "SELECT * FROM bills WHERE student_id='$student_id' AND month=$current_month AND year=$current_year";
$result = mysqli_query($conn, $query);
$current_bill = mysqli_fetch_assoc($result);

// If not exists, calculate
if (!$current_bill) {
    include '../utils/calculate_bill.php';
    calculateMonthlyBill($student_id, $current_month, $current_year);
    $result = mysqli_query($conn, $query);
    $current_bill = mysqli_fetch_assoc($result);
}

// Fetch pending bills
$pending_query = "SELECT * FROM bills WHERE student_id='$student_id' AND status='pending' ORDER BY year DESC, month DESC";
$pending_result = mysqli_query($conn, $pending_query);

// Fetch completed bills
$completed_query = "SELECT * FROM bills WHERE student_id='$student_id' AND status='paid' ORDER BY year DESC, month DESC";
$completed_result = mysqli_query($conn, $completed_query);
?>

<div class="container">

    <div class="header">
        <h2>Monthly Billing Overview</h2>
        <span class="date"><?php echo date('M d, Y, h:i A'); ?></span>
    </div>

    <div class="card">
        <h3>Current Month Calculation</h3>

        <div class="dropdown">
            <label>Current Month</label>
            <select>
                <option><?php echo date('F Y'); ?></option>
            </select>
        </div>

        <div class="grid">
            <div class="box">
                <div class="badge">1</div>
                <h4>Rates</h4>
                <p>Daily Rate: <b>₹160 / Day</b></p>
            </div>

            <div class="box">
                <div class="badge">2</div>
                <h4>Days Calculation</h4>
                <p>Total Days in Month: <?php echo $current_bill['total_days']; ?></p>
                <p>Mess Cut Days (Exc.): <?php echo $current_bill['mess_cut_days']; ?></p>
                <p><b>Effective Days: <?php echo $current_bill['effective_days']; ?></b></p>
            </div>

            <div class="box">
                <div class="badge">3</div>
                <h4>Total Bill Amount</h4>
                <p>Calculation: <?php echo $current_bill['effective_days']; ?> * 160</p>
                <p class="total">Total: ₹<?php echo number_format($current_bill['amount'], 2); ?></p>
            </div>
        </div>
    </div>

    <div class="tabs">
        <span class="active">Pending Bills</span>
        <span>Completed Bills</span>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($bill = mysqli_fetch_assoc($pending_result)) { ?>
                <tr>
                    <td><?php echo $bill['bill_id']; ?></td>
                    <td><?php echo date('M Y', mktime(0,0,0,$bill['month'],1,$bill['year'])); ?></td>
                    <td>₹<?php echo number_format($bill['amount'], 2); ?></td>
                    <td><?php echo date('M Y', mktime(0,0,0,$bill['month']+1,1,$bill['year'])); ?></td>
                    <td><span class="status pending">Pending</span></td>
                    <td><button class="pay-btn" onclick="payNow(<?php echo $bill['bill_id']; ?>, <?php echo $bill['amount']; ?>)">PAY NOW</button></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <h4 class="completed-title">Completed Bills</h4>

    <div class="table-card">
        <table>
            <tbody>
                <?php while ($bill = mysqli_fetch_assoc($completed_result)) { ?>
                <tr>
                    <td><?php echo $bill['bill_id']; ?></td>
                    <td><?php echo date('M Y', mktime(0,0,0,$bill['month'],1,$bill['year'])); ?></td>
                    <td>₹<?php echo number_format($bill['amount'], 2); ?></td>
                    <td><?php echo date('M Y', strtotime($bill['payment_date'])); ?></td>
                    <td><span class="status paid">Paid</span></td>
                    <td class="link">View Receipt</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function payNow(bill_id, amount) {
    var options = {
        key: 'YOUR_RAZORPAY_KEY_ID', // Replace with your Razorpay Key ID
        amount: amount * 100, // Amount in paise
        currency: 'INR',
        name: 'Hostel Management',
        description: 'Mess Bill Payment',
        handler: function (response) {
            // Redirect to process payment
            window.location.href = 'process_payment.php?bill_id=' + bill_id + '&payment_id=' + response.razorpay_payment_id;
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
}
</script>

</body>
</html>