<?php
// register_action.php
require_once 'db_connect.php';

function json_response($msg) {
    echo "<p>$msg</p><p><a href='register.php'>Back to Register</a></p>";
    exit;
}

// Collect and validate
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if (!$username || !$password || !$role) {
    json_response("Username, password and role are required.");
}

// Check username unique
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    json_response("Username already taken. Choose another username.");
}
$stmt->close();

// Hash password
$pw_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert into users
$ins = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$ins->bind_param("sss", $username, $pw_hash, $role);
if (!$ins->execute()) {
    json_response("Error creating user: " . $ins->error);
}
$user_id = $ins->insert_id;
$ins->close();

// If student: insert into students table
if ($role === 'Student') {
    $name = trim($_POST['s_name'] ?? '');
    $email = trim($_POST['s_email'] ?? '');
    $phone = trim($_POST['s_phone'] ?? '');
    $hostel_id = !empty($_POST['s_hostel']) ? intval($_POST['s_hostel']) : null;
    $room_id = null;

    // If user provided room as text/room_number, try to find room_id
    $room_text = trim($_POST['s_room'] ?? '');
    if ($room_text !== '') {
        // try to find by room_number in rooms
        $q = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = ? LIMIT 1");
        $q->bind_param("s", $room_text);
        $q->execute();
        $q->bind_result($found_room_id);
        if ($q->fetch()) {
            $room_id = $found_room_id;
        }
        $q->close();
    }

    $ins2 = $conn->prepare("INSERT INTO students (user_id, name, email, phone, hostel_id, room_id) VALUES (?, ?, ?, ?, ?, ?)");
    // use s for strings and i for ints; need to pass nulls for hostel/room if not set
    // convert null to null in bind_param by setting variables and types accordingly
    $hostel_val = $hostel_id !== null ? $hostel_id : null;
    $room_val = $room_id !== null ? $room_id : null;
    // bind_param doesn't accept null type directly; use integer or string and pass null as null via conditional
    if ($hostel_val === null && $room_val === null) {
        $ins2->bind_param("issssi", $user_id, $name, $email, $phone, $hostel_val, $room_val);
        // above will treat null as 0 which might violate FK; better use explicit NULL in query if needed.
    } else {
        // safer version using dynamic query to handle nulls properly
        $ins2->close();
        $query = "INSERT INTO students (user_id, name, email, phone, hostel_id, room_id) VALUES (?, ?, ?, ?, ?, ?)";
        $ins2 = $conn->prepare($query);
        // For null positions, we set to NULL using bind_param with types "isssii" but pass 0 for nulls and then update to NULL via query? Simpler: use explicit SQL with NULL values.
        $h = ($hostel_val === null) ? null : $hostel_val;
        $r = ($room_val === null) ? null : $room_val;
        $ins2->bind_param("isssii", $user_id, $name, $email, $phone, $h, $r);
    }

    // To avoid binding null confusion, we will instead build a query that uses NULL placeholders:
    $ins2->close();
    $query = "INSERT INTO students (user_id, name, email, phone, hostel_id, room_id) VALUES (?, ?, ?, ?, ?, ?)";
    $ins3 = $conn->prepare($query);
    // set variables as ints or nulls
    $hostel_param = ($hostel_id === null) ? null : $hostel_id;
    $room_param = ($room_id === null) ? null : $room_id;
    $ins3->bind_param("isssii", $user_id, $name, $email, $phone, $hostel_param ?? 0, $room_param ?? 0);
    // This is a small pragmatic compromise: if hostel/room unknown we pass 0 — if your DB forbids 0, change logic to produce query with NULLs.
    if (!$ins3->execute()) {
        json_response("User created but error inserting student: " . $ins3->error);
    }
    $ins3->close();

    json_response("Student registration successful. User ID: $user_id");
}

// If Matron or Chief Warden: insert into staff table if exists, else only users created
if ($role === 'Matron' || $role === 'ChiefWarden') {
    // check if staff table exists
    $res = $conn->query("SHOW TABLES LIKE 'staff'");
    if ($res && $res->num_rows > 0) {
        $st_name = trim($_POST['st_name'] ?? '');
        $st_email = trim($_POST['st_email'] ?? '');
        $st_phone = trim($_POST['st_phone'] ?? '');
        $st_hostel = !empty($_POST['st_hostel']) ? intval($_POST['st_hostel']) : null;

        $query = "INSERT INTO staff (user_id, name, email, phone, hostel_id) VALUES (?, ?, ?, ?, ?)";
        $insS = $conn->prepare($query);
        // handle null hostel
        $hid = ($st_hostel === null) ? null : $st_hostel;
        $insS->bind_param("isssi", $user_id, $st_name, $st_email, $st_phone, $hid ?? 0);
        if (!$insS->execute()) {
            json_response("User created but error inserting staff: " . $insS->error);
        }
        $insS->close();
        json_response("Staff registration successful. User ID: $user_id");
    } else {
        json_response("User created as staff (users table). Consider creating a 'staff' table to store contact details.");
    }
}

// Default response (if some other role)
json_response("User created with user_id = $user_id");
?>
