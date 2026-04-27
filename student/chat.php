<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
require_once "../database/db_connect.php";

/* check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Get student's hostel name */
$stmt = $conn->prepare("SELECT h.hostel_name, s.hostel_id FROM students s 
                        JOIN hostels h ON s.hostel_id = h.hostel_id
                        WHERE s.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$hostel_name = $result['hostel_name'] ?? 'Hostel';
$hostel_id = $result['hostel_id'] ?? null;

if (!$hostel_id) {
    die("Hostel information not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GBM - Live Discussion</title>

<style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background: #f4f7fb;
}

/* Layout */
.dashboard {
  display: flex;
  min-height: 100vh;
}

/* Sidebar */
.sidebar {
  width: 240px;
  background: linear-gradient(180deg, #0f2027, #203a43, #2c5364);
  color: white;
  padding: 25px;
  overflow-y: auto;
}

.sidebar h2 {
  text-align: center;
  margin-bottom: 35px;
}

.sidebar a {
  display: block;
  color: white;
  text-decoration: none;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 6px;
}

.sidebar a:hover {
  background: rgba(255, 255, 255, 0.15);
}

.sidebar a.active {
  background: rgba(255, 255, 255, 0.25);
  border-left: 4px solid #00d2d3;
}

/* Content */
.content {
  flex: 1;
  padding: 30px;
  display: flex;
  flex-direction: column;
}

/* Header */
.gbm-header {
  background: white;
  padding: 25px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.gbm-header h1 {
  font-size: 28px;
  color: #1e293b;
}

.gbm-header .info {
  text-align: right;
}

.gbm-header .info p {
  color: #666;
  margin: 4px 0;
}

.active-count {
  background: #10b981;
  color: white;
  padding: 8px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

/* Chat Container */
.chat-container {
  display: flex;
  flex-direction: column;
  flex: 1;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

/* Messages Area */
.messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 25px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.message-bubble {
  background: #f1f5f9;
  padding: 14px 18px;
  border-radius: 12px;
  border-left: 4px solid #1aa6a6;
  word-wrap: break-word;
  animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.message-bubble.own {
  background: #e0f2fe;
  border-left-color: #1e5aa8;
  align-self: flex-start;
}

.message-time {
  font-size: 12px;
  color: #999;
  margin-top: 6px;
}

.message-date {
  text-align: center;
  color: #aaa;
  font-size: 12px;
  margin: 10px 0;
  padding: 5px 0;
}

.empty-messages {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #999;
  text-align: center;
}

.empty-messages div {
  font-size: 14px;
}

/* Loading Indicator */
.loading {
  text-align: center;
  padding: 10px;
  color: #999;
  font-size: 13px;
}

/* Input Area */
.input-area {
  padding: 20px 25px;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.input-wrapper {
  display: flex;
  gap: 12px;
}

#messageInput {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  resize: none;
  max-height: 100px;
}

#messageInput:focus {
  outline: none;
  border-color: #1aa6a6;
  box-shadow: 0 0 0 2px rgba(26, 166, 166, 0.2);
}

.send-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #1e5aa8, #3b82f6);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
  white-space: nowrap;
}

.send-btn:hover {
  background: linear-gradient(135deg, #1a4a8a, #2563eb);
  transform: translateY(-2px);
}

.send-btn:active {
  transform: translateY(0);
}

.send-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
  transform: none;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    display: flex;
  }

  .gbm-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }

  .gbm-header .info {
    text-align: center;
  }
}

</style>

</head>
<body>

<div class="dashboard">

<!-- Sidebar -->
<div class="sidebar">
<h2>ARUVI</h2>
<a href="dashboard.php">Dashboard</a>
<a href="attendance.php">Attendance</a>
<a href="mess.php">Mess</a>
<a href="chat.php">Community Chat</a>
<a href="complaints.php">Complaints</a>
<a href="fees.php">Fees</a>
<a href="feedback.php">Feedback</a>
<a href="services.php">Services</a>
<a href="notifications.php">Notifications</a>
<a href="../auth/logout.php">Logout</a>
</div>

<!-- Content -->
<div class="content">

<!-- Header -->
<div class="gbm-header">
  <div>
    <h1>🎤 Community Chat</h1>
    <p style="font-size: 14px; color: #666; margin-top: 5px;"><?= htmlspecialchars($hostel_name) ?></p>
  </div>
  <div class="info">
    <div class="active-count" id="activeUsers">
      <span id="userCount">0</span> Active
    </div>
  </div>
</div>

<!-- Chat Container -->
<div class="chat-container">

  <!-- Messages Area -->
  <div class="messages-area" id="messagesArea">
    <div class="empty-messages">
      <div>
        <p>💬 No messages yet</p>
        <p style="font-size: 12px; margin-top: 5px;">Be the first to start the discussion!</p>
      </div>
    </div>
  </div>

  <!-- Input Area -->
  <div class="input-area">
    <div class="input-wrapper">
      <textarea 
        id="messageInput" 
        placeholder="Type your anonymous message here... (Max 500 characters)"
        rows="1"
        maxlength="500">
      </textarea>
      <button class="send-btn" id="sendBtn">Send</button>
    </div>
    <div style="font-size: 12px; color: #999; margin-top: 8px;">
      <span id="charCount">0</span>/500 characters
    </div>
  </div>

</div>

</div>

</div>

<script>

const messagesArea = document.getElementById('messagesArea');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const charCount = document.getElementById('charCount');
const activeUsersEl = document.getElementById('userCount');

let lastMessageTime = {};
let loadedMessageIds = new Set();
let lastloadTime = 0;

// Auto-resize textarea
messageInput.addEventListener('input', function() {
  this.style.height = '40px';
  this.style.height = Math.min(this.scrollHeight, 100) + 'px';
  charCount.textContent = this.value.length;
});

// Send message
sendBtn.addEventListener('click', sendMessage);
messageInput.addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

async function sendMessage() {
  const message = messageInput.value.trim();
  
  if (!message) {
    alert('Please enter a message');
    return;
  }

  sendBtn.disabled = true;
  sendBtn.textContent = 'Sending...';

  try {
    const response = await fetch('chat_handler.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=save&message=${encodeURIComponent(message)}`
    });

    const data = await response.json();

    if (data.success) {
      messageInput.value = '';
      charCount.textContent = '0';
      messageInput.style.height = '40px';
      await loadMessages();
      scrollToBottom();
    } else {
      alert(data.error || 'Failed to send message');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error sending message');
  } finally {
    sendBtn.disabled = false;
    sendBtn.textContent = 'Send';
  }
}

async function loadMessages() {
  try {
    const response = await fetch(`chat_handler.php?action=get&limit=100`);
    const data = await response.json();

    if (data.success) {
      let currentDate = '';
      messagesArea.innerHTML = '';

      if (data.messages.length === 0) {
        messagesArea.innerHTML = `
          <div class="empty-messages">
            <div>
              <p>💬 No messages yet</p>
              <p style="font-size: 12px; margin-top: 5px;">Be the first to start the discussion!</p>
            </div>
          </div>
        `;
        return;
      }

      data.messages.forEach(msg => {
        if (msg.date !== currentDate) {
          currentDate = msg.date;
          const dateEl = document.createElement('div');
          dateEl.className = 'message-date';
          dateEl.textContent = currentDate;
          messagesArea.appendChild(dateEl);
        }

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.innerHTML = `
          ${escapeHtml(msg.message)}
          <div class="message-time">${msg.time}</div>
        `;
        messagesArea.appendChild(bubble);
      });

      scrollToBottom();
    }
  } catch (error) {
    console.error('Error loading messages:', error);
  }
}

async function updateActiveUsers() {
  try {
    const response = await fetch('chat_handler.php?action=active_users');
    const data = await response.json();
    if (data.success) {
      activeUsersEl.textContent = data.active_users || 0;
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

function scrollToBottom() {
  messagesArea.scrollTop = messagesArea.scrollHeight;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Load initial messages
loadMessages();
updateActiveUsers();

// Poll for new messages every 2 seconds
setInterval(loadMessages, 2000);

// Update active users every 5 seconds
setInterval(updateActiveUsers, 5000);

</script>

</body>
</html>
