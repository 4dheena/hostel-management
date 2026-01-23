<?php
require_once 'database/db_connect.php';

$student_email = 'student@example.com';
$upload_dir = 'uploads/documents/';

/* ================= HANDLE FILE UPLOAD ================= */
function uploadFile($field, $existingFile = null) {
    global $upload_dir;

    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return $existingFile;
    }

    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') return $existingFile;

    $filename = uniqid() . '_' . basename($_FILES[$field]['name']);
    $path = $upload_dir . $filename;

    move_uploaded_file($_FILES[$field]['tmp_name'], $path);
    return $path;
}

/* ================= LOAD EXISTING ================= */
$stmt = $conn->prepare("SELECT * FROM hostel_applications WHERE student_email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

/* ================= UPLOAD FILES ================= */
$income_certificate = uploadFile('income_certificate', $app['income_certificate'] ?? null);
$pwd_certificate    = uploadFile('pwd_certificate', $app['pwd_certificate'] ?? null);
$id_proof           = uploadFile('id_proof', $app['id_proof'] ?? null);

/* ================= SAVE FILE PATHS ================= */
$stmt = $conn->prepare("
  UPDATE hostel_applications
  SET income_certificate = ?, pwd_certificate = ?, id_proof = ?
  WHERE student_email = ?
");
$stmt->bind_param(
  "ssss",
  $income_certificate,
  $pwd_certificate,
  $id_proof,
  $student_email
);

$stmt->execute();

header("Location: apply.php");
exit;
