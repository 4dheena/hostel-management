<?php
include '../database/db_connect.php';

function calculateMonthlyBill($student_id, $month, $year) {
    // Get total days in the month
    $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Get mess cut days for the month
    $start_of_month = sprintf('%04d-%02d-01', $year, $month);
    $end_of_month = sprintf('%04d-%02d-%02d', $year, $month, $total_days);

    $query = "SELECT SUM(DATEDIFF(LEAST(end_date, '$end_of_month'), GREATEST(start_date, '$start_of_month')) + 1) as mess_cut_days
              FROM mess_cuts
              WHERE student_id = '$student_id' AND status = 'approved'
              AND start_date <= '$end_of_month' AND end_date >= '$start_of_month'";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $mess_cut_days = $row['mess_cut_days'] ? $row['mess_cut_days'] : 0;

    // Effective days
    $effective_days = $total_days - $mess_cut_days;

    // Amount
    $amount = $effective_days * 160;

    // Insert or update bill
    $insert_query = "INSERT INTO bills (student_id, month, year, total_days, mess_cut_days, effective_days, amount)
                     VALUES ('$student_id', $month, $year, $total_days, $mess_cut_days, $effective_days, $amount)
                     ON DUPLICATE KEY UPDATE
                     mess_cut_days = VALUES(mess_cut_days),
                     effective_days = VALUES(effective_days),
                     amount = VALUES(amount)";

    mysqli_query($conn, $insert_query);
}

function generateBillsForAllStudents($month, $year) {
    global $conn;
    $query = "SELECT student_id FROM students";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        calculateMonthlyBill($row['student_id'], $month, $year);
    }
}

// Example usage: generate for current month
$current_month = date('m');
$current_year = date('Y');
generateBillsForAllStudents($current_month, $current_year);

echo "Bills calculated for $current_month/$current_year";
?>