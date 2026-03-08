<?php
require_once "database/db_connect.php";

/* FETCH WARDENS GROUPED BY HOSTEL */

$wardenQuery = $conn->query("
SELECT h.hostel_name, w.full_name, w.phone, w.email
FROM wardens w
JOIN hostels h ON w.hostel_id = h.hostel_id
ORDER BY h.hostel_name
");

$wardens = [];

while($row = $wardenQuery->fetch_assoc()){
    $wardens[$row['hostel_name']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>Contact | ARUVI</title>
<link rel="stylesheet" href="assets/css/contact.css">

</head>


<body>

<!-- NAVBAR (same structure as your site) -->

    <header class="navbar">
        <div class="logo">ARUVI</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="facilities.php">Facilities</a>
            <a href="contact.php" class="active">Contact</a>
            <a href="announcement.php">Announcements</a>
            <a href="rules.php">Rules</a>
            <a href="forms.php">Forms</a>
        </nav>
    </header>

<!-- HERO -->

<section class="hero-contact">
<div class="hero-overlay">

<h1>Contact Aruvi Hostels</h1>
<p>Get in touch with hostel administration and wardens</p>

</div>
</section>



<div class="contact-main">

<!-- ADMIN / EMERGENCY / OFFICE -->

<div class="info-grid">

<div class="info-card">
<h3>Hostel Administration</h3>
<p>Aruvi Hostels Administration Office</p>
<p>Phone: +91 9876543210</p>
<p>Email: aruvi.hostels@gmail.com</p>
</div>

<div class="info-card">
<h3>Emergency Contact</h3>
<p>Emergency Helpline</p>
<p>+91 9876543210</p>
<p>Available 24/7</p>
</div>

<div class="info-card">
<h3>Office Hours</h3>
<p>Mon – Fri : 9:00 AM – 5:00 PM</p>
<p>Sat : 9:00 AM – 1:00 PM</p>
<p>Sunday : Closed</p>
</div>

</div>



<!-- WARDENS -->

<div class="warden-section">

<h2>Hostel Wardens</h2>

<div class="warden-grid">

<?php foreach($wardens as $hostel => $wardenList){ ?>

<div class="warden-card">

<h3><?= htmlspecialchars($hostel) ?> Hostel</h3>

<?php foreach($wardenList as $w){ ?>

<p><strong><?= htmlspecialchars($w['full_name']) ?></strong></p>
<p>Phone: <?= htmlspecialchars($w['phone']) ?></p>
<p>Email: <?= htmlspecialchars($w['email']) ?></p>

<hr>

<?php } ?>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>