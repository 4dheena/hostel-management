<?php
require_once 'database/db_connect.php';

date_default_timezone_set('Asia/Kolkata');

// Check if user is logged in (optional - depends on your auth system)
// You might want to add authentication checks here

// Get form action
$action = $_POST['action'] ?? '';

// Validate action
if (!in_array($action, ['save', 'submit'])) {
    die("Invalid action");
}

// Get application settings
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

// Get student email to identify application
$personal_email = trim($_POST['personal_email'] ?? '');

if (empty($personal_email)) {
    die("Email is required");
}

// Check if application exists
$existingApp = null;
$stmt = $conn->prepare("SELECT * FROM hostel_applications WHERE personal_email = ?");
$stmt->bind_param("s", $personal_email);
$stmt->execute();
$existingApp = $stmt->get_result()->fetch_assoc();

$submitted = !empty($existingApp['submitted_at']);
$editable = (
    ($appWindowOpen && !$submitted) ||
    ($editWindowOpen && $submitted)
);

if (!$editable) {
    die("Application editing is not allowed at this time");
}

// Validate required fields for submission
if ($action === 'submit') {
    $required_fields = [
        'full_name', 'register_number', 'personal_email', 'phone',
        'gender', 'department', 'year_semester', 'dob', 'pincode',
        'annual_income', 'pwd_status'
    ];

    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            die("Required field missing: " . $field);
        }
    }
}

// Handle file uploads
$upload_dir = 'uploads/applications/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_fields = ['income_certificate', 'pwd_certificate', 'id_proof'];
$uploaded_files = [];

foreach ($file_fields as $field) {
    if (!empty($_FILES[$field]['name'])) {
        $file_name = $_FILES[$field]['name'];
        $file_tmp = $_FILES[$field]['tmp_name'];
        $file_size = $_FILES[$field]['size'];
        $file_error = $_FILES[$field]['error'];

        // Validate file
        if ($file_error !== UPLOAD_ERR_OK) {
            die("File upload error for $field");
        }

        // Check file size (max 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            die("File too large for $field (max 5MB)");
        }

        // Check file type (only PDF)
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($file_ext !== 'pdf') {
            die("Only PDF files allowed for $field");
        }

        // Generate unique filename
        $unique_name = uniqid() . '_' . $personal_email . '_' . $field . '.pdf';
        $file_path = $upload_dir . $unique_name;

        if (!move_uploaded_file($file_tmp, $file_path)) {
            die("Failed to save file for $field");
        }

        $uploaded_files[$field] = $file_path;
    }
}

// Prepare data for database
$data = [
    'full_name' => trim($_POST['full_name'] ?? ''),
    'register_number' => trim($_POST['register_number'] ?? ''),
    'personal_email' => $personal_email,
    'phone' => trim($_POST['phone'] ?? ''),
    'gender' => trim($_POST['gender'] ?? ''),
    'department' => trim($_POST['department'] ?? ''),
    'year_semester' => trim($_POST['year_semester'] ?? ''),
    'dob' => trim($_POST['dob'] ?? ''),
    'pincode' => trim($_POST['pincode'] ?? ''),
    'distance_km' => trim($_POST['distance_km'] ?? ''),
    'annual_income' => trim($_POST['annual_income'] ?? ''),
    'pwd_status' => trim($_POST['pwd_status'] ?? ''),
    'disability_percentage' => trim($_POST['disability_percentage'] ?? ''),
    'updated_at' => $now
];

// Handle password if provided
if (!empty($_POST['new_password'])) {
    $password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    // Validate password strength
    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)) {
        die("Password does not meet requirements");
    }

    $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
}

// Set submission timestamp for final submit
if ($action === 'submit') {
    $data['submitted_at'] = $now;
}

// Handle file paths
foreach ($file_fields as $field) {
    if (isset($uploaded_files[$field])) {
        $data[$field . '_path'] = $uploaded_files[$field];
    }
}

// Insert or update application
if ($existingApp) {
    // Update existing application
    $update_fields = [];
    $types = '';
    $values = [];

    foreach ($data as $field => $value) {
        $update_fields[] = "$field = ?";
        $types .= 's';
        $values[] = $value;
    }

    $values[] = $personal_email; // WHERE clause
    $types .= 's';

    $sql = "UPDATE hostel_applications SET " . implode(', ', $update_fields) . " WHERE personal_email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$values);

} else {
    // Insert new application
    $fields = array_keys($data);
    $placeholders = str_repeat('?,', count($fields) - 1) . '?';
    $types = str_repeat('s', count($fields));

    $sql = "INSERT INTO hostel_applications (" . implode(',', $fields) . ") VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...array_values($data));
}

if (!$stmt->execute()) {
    die("Database error: " . $stmt->error);
}

// Success - redirect back to application form
$redirect_url = "apply.php?email=" . urlencode($personal_email);

if ($action === 'submit') {
    $redirect_url .= "&submitted=1";
} else {
    $redirect_url .= "&saved=1";
}

header("Location: $redirect_url");
exit();
?>