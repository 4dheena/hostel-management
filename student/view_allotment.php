<?php

session_start();
require_once '../database/db_connect.php';
require_once '../fpdf/fpdf.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ================= FETCH STUDENT DATA ================= */

$stmt = $conn->prepare("
SELECT 
    s.student_id,
    s.name,
    s.email,
    s.phone,
    h.hostel_name,
    r.room_number,

    mw.full_name AS male_warden,
    mw.phone AS male_phone,

    fw.full_name AS female_warden,
    fw.phone AS female_phone

FROM students s

LEFT JOIN hostels h 
ON s.hostel_id = h.hostel_id

LEFT JOIN rooms r 
ON s.room_id = r.room_id

LEFT JOIN wardens mw 
ON h.hostel_id = mw.hostel_id AND mw.gender = 'Male'

LEFT JOIN wardens fw 
ON h.hostel_id = fw.hostel_id AND fw.gender = 'Female'

WHERE s.user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

/* ================= CHECK ALLOTMENT ================= */

if (!$data || empty($data['hostel_name'])) {

    $_SESSION['message'] = "⚠ You have not been allotted a hostel yet. Please check back later.";
    header("Location: dashboard.php");
    exit;
}

/* ================= GENERATE PDF ================= */

$pdf = new FPDF();
$pdf->AddPage();

/* TITLE */

$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,10,'ARUVI HOSTELS',0,1,'C');

$pdf->SetFont('Arial','',14);
$pdf->Cell(0,8,'Official Hostel Allotment Letter',0,1,'C');

$pdf->Ln(10);

/* ================= STUDENT DETAILS ================= */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Student Information',0,1);

$pdf->SetFont('Arial','',12);

$pdf->Cell(50,8,'Student ID:',0);
$pdf->Cell(0,8,$data['student_id'],0,1);

$pdf->Cell(50,8,'Name:',0);
$pdf->Cell(0,8,$data['name'],0,1);

$pdf->Cell(50,8,'Email:',0);
$pdf->Cell(0,8,$data['email'],0,1);

$pdf->Cell(50,8,'Phone:',0);
$pdf->Cell(0,8,$data['phone'],0,1);

$pdf->Ln(10);

/* ================= HOSTEL DETAILS ================= */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Hostel Allotment Details',0,1);

$pdf->SetFont('Arial','',12);

$pdf->Cell(50,8,'Hostel Name:',0);
$pdf->Cell(0,8,$data['hostel_name'],0,1);

$pdf->Cell(50,8,'Room Number:',0);
$pdf->Cell(0,8,$data['room_number'],0,1);

$pdf->Ln(10);

/* ================= WARDEN DETAILS ================= */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Warden Information',0,1);

$pdf->SetFont('Arial','',12);

$pdf->Cell(50,8,'Male Warden:',0);
$pdf->Cell(0,8,$data['male_warden'],0,1);

$pdf->Cell(50,8,'Contact:',0);
$pdf->Cell(0,8,$data['male_phone'],0,1);

$pdf->Ln(4);

$pdf->Cell(50,8,'Female Warden:',0);
$pdf->Cell(0,8,$data['female_warden'],0,1);

$pdf->Cell(50,8,'Contact:',0);
$pdf->Cell(0,8,$data['female_phone'],0,1);

$pdf->Ln(20);

/* ================= FOOTER ================= */

$pdf->SetFont('Arial','I',10);

$pdf->MultiCell(
0,
6,
"This document serves as the official confirmation of hostel accommodation under Aruvi Hostels. 
Students must strictly follow all hostel rules and regulations during their stay."
);

$pdf->Ln(15);

$pdf->Cell(0,8,'Issued By: Aruvi Hostels Administration',0,1,'R');
$pdf->Cell(0,8,date("d M Y"),0,1,'R');

/* ================= OUTPUT PDF ================= */

$pdf->Output('I','Hostel_Allotment.pdf');

?>