<?php
require_once 'database/db_connect.php';
ob_start();
date_default_timezone_set('Asia/Kolkata');

/* ================= ACTION ================= */

$action = $_POST['action'] ?? '';

if (!in_array($action, ['submit', 'update'])) {

    echo "<script>
    alert('Invalid action');
    window.history.back();
    </script>";

    exit();
}

/* ================= SETTINGS ================= */

$settingsQuery = $conn->query("
    SELECT start_date, end_date, edit_start, edit_end
    FROM application_settings
    WHERE id = 1
");

$settings = $settingsQuery->fetch_assoc();

$now = date('Y-m-d H:i:s');

$appWindowOpen = (
    $now >= $settings['start_date'] &&
    $now <= $settings['end_date']
);

$editWindowOpen = (
    !empty($settings['edit_start']) &&
    !empty($settings['edit_end']) &&
    $now >= $settings['edit_start'] &&
    $now <= $settings['edit_end']
);

/* ================= IDENTIFY ================= */

$personal_email = trim($_POST['personal_email'] ?? '');
$student_id = trim($_POST['register_number'] ?? '');

if (empty($personal_email)) {

    echo "<script>
    alert('Email is required');
    window.history.back();
    </script>";

    exit();
}

/* ================= FETCH EXISTING ================= */

$stmt = $conn->prepare("
    SELECT * 
    FROM hostel_applications 
    WHERE student_id = ?
");

$stmt->bind_param("s", $student_id);
$stmt->execute();

$existingApp = $stmt->get_result()->fetch_assoc();

/* ================= APPLICATION WINDOW ================= */

if (!$appWindowOpen) {

    echo "<script>
    alert('Application window is closed');
    window.location.href='index.php';
    </script>";

    exit();
}

/* ================= DUPLICATE CHECK ================= */

if ($existingApp && $action === 'submit') {

    echo "
    <script>
    alert('Application already exists for Student ID: {$existingApp['student_id']}');
    window.location.href='apply.php';
    </script>
    ";

    exit();
}

/* ================= VALIDATION ================= */

$required_fields = [
    'full_name',
    'register_number',
    'personal_email',
    'phone',
    'gender',
    'department',
    'year_semester',
    'dob',
    'pincode',
    'annual_income',
    'pwd_status'
];

foreach ($required_fields as $field) {

    if (empty(trim($_POST[$field] ?? ''))) {

        echo "<script>
        alert('Please fill all required fields');
        window.history.back();
        </script>";

        exit();
    }
}

/* ================= FILE UPLOAD ================= */

$upload_dir = 'uploads/applications/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_fields = ['income_certificate', 'pwd_certificate', 'id_proof'];

$uploaded_files = [];

foreach ($file_fields as $field) {

    if (!empty($_FILES[$field]['name'])) {

        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {

            echo "<script>
            alert('File upload error for $field');
            window.location.href='apply.php';
            </script>";

            exit();
        }

        if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {

            echo "<script>
            alert('File too large for $field (max 5MB)');
            window.location.href='apply.php';
            </script>";

            exit();
        }

        $file_ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

        if ($file_ext !== 'pdf') {

            echo "<script>
            alert('Only PDF files allowed for $field');
            window.location.href='apply.php';
            </script>";

            exit();
        }

        $unique_name = uniqid() . '_' . $personal_email . '_' . $field . '.pdf';

        $file_path = $upload_dir . $unique_name;

        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $file_path)) {

            echo "<script>
            alert('Failed to save file for $field');
            window.location.href='apply.php';
            </script>";

            exit();
        }

        $uploaded_files[$field] = $file_path;
    }
}

/* ================= DATA ================= */

$data = [

    'full_name' => trim($_POST['full_name'] ?? ''),
    'student_id' => $student_id,
    'personal_email' => $personal_email,
    'phone' => trim($_POST['phone'] ?? ''),
    'gender' => trim($_POST['gender'] ?? ''),
    'department' => trim($_POST['department'] ?? ''),
    'year_semester' => trim($_POST['year_semester'] ?? ''),
    'dob' => trim($_POST['dob'] ?? ''),
    'pincode' => trim($_POST['pincode'] ?? ''),

    'distance_km' => (
        isset($_POST['distance_km']) &&
        is_numeric($_POST['distance_km'])
    )
    ? round((float)$_POST['distance_km'], 2)
    : 0.00,

    'annual_income' => trim($_POST['annual_income'] ?? ''),
    'pwd_status' => trim($_POST['pwd_status'] ?? ''),
    'income_certificate' => $uploaded_files['income_certificate'] ?? '',
    'pwd_certificate' => $uploaded_files['pwd_certificate'] ?? '',
    'id_proof' => $uploaded_files['id_proof'] ?? '',
    'disability_percentage' => (
        isset($_POST['disability_percentage']) &&
        $_POST['disability_percentage'] !== '' &&
        is_numeric($_POST['disability_percentage'])
    )
    ? (int)$_POST['disability_percentage']
    : 0,

    'updated_at' => $now
];

/* ================= PASSWORD ================= */

if (!empty($_POST['new_password'])) {

    $password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {

        echo "<script>
        alert('Passwords do not match');
        window.history.back();
        </script>";

        exit();
    }

    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
    ) {

        echo "<script>
        alert('Password does not meet security requirements');
        window.history.back();
        </script>";

        exit();
    }

    $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
}

/* ================= INSERT ================= */

$data['submitted_at'] = $now;

$fields = array_keys($data);

$placeholders = str_repeat('?,', count($fields) - 1) . '?';

$types = str_repeat('s', count($fields));

$sql = "
    INSERT INTO hostel_applications 
    (" . implode(',', $fields) . ")
    VALUES ($placeholders)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo "<script>
    alert('Database prepare failed');
    window.history.back();
    </script>";

    exit();
}

$stmt->bind_param($types, ...array_values($data));

if (!$stmt->execute()) {

    echo "<script>
    alert('Database error occurred');
    window.history.back();
    </script>";

    exit();
}

/* ================= SUCCESS ================= */

echo "
<script>
alert('Application submitted successfully');
window.location.href='index.php';
</script>
";

exit();
?>