<?php
session_start();
require_once '../database/db_connect.php';
require_once '../fpdf/fpdf.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* FETCH APPROVED STUDENTS */

$query = "
SELECT student_id, full_name, department, priority_score
FROM hostel_applications
WHERE status='approved'
ORDER BY priority_score DESC
";

$result = $conn->query($query);

if($result->num_rows == 0){
    die("No approved students found.");
}

/* CREATE PDF */

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'ARUVI HOSTELS - RANK LIST',0,1,'C');

$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(20,10,'Rank',1);
$pdf->Cell(40,10,'Student ID',1);
$pdf->Cell(80,10,'Name',1);
$pdf->Cell(50,10,'Department',1);

$pdf->Ln();

$pdf->SetFont('Arial','',11);

$rank = 1;

while($row = $result->fetch_assoc()){

$pdf->Cell(20,10,$rank,1);
$pdf->Cell(40,10,$row['student_id'],1);
$pdf->Cell(80,10,$row['full_name'],1);
$pdf->Cell(50,10,$row['department'],1);

$pdf->Ln();

$rank++;

}

/* SAVE PDF */

$filename = "ranklist_".date("Ymd_His").".pdf";

$file_path = "../uploads/ranklists/".$filename;

$pdf->Output('F',$file_path);

/* INSERT ANNOUNCEMENT */

$title = "Hostel Rank List Published";

$message = "The hostel rank list has been published. Click to download.";

$stmt = $conn->prepare("INSERT INTO announcements (title, message, file_path, created_at) VALUES (?, ?, ?, NOW())");

$stmt->bind_param("sss",$title,$message,$filename);

$stmt->execute();

/* REDIRECT */

header("Location: announcements.php");

exit;
?>