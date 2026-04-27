<?php
session_start();
require_once "../database/db_connect.php";
require_once "../database/notify.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch student hostel info */
$stmt = $conn->prepare("SELECT student_id, name, hostel_id FROM students WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student record not found.");
}

$feedbackType = '';
$comments = '';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedbackType = $_POST['feedback_type'] ?? '';
    $comments = trim($_POST['comments'] ?? '');

    if (!$feedbackType || !$comments) {
        $error = 'Please choose a feedback category and write your message.';
    } elseif (strlen($comments) > 2000) {
        $error = 'Feedback must be less than 2000 characters.';
    } else {
        $hostel_id = $student['hostel_id'];
        $title = '';
        $message = '';
        $targetUserId = null;
        $notificationType = '';

        if ($feedbackType === 'warden_feedback') {
            $title = 'Warden Feedback Submitted';
            $message = "Student {$student['name']} ({$student['student_id']}) has submitted feedback about the warden: {$comments}";
            $notificationType = 'warden_feedback';

            $adminStmt = $conn->prepare("SELECT user_id FROM admins LIMIT 1");
            $adminStmt->execute();
            $admin = $adminStmt->get_result()->fetch_assoc();
            $targetUserId = $admin['user_id'] ?? null;

            if (!$targetUserId) {
                $error = 'Unable to find the hostel admin. Please contact support.';
            }
        } elseif ($feedbackType === 'mess_feedback' || $feedbackType === 'staff_feedback') {
            $title = $feedbackType === 'mess_feedback' ? 'Mess Feedback Submitted' : 'Staff Feedback Submitted';
            $message = "Student {$student['name']} ({$student['student_id']}) has submitted feedback: {$comments}";
            $notificationType = $feedbackType;

            $wardenStmt = $conn->prepare("SELECT user_id FROM wardens WHERE hostel_id = ? LIMIT 1");
            $wardenStmt->bind_param("i", $hostel_id);
            $wardenStmt->execute();
            $warden = $wardenStmt->get_result()->fetch_assoc();
            $targetUserId = $warden['user_id'] ?? null;

            if (!$targetUserId) {
                $error = 'Unable to find your hostel warden. Please contact support.';
            }
        } else {
            $error = 'Invalid feedback category selected.';
        }

        if (!$error && $targetUserId) {
            createNotification($conn, $targetUserId, $hostel_id, $title, $message, $notificationType, null);
            $success = 'Your feedback has been submitted successfully.';
            $feedbackType = '';
            $comments = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Feedback</title>
<style>
body{font-family:Segoe UI,Arial,sans-serif;background:#f3f6fb;color:#222;margin:0;padding:0;}
.container{max-width:720px;margin:40px auto;padding:24px;background:white;border-radius:14px;box-shadow:0 12px 30px rgba(0,0,0,0.08);}
h1{margin-bottom:18px;font-size:28px;color:#1f3a72;}
.note{margin-bottom:20px;padding:14px 18px;border-radius:10px;background:#eef5ff;color:#1a376c;border:1px solid #d2e3fb;}
.form-group{margin-bottom:18px;}
label{display:block;margin-bottom:8px;font-weight:600;}
select,textarea{width:100%;border:1px solid #cbd4e7;border-radius:10px;padding:12px;font-size:15px;outline:none;transition:all .2s;}
select:focus,textarea:focus{border-color:#5b8dee;box-shadow:0 0 0 3px rgba(91,142,238,0.14);}
textarea{min-height:180px;resize:vertical;}
.button{display:inline-block;padding:12px 22px;background:#1f3a72;color:white;border:none;border-radius:10px;font-size:15px;cursor:pointer;transition:background .2s;}
.button:hover{background:#13306a;}
.message{padding:14px 18px;border-radius:10px;margin-bottom:18px;}
.success{background:#e6f7ea;color:#1e5e33;border:1px solid #b7e2bd;}
.error{background:#ffe9e5;color:#8d2a20;border:1px solid #f5c1bb;}
</style>
</head>
<body>
<div class="container">
<h1>Submit Feedback</h1>
<p class="note">Choose the correct feedback category. Warden feedback will notify the admin, while mess and staff feedback will notify your hostel warden.</p>

<?php if ($error): ?>
<div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" action="">
<div class="form-group">
<label for="feedback_type">Feedback Category</label>
<select id="feedback_type" name="feedback_type" required>
<option value="">Select feedback type</option>
<option value="warden_feedback" <?= $feedbackType === 'warden_feedback' ? 'selected' : '' ?>>Warden Feedback</option>
<option value="mess_feedback" <?= $feedbackType === 'mess_feedback' ? 'selected' : '' ?>>Mess Feedback</option>
<option value="staff_feedback" <?= $feedbackType === 'staff_feedback' ? 'selected' : '' ?>>Staff Feedback</option>
</select>
</div>

<div class="form-group">
<label for="comments">Feedback Details</label>
<textarea id="comments" name="comments" required><?= htmlspecialchars($comments) ?></textarea>
</div>

<button type="submit" class="button">Submit Feedback</button>
</form>
</div>
</body>
</html>
