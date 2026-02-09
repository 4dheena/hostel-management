<?php
require_once 'database/db_connect.php';

/* TODO: replace with session later */
$student_email = 'student@example.com';
$action = $_POST['action'] ?? 'save'; // 'save' or 'submit'
$upload_dir = 'uploads/documents/';

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

/* LOAD EXISTING */
$stmt = $conn->prepare("SELECT * FROM hostel_applications WHERE student_email=?");
$stmt->bind_param("s",$student_email);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
if (!empty($app['submitted_at'])) {
    header("Location: apply.php");
    exit;
}
/* HANDLE DISABILITY PERCENTAGE */
$disability_percentage = null;
if (
  isset($_POST['pwd_status']) &&
  $_POST['pwd_status'] === 'Yes' &&
  isset($_POST['disability_percentage']) &&
  $_POST['disability_percentage'] !== ''
) {
  $disability_percentage = (int) $_POST['disability_percentage'];
}

/* UPLOAD FILES */
$income_certificate = uploadFile('income_certificate',$app['income_certificate']??null);
$pwd_certificate    = uploadFile('pwd_certificate',$app['pwd_certificate']??null);
$id_proof           = uploadFile('id_proof',$app['id_proof']??null);

/* SAVE DATA */
$stmt = $conn->prepare("
UPDATE hostel_applications SET
full_name=?, student_id=?, phone=?, gender=?,
department=?, year_semester=?, dob=?, pincode=?, distance_km=?,
annual_income=?, pwd_status=?, disability_percentage=?,
income_certificate=?, pwd_certificate=?, id_proof=?,
submitted_at = IF(? = 'submit', NOW(), submitted_at)
WHERE student_email=?
");

$stmt->bind_param(
"sssssssssdissssss",
$_POST['full_name'],
$_POST['register_number'],
$_POST['personal_email'],
$_POST['phone'],
$_POST['gender'],
$_POST['department'],
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
$action,
$student_email
);

$stmt->execute();

/* ================= PASSWORD SET ================= */
if (!empty($_POST['new_password'])) {

    $password = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||   // uppercase
        !preg_match('/[a-z]/', $password) ||   // lowercase
        !preg_match('/[0-9]/', $password) ||   // number
        !preg_match('/[\W_]/', $password)      // special character
    ) {
        die(
          "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character."
        );
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "UPDATE students SET password_hash = ? WHERE email = ?"
    );
    $stmt->bind_param("ss", $hash, $student_email);
    $stmt->execute();
}


header("Location: apply.php");
exit;
