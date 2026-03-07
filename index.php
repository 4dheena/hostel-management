<?php
session_start();
require_once 'database/db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = "Please enter both User ID and Password.";
    } else {

        $user_id  = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare(
            "SELECT user_id,username, password, role FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role']    = $user['role'];

                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/dashboard.php");
                        exit;
                    case 'warden':
                        header("Location: warden/dashboard.php");
                        exit;
                    case 'student':
                        header("Location: student/dashboard.php");
                        exit;
                }

            } else {
                $error = "Invalid User ID or Password.";
            }

        } else {
            $error = "Invalid User ID or Password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aruvi Hostels</title>

  <!-- Font Awesome (REQUIRED for icons) -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  />

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body>

  <!-- ================= NAVBAR ================= -->
  <nav class="navbar">
    <div class="logo">
      <img src="assets/images/logo.jpeg" alt="Aruvi Logo">
      <span>ARUVI</span>
    </div>

    <ul class="nav-links">
      <li><a href="about.php">About</a></li>
      <li><a href="facilities.php">Facilities</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="announcement.php">Announcements</a></li>
      <li><a href="rules.php">Rules</a></li>
      <li><a href="forms.php">Forms</a></li>
    </ul>

  </nav>

  <!-- ================= HERO ================= -->
  <section class="hero">

    <!-- Neutral overlay (NO blue tint) -->
    <div class="overlay"></div>

    <!-- SVG wave -->
    <img src="assets/images/wave.svg" class="wave" alt="wave design">

    <div class="hero-content">
      <!-- Left -->
      <div class="hero-left">
        <h1>A Home Away from Home</h1>
        <p>
          Smart living, seamless management, and a thriving
          hostel community.
        </p>

        <div class="hero-buttons">
          <button class="btn primary" onclick= "window.location.href='apply.php'">Apply now</button>
          <button class="btn outline">View Vacancy</button>
        </div>

        <div class="stats">
          <span><i class="fa-solid fa-bed"></i> 1200+ Units</span>
          <span><i class="fa-solid fa-building"></i> 4 Blocks</span>
          <span><i class="fa-solid fa-shield-halved"></i> 24/7 Security</span>
        </div>
      </div>

      <!-- Right -->
         <div class="login-card">

    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
          <form method="POST" autocomplete="off">
    <h3>User Authentication</h3>

    <input
        type="text"
        name="username"
        placeholder="Username"
        required
    >

    <input
        type="password"
        name="password"
        placeholder="Password"
        required
    >

    <button type="submit" class="btn primary full">
        Sign In
    </button>
</form>
      </div>
    </div>
  </section>

 