<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facilities | ARUVI Hostel</title>
    
    <link rel="stylesheet" href="assets/css/facilities.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>

<body>
 
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">ARUVI</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="facilities.php" class="active">Facilities</a>
            <a href="contact.php">Contact</a>
            <a href="rules.php">Rules</a>
            <a href="announcement.php">Announcements</a>
            <a href="forms.php">Forms</a>
        </nav>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="overlay">
            <h1  >HOSTEL FACILITIES</h1>
            <p>
              Experience Comfort, Community & Convenience.   </p>
        </div>
    </section>


    
<p> <marquee style="color: 000000;"> Our Hostel provides a well-maintained and secure living space with modern amenities. Residents enjoy comfortable furnished rooms, high-speed Wi-Fi, 24/7 water and power supply, hygienic washrooms, 
    filtered drinking water, and a peaceful atmosphere ideal for focused living.</marquee> </p>
<br>
<br>
<br>


<!-- Card 1 -->
<div class="card animate">
    <div class="row">
        <div class="text">
            <h3>Rooms</h3><br>
            <p>Spacious and thoughtfully arranged, 
              the rooms offer privacy, comfort, and all essential amenities for a hassle-free living experience.</p>
        </div>
        <div class="image">
            <img src="assets/images/hostel-1.jpg" alt="Photo">
        </div>
    </div>
</div>

<!-- Card 2 -->
<div class="card animate">
    <div class="row reverse">
        <div class="text">
            <h3>Mess</h3><br>
            <p>The hostel mess hall offers a warm and welcoming space where students are served fresh, 
              home-style meals prepared with care, maintaining high standards of cleanliness and hygiene.</p>
        </div>
        <div class="image">
            <img src="assets/images/mess.jpg" alt="Photo">
        </div>
    </div>
</div>

<!-- Card 3 -->
<div class="card animate">
    <div class="row">
        <div class="text">
            <h3>Conference Hall</h3><br>
            <p>A versatile hostel hall where students can host workshops, discussions, or lectures.
               With modern seating and climate control, it combines comfort and functionality for every event.</p>
        </div>
        <div class="image">
            <img src="assets/images/hall.jpg" alt="Photo">
        </div>
    </div>
</div>

<!-- Card 4 -->
<div class="card animate">
    <div class="row reverse">
        <div class="text">
            <h3>Gym</h3><br>
            <p>Stay fit without leaving campus. Our hostel gym is equipped with essential machines, free weights, and cardio zones, 
              designed for students who want effective workouts in a convenient, accessible space.</p>
        </div>
        <div class="image">
            <img src="assets/images/gym.jpg"Photo">
        </div>
    </div>
</div>

<!-- Card 5 -->
<div class="card animate">
    <div class="row">
        <div class="text">
            <h3>laundry Service</h3><br>
            <p>Our efficient laundry service ensures clean,
               fresh clothes without hassle, giving you more time to relax and focus on what matters.</p>
        </div>
        <div class="image">
            <img src="assets/images/laundry.jpg" alt="Photo">
        </div>
    </div>
</div>

<!-- Card 6 -->
<div class="card animate">
    <div class="row reverse">
        <div class="text">
            <h3>Indoor Games</h3><br>
            <p>Our hostel provides indoor gaming options including chess,
              Snooker, carrom, table tennis, and board games, offering a space to relax and socialize.</p>
        </div>
        <div class="image">
            <img src="assets/images/sports.jpg" alt="Photo">
        </div>
    </div>
</div>



    <!-- FOOTER -->
    <footer>
        <p>© 2026 ARUVI Hostel | Kakkanad, Kochi</p>
    </footer>

    <script>
const cards = document.querySelectorAll('.animate');

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
        }
    });
}, { threshold: 0.2 });

cards.forEach(card => observer.observe(card));
</script>


</body>
</html>