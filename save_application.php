<?php
require_once 'database/db_connect.php';

$action = $_POST['action'] ?? 'save';
$upload_dir = 'uploads/documents/';

/* ================= FILE UPLOAD FUNCTION ================= */
function uploadFile($field, $existing = null){
    global $upload_dir;

    if(!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK){
        return $existing;
    }

    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if($ext !== 'pdf') return $existing;

    if(!is_dir($upload_dir)){
        mkdir($upload_dir, 0777, true);
    }

    $name = uniqid().'_'.basename($_FILES[$field]['name']);
    $path = $upload_dir.$name;

    move_uploaded_file($_FILES[$field]['tmp_name'], $path);
    return $path;
}

/* ================= LOAD EXISTING APPLICATION ================= */
$stmt = $conn->prepare("SELECT * FROM hostel_applications WHERE student_id = ?");
$stmt->bind_param("s", $_POST['student_id']);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

/* Prevent editing after final submit */
if (!empty($app['submitted_at'])) {
    header("Location: apply.php");
    exit;
}

/* ================= HANDLE DISABILITY ================= */
$disability_percentage = null;

if (
    isset($_POST['pwd_status']) &&
    $_POST['pwd_status'] === 'Yes' &&
    !empty($_POST['disability_percentage'])
) {
    $disability_percentage = (int) $_POST['disability_percentage'];
}

/* ================= HANDLE PASSWORD ================= */
$password_hash = $app['password_hash'] ?? null;

if (!empty($_POST['new_password'])) {

    $password = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
    ) {
        die("Password must be at least 8 characters and include uppercase, lowercase, number and special character.");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
}

/* ================= FILE UPLOADS ================= */
$income_certificate = uploadFile('income_certificate', $app['income_certificate'] ?? null);
$pwd_certificate    = uploadFile('pwd_certificate', $app['pwd_certificate'] ?? null);
$id_proof           = uploadFile('id_proof', $app['id_proof'] ?? null);

/* ================= UPDATE APPLICATION ================= */
$stmt = $conn->prepare("
UPDATE hostel_applications SET
full_name=?,
department=?,
personal_email=?,
phone=?,
gender=?,
year_semester=?,
dob=?,
pincode=?,
distance_km=?,
annual_income=?,
pwd_status=?,
disability_percentage=?,
income_certificate=?,
pwd_certificate=?,
id_proof=?,
password_hash=?,
submitted_at = IF(?='submit', NOW(), submitted_at)
WHERE student_id=?
");

$stmt->bind_param(
    "ssssssssdiissssss",
    $_POST['full_name'],
    $_POST['department'],
    $_POST['personal_email'],
    $_POST['phone'],
    $_POST['gender'],
    $_POST['year_semester'],
    $_POST['dob'],
    $_POST['pincode'],
    $_POST['distance_km'],
    $_POST['annual_income'],
    $_POST['pwd_status'],
    $disability_percentage,
    $income_certificate,
    $pwd_certificate,
    $id_proof,
    $password_hash,
    $action,
    $_POST['student_id']
);

$stmt->execute();

header("Location: apply.php");
exit;