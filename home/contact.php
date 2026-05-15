<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Little Haven Elite</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Shared CSS -->
    <link rel="stylesheet" href="home.css">
    
    <style>
        .contact-hero {
            padding: 180px 0 80px;
            background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
            text-align: center;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 60px;
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .contact-info-card {
            background: var(--secondary);
            color: white;
            padding: 50px;
            border-radius: 40px;
            box-shadow: 0 30px 60px rgba(26, 82, 118, 0.2);
        }

        .contact-info-card h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item i {
            font-size: 1.5rem;
            color: var(--primary);
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 15px;
        }

        .info-item div h4 {
            margin: 0 0 5px;
            font-size: 1.1rem;
        }

        .info-item div p {
            margin: 0;
            opacity: 0.8;
            line-height: 1.6;
        }

        .contact-form-card {
            background: white;
            padding: 50px;
            border-radius: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #475569;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #f1f5f9;
            border-radius: 15px;
            font-family: inherit;
            font-size: 1rem;
            transition: 0.3s;
            background: #f8fafc;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(38, 198, 218, 0.1);
        }

        .map-container {
            height: 450px;
            background: #e2e8f0;
            border-radius: 40px;
            margin-top: 80px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        .social-circle {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .social-circle a {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .social-circle a:hover {
            background: var(--primary);
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <nav class="container nav">
            <a href="home.php" class="logo">
                <i class="fas fa-hands-holding-child logo-icon"></i>
                <span class="logo-text">Little Haven</span>
            </a>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="home.php#services">Services</a></li>
                <li><a href="home.php#why-choose-us">Why Us</a></li>
                <li><a href="home.php#facilities">Facilities</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
            </ul>
            <div class="nav-cta">
                <a href="../login/login.php" class="btn btn-primary btn-nav">Login</a>
            </div>
        </nav>
    </header>

    <!-- Hero -->
    <section class="contact-hero">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin: 0;">Get in <span>Touch</span></h1>
            <p style="color: #64748b; margin-top: 15px;">Have questions? We're here to help your child thrive.</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="section-padding" style="padding-top: 0;">
        <div class="container contact-container">
            <!-- Info Side -->
            <div class="contact-info-card reveal fade-up">
                <h2>Contact Information</h2>
                <p style="opacity: 0.7; margin-bottom: 40px;">Fill out the form and our team will get back to you within 24 hours.</p>
                
                <div class="info-item">
                    <i class="fas fa-phone-volume"></i>
                    <div>
                        <h4>Phone Number</h4>
                        <p>+94 11 234 5678</p>
                        <p>+94 77 987 6543</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope-open-text"></i>
                    <div>
                        <h4>Email Address</h4>
                        <p>hello@littlehaven.com</p>
                        <p>admissions@littlehaven.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-location-dot"></i>
                    <div>
                        <h4>Our Location</h4>
                        <p>123 Elite Avenue, Sky City,<br>Colombo 07, Sri Lanka</p>
                    </div>
                </div>

                <div class="social-circle">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Form Side -->
            <div class="contact-form-card reveal fade-up" style="transition-delay: 0.2s;">
                <form action="#" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" placeholder="John" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" placeholder="Doe" required>
                        </div>
                        <div class="form-group full-width">
                            <label>Email Address</label>
                            <input type="email" placeholder="john@example.com" required>
                        </div>
                        <div class="form-group full-width">
                            <label>Subject</label>
                            <input type="text" placeholder="Inquiry about Enrollment">
                        </div>
                        <div class="form-group full-width">
                            <label>Your Message</label>
                            <textarea rows="5" placeholder="Tell us how we can help..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 18px; font-size: 1.1rem; justify-content: center;">Send Message <i class="fas fa-paper-plane" style="margin-left: 10px;"></i></button>
                </form>
            </div>
        </div>

        <div class="container">
            <div class="map-container reveal zoom-in">
                <!-- Embedded Google Map Placeholder -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31686.438848419614!2d79.843657!3d6.927079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2591901a88673%3A0x6b1f2e9d72c6762e!2sColombo%2007!5e0!3m2!1sen!2slk!4v1688921234567!5m2!1sen!2slk" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-column">
                <a href="home.php" class="logo" style="color: white; margin-bottom: 30px;">
                    <i class="fas fa-hands-holding-child logo-icon" style="color: var(--primary); font-size: 2.5rem;"></i>
                    <span class="logo-text" style="background: white; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Little Haven</span>
                </a>
                <p class="footer-info">Setting the gold standard for early childhood education and management since 2015.</p>
            </div>
            <div class="footer-column">
                <h4>Explore</h4>
                <ul class="footer-links">
                    <li><a href="home.php">Our Philosophy</a></li>
                    <li><a href="home.php#services">Learning Paths</a></li>
                    <li><a href="about.php">The Facility</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Connect</h4>
                <p class="footer-info">123 Elite Avenue, Sky City<br>hello@littlehaven.com</p>
            </div>
        </div>
        <div class="container footer-bottom">
            &copy; 2026 Little Haven Elite. All Rights Reserved.
        </div>
    </footer>

    <script src="home.js"></script>
</body>
</html>
