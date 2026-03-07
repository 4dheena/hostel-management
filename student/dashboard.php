<?php
/* 
====================================================
 TEMPORARY STUDENT DASHBOARD
 No authentication
 No database
 For UI development only
====================================================
*/

session_start();

/* TEMPORARY FAKE STUDENT SESSION */
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>

    <!-- Global styles (if you have them) -->
    <link rel="stylesheet" href="../assets/css/styles.css">

    <!-- Dashboard specific styles -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="layout">

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar">
        <h2 class="logo">Hostel Portal</h2>

        <nav class="menu">
            <a href="#" class="active">Dashboard</a>
            <a href="#">Feedback</a>
            <a href="#">Complaints</a>
            <a href="#">Community Chat</a>
            <a href="#">GBM</a>
            <a href="#">Fees</a>
            <a href="#">Attendance</a>
            <a href="#">Mess</a>
            <a href="#">Services</a>
            <a href="#">Notifications</a>
            <a href="#" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="content">

        <!-- PROFILE / HEADER CARD -->
        <div class="card profile-card">
            <div class="profile-placeholder"></div>
            <div>
                <h3>Welcome 👋</h3>
                <p>Student Dashboard</p>
            </div>
        </div>

        <!-- QUICK STATS -->
        <div class="stats">
            <div class="card stat">
                <h4>Fees Due</h4>
                <p>₹ 12,000</p>
            </div>

            <div class="card stat">
                <h4>Attendance</h4>
                <p>92%</p>
            </div>

            <div class="card stat">
                <h4>Complaints</h4>
                <p>2 Active</p>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="card">
            <h4>Quick Actions</h4>
            <div class="actions">
                <button>Pay Fees</button>
                <button>Raise Complaint</button>
                <button>Give Feedback</button>
            </div>
        </div>

        <!-- ANNOUNCEMENTS -->
        <div class="card">
            <h4>Announcements</h4>
            <ul class="announcement-list">
                <li>📢 General Body Meeting on Friday at 6 PM</li>
                <li>⚡ Power outage tomorrow (10 AM – 12 PM)</li>
                <li>🧹 Room inspection scheduled this weekend</li>
            </ul>
        </div>

    </main>
</div>

</body>
</html>
