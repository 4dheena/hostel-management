<?php
header('Content-Type: application/json');
require_once '../database/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get student's hostel_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT hostel_id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$studentResult = $stmt->get_result()->fetch_assoc();
$hostel_id = $studentResult['hostel_id'] ?? null;

if (!$hostel_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Student hostel not found']);
    exit();
}

// Temporary file to store messages (not in database)
$tempDir = '../uploads/gbm_temp/';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

$messagesFile = $tempDir . 'hostel_' . $hostel_id . '_messages.json';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ================= GET MESSAGES =================
if ($action === 'get') {
    $messages = [];
    
    if (file_exists($messagesFile)) {
        $content = file_get_contents($messagesFile);
        $allMessages = json_decode($content, true) ?? [];
        
        // Filter and limit messages
        $messages = array_slice($allMessages, -100);
    }

    echo json_encode(['success' => true, 'messages' => $messages]);
    exit();
}

// ================= SAVE MESSAGE =================
if ($action === 'save') {
    $message = trim($_POST['message'] ?? '');

    if (empty($message)) {
        http_response_code(400);
        echo json_encode(['error' => 'Message cannot be empty']);
        exit();
    }

    if (strlen($message) > 500) {
        http_response_code(400);
        echo json_encode(['error' => 'Message too long (max 500 characters)']);
        exit();
    }

    // Load existing messages
    $messages = [];
    if (file_exists($messagesFile)) {
        $content = file_get_contents($messagesFile);
        $messages = json_decode($content, true) ?? [];
    }

    // Prevent spam - check if last message was sent less than 2 seconds ago
    if (!empty($messages)) {
        $lastMessage = end($messages);
        $lastTime = strtotime($lastMessage['created_at']);
        $currentTime = time();
        
        if (($currentTime - $lastTime) < 2) {
            http_response_code(429);
            echo json_encode(['error' => 'Please wait before sending another message']);
            exit();
        }
    }

    // Add new message
    $newMessage = [
        'id' => count($messages) + 1,
        'message' => htmlspecialchars($message),
        'created_at' => date('Y-m-d H:i:s'),
        'time' => date('H:i'),
        'date' => date('d M Y')
    ];

    $messages[] = $newMessage;

    // Keep only last 500 messages to prevent file from getting too large
    if (count($messages) > 500) {
        $messages = array_slice($messages, -500);
    }

    // Save messages to file
    if (file_put_contents($messagesFile, json_encode($messages))) {
        echo json_encode(['success' => true, 'message_id' => $newMessage['id']]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save message']);
    }
    exit();
}

// ================= GET ACTIVE USERS COUNT =================
if ($action === 'active_users') {
    // Count how many GBM temp files exist (active hostels)
    $gbmTempDir = '../uploads/gbm_temp/';
    $activeCount = 0;
    
    if (is_dir($gbmTempDir)) {
        $files = scandir($gbmTempDir);
        $activeCount = max(0, count($files) - 2); // Exclude . and ..
    }

    echo json_encode(['success' => true, 'active_users' => max(1, $activeCount)]);
    exit();
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
