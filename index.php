<?php
// Database connection details
$host = 'localhost';
$port = 3307; // Ensure this port matches if used
$dbName = 'user_auth';
$user = 'root';
$password = '';
$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

try {
    // Creating a PDO instance
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve existing event details
$stmt = $pdo->prepare("SELECT * FROM event_details LIMIT 1");
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the event details were fetched successfully
if ($event) {
    $dates = $event['dates'] ?? 'Date not set';
    $times = $event['times'] ?? 'Time not set';
    $venue = $event['venue'] ?? 'Venue not set';
} else {
    // In case no event data is found
    $dates = 'No event details found';
    $times = 'No event details found';
    $venue = 'No event details found';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <!-- bootstrap5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- icon -->
     <link rel="icon" href="assets/gallery4.jpeg">
     <!-- aos  -->
     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Lobster&family=Mr+De+Haviland&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Itim&family=Lobster&family=Mr+De+Haviland&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&display=swap" rel="stylesheet">
    <title>Remy ❤️ Melati | Wedding RSVP</title>
    
    <style>

        html{
            scroll-behavior: smooth;
        }
        /* body {
            font-family: serif;
            margin: 0;
            padding: 0;
            background-image: url('assets/sparkle.gif');
            background-color: #efa5ba; */
            /* fff1f5 */
            /* color: #6f6f6f;
            text-align: center;
            position: relative;
            padding-bottom: 10px;
        } */

        /* General Styling */
        body {
            font-family: serif;
            margin: 0;
            padding: 0;
            background-image: url('assets/sparkle.gif');
            background-color: #efa5ba;
            color: #6f6f6f;
            text-align: center;
        }

        #gallery-section {
            margin-top: 40px;
        }
        
        /* Gallery Styling */
        .gallery-section img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease-in-out;
        }
        .gallery-section img:hover {
            transform: scale(1.05);
        }

        .section-title h1 {
            font-family: "Lobster", cursive;
            font-size: 2em;
            color: #f05287;
            margin-bottom: 20px;
        }

        /* Animation Styles */
        .slide-in {
            opacity: 0;
            transition: transform 0.5s ease, opacity 0.5s ease;
        }
        
        .slide-in.left {
            transform: translateX(-50px);
        }
        
        .slide-in.right {
            transform: translateX(50px);
        }
        
        .slide-in.show {
            transform: translateX(0);
            opacity: 1;
        }

        #entrance h1{
            font-size: 64px;
        }

        #entrance::before{
            content: "";
            position: absolute;
            bottom: 0;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(241, 78, 149, 0.4);
        }

        #entrance{
            width: 100%;
            height: 100vh;
            background: url("/assets/ballroom.jpg") top left;
            background-size: cover;
            position: relative;
            background-position: center;
            z: 2;
        }

        #entrance .container{
            position: relative;
        }

        #entrance .btn-get-started{
            text-transform: uppercase;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
            display: inline-block;
            padding: 10px 28px;
            transition: 0.5s;
            color: var(--pink);
            background: var(--white);
        }

        #entrance .btn-get-started::hover {
            background: var(--pink-hover);
            color: var(--white);
        }

        .sparkle{
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .mr-de-haviland-regular {
            font-family: "Mr De Haviland", cursive;
            font-weight: 550;
            font-style: normal;
            font-size: 90px;
            color: #f05287;
        }

        .cormorant-upright-bold {
            font-family: "Cormorant Upright", serif;
            font-weight: 700;
            font-style: normal;
            font-size: 20px;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fafff7;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* Banner Section Styling */
        .banner-section {
            margin-bottom: 40px; /* Adds space between banner and RSVP */
        }

        img.banner {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        h1 {
            color: #545151;
            font-size: 3.0em;
            margin-bottom: 15px;
        }

        h2 {
            font-size: 2.7em;
            margin: 10px 0 20px;
        }

        p {
            font-size: 1.0em;
            margin: 10px 0 20px;
        }

        /* RSVP Section Styling */
        .rsvp-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 30px;
        }

        .rsvp-message {
            font-size: 1.2em;
            color: #f05287; /* A soft pink color to match the theme */
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        .rsvp-button {
            padding: 15px 30px;
            background-color: #e91e63; /* Rich pink */
            color: #fff;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.2em;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0px 4px 8px rgba(233, 30, 99, 0.4);
        }

        .rsvp-button:hover {
            background-color: #d81b60;
            transform: scale(1.05);
        }

        .update-container {
            margin-top: 10px;
        }

        .update-link {
            color: #e91e63;
            font-size: 1em;
            text-decoration: underline;
            padding: 8px 16px;
            border-radius: 4px;
            transition: color 0.3s, background-color 0.3s;
        }

        .update-link:hover {
            color: #c12664;
            background-color: #fef2f5;
        }


        .footer {
            position: relative; /* Keep it relative to the page */
            padding: 10px 20px;
            margin-top: 40px 0 0;
            text-align: right; /* Align text to the right */
        }

        .footer .staff-button {
            font-size: 1em;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            display: inline-block;
            cursor: pointer;
        }

        .footer .staff-button:hover {
            background: linear-gradient(135deg, #ff4081, #e91e63);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

           /* Typing effect for names */
        .typing {
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
            border-right: 3px solid #f05287; /* Optional blinking cursor color */
            animation: typing 3s steps(12, end), blink-caret 0.7s step-end infinite;
        }

        /* Typing animation */
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }

        /* Blinking cursor animation */
        @keyframes blink-caret {
            from, to { border-color: transparent; }
            50% { border-color: #f05287; } /* Cursor color */
        }

        .event-details {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fdf5f8;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .rsvp-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 40px auto;
            padding: 20px;
            background-color: #fdf5f8;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .event-item {
            display: flex;
            align-items: center;
            font-size: 1.2em; 
            color: #333;
        }

        .icon {
            width: 50px; 
            height: 50px; 
            margin-right: 12px; 
            opacity: 0.85; 
        }

        .divider {
            width: 3px;
            height: 60px;
            background-color: #e91e63;
            opacity: 0.7;
        }

        .map-link {
            margin-left: 10px;
            color: #e91e63; 
            font-weight: bold;
            text-decoration: none;
            padding: 5px 8px;
            border: 1px solid #e91e63;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .map-link:hover {
            background-color: #e91e63;
            color: #fff;
        }


    </style>
</head>
<body class="cormorant-upright-bold">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- aos  -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- lightbox2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/js/lightbox.min.js" integrity="sha512-KbRFbjA5bwNan6DvPl1ODUolvTTZ/vckssnFhka5cG80JVa5zSlRPCr055xSgU/q6oMIGhZWLhcbgIC0fyw3RQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>

  
    <!-- Wedding Banner Section -->
    <div class="container banner-section">
        <!-- Banner Image -->
        <img src="ring1.jpg" alt="Wedding Banner" class="banner">

        <h1>You're Invited!</h1>
        <p>We are excited to invite you to the wedding of</p>
        <h2 class="mr-de-haviland-regular"><span class="typing">Remy bin Ali</span><br>and<br><span class="typing">Melati binti Abu</span></h2>
    </div>

    <!-- Event Details Section -->
    <div class="container event-details">
        <!-- <div class="event-item">
            <img src="assets/calendar.png" alt="Date icon" class="icon">
            <span>Tuesday, October 15, 2024</span>
        </div>
        <div class="divider"></div>
        <div class="event-item">
            <img src="assets/time.png" alt="Time icon" class="icon">
            <span>4:00 PM</span>
        </div>
        <div class="divider"></div>
        <div class="event-item">
            <img src="assets/location.png" alt="Location icon" class="icon">
            <span>Parkview Hotel, Jalan Pertanian Luahan Jerudong</span>
            <a href="https://www.google.com/maps/search/Parkview+Hotel,+Jalan+Pertanian+Luahan+Jerudong,+BG3122,+Brunei" 
                    target="_blank" class="map-link">View Location</a>
        </div> -->
    

        <div class="event-item">
            <img src="assets/calendar.png" alt="Date icon" class="icon">
            <p>Date: <?php echo htmlspecialchars($dates); ?></p>
            </div>
        <div class="divider"></div>
        <div class="event-item">
            <img src="assets/time.png" alt="Time icon" class="icon">
            <p>Time: <?php echo htmlspecialchars($times); ?></p>
        </div>
        <div class="divider"></div>
        <div class="event-item">
            <img src="assets/location.png" alt="Location icon" class="icon">
            <p>Venue: <?php echo htmlspecialchars($venue); ?></p>
            <a href="https://www.google.com/maps/search/Parkview+Hotel,+Jalan+Pertanian+Luahan+Jerudong,+BG3122,+Brunei<?= urlencode($venue) ?>" target="_blank" class="map-link">View Location</a>
        </div>
    </div>

    <!-- RSVP Section -->
    <div class="container rsvp-section">
        <p class="rsvp-message">This favour of a response is requested by 1st October 2024</p>
        
        <!-- RSVP Button -->
        <a href="rsvpreg.html" class="rsvp-button">RSVP Here</a>
        
        <!-- Update RSVP Link -->
        <div class="update-container">
            <a href="login.php" class="update-link">Click here to update your RSVP!</a>
        </div>
    </div>

    <!-- Gallery Section -->
    <section id="gallery-section" class="gallery-section py-5 section-bg">
        <div class="container" data-aos="fade-up" data-aos-easing="linear" data-aos-duration="1500">
            <div class="section-title mb-4">
                <h1 class="huruf-sambung text-pink fw-bold">Gallery</h1>
            </div>
            <div class="row justify-content-center">
                <!-- Gallery Item 1 -->
                <div class="col-sm-6 col-md-4 mb-4" data-aos="fade-right" data-aos-duration="1000">
                    <a href="assets/gallery7.jpg" data-lightbox="wedding-gallery" class="text-decoration-none">
                        <img src="assets/gallery7.jpg" alt="pre-wed1" class="img-fluid border-pink rounded">
                    </a>
                </div>
                <!-- Gallery Item 2 -->
                <div class="col-sm-6 col-md-4 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <a href="assets/gallery6.jpg" data-lightbox="wedding-gallery" class="text-decoration-none">
                        <img src="assets/gallery6.jpg" alt="pre-wed2" class="img-fluid border-pink rounded">
                    </a>
                </div>
                <!-- Gallery Item 3 -->
                <div class="col-sm-6 col-md-4 mb-4" data-aos="fade-left" data-aos-duration="1000">
                    <a href="assets/gallery8.jpg" data-lightbox="wedding-gallery" class="text-decoration-none">
                        <img src="assets/gallery8.jpg" alt="pre-wed3" class="img-fluid border-pink rounded">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer with Staff Button -->
    <div class="footer">
        <a href="stafflogin.php" class="staff-button">Staff</a>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/js/lightbox.min.js"></script>
    
    <!-- Intersection Observer Script for Animations -->
    <script>
        // Intersection Observer to add 'show' class on scroll
        const slideInElements = document.querySelectorAll('.slide-in');

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target); // Stop observing once animation is triggered
                }
            });
        }, { threshold: 0.1 });

        slideInElements.forEach(element => {
            observer.observe(element);
        });
    </script>

<script>
    // Initialize AOS animation
    AOS.init();
</script>

</body>
</html>
