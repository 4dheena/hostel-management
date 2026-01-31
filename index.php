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
  <link rel="stylesheet" href="css/styles.css" />
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
      <li><a href="#facilities">Facilities</a></li>
      <li><a href="#contact">Contact</a></li>
      <li><a href="#announcements">Announcements</a></li>
      <li><a href="#rules">Rules</a></li>
      <li><a href="#forms">Forms</a></li>
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
          <a href="apply.php"><button class="btn primary">Apply now</button></a>
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
        <h3>User Authentication</h3>
        <input type="text" placeholder="Email">
        <input type="password" placeholder="Password">
        <button class="btn primary full">Sign In</button>
      </div>
    </div>
  </section>

 