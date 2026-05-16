<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Haven | Elite Daycare Management</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="home.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Decorative Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Header / Navbar -->
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
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-cta">
                <a href="../login/login.php" class="btn btn-primary btn-nav">Login</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container hero-container">
            <div class="hero-content reveal fade-up">
                <h1>Crafting <br><span>Bright Futures</span></h1>
                <p>Safe & Caring Daycare for Your Little Ones</p>
                <p><i>"An elite educational environment where play meets purpose. We nurture the next generation of leaders with love and expert care."</i></p>
                <div class="hero-btns">
                    <a href="../register/register.php" class="btn btn-primary">Enroll Today</a>
                    <a href="#services" class="btn btn-outline" style="margin-left: 15px;">Discover Programs</a>
                </div>
            </div>
            <div class="hero-image-wrapper reveal zoom-in">
                <div class="hero-decor"></div>
                <img src="../assets/hero_teal.png" alt="Little Haven Kids" class="hero-image">
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section-padding bg-alt" style="border-radius: 100px 100px 0 0;">
        <div class="container">
            <div class="section-header reveal fade-up">
                <h2>Our <span>Premium Services</span></h2>
                <p>Tailored programs designed for every stage of your child's early development.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card reveal fade-up" style="transition-delay: 0.1s;">
                    <div class="icon"><i class="fas fa-sun"></i></div>
                    <h3>Full Day Care</h3>
                    <p>Comprehensive care from morning to evening, focusing on all-round development.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.2s;">
                    <div class="icon"><i class="fas fa-cloud-sun"></i></div>
                    <h3>Half Day Care</h3>
                    <p>Flexible morning or afternoon sessions perfect for transitional learning.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.3s;">
                    <div class="icon"><i class="fas fa-school"></i></div>
                    <h3>After School Programs</h3>
                    <p>Safe and engaging activities for school-goers, including homework support.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.4s;">
                    <div class="icon"><i class="fas fa-palette"></i></div>
                    <h3>Early Learning Activities</h3>
                    <p>Specialized sessions to spark creativity and cognitive skills in toddlers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section id="why-choose-us" class="section-padding">
        <div class="container">
            <div class="section-header reveal fade-up">
                <h2>Why Choose <span>Little Haven?</span></h2>
                <p>We set the standard for safety, education, and child development.</p>
            </div>
            
            <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="feature-card reveal fade-up" style="transition-delay: 0.1s;">
                    <div class="icon" style="background: rgba(38, 198, 218, 0.1); color: var(--primary);"><i class="fas fa-user-graduate"></i></div>
                    <h4 style="margin: 15px 0;">Qualified Staff</h4>
                    <p style="font-size: 0.85rem;">Certified experts in early childhood education.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.2s;">
                    <div class="icon" style="background: rgba(38, 198, 218, 0.1); color: var(--primary);"><i class="fas fa-shield-heart"></i></div>
                    <h4 style="margin: 15px 0;">Safe Environment</h4>
                    <p style="font-size: 0.85rem;">Designed with the highest safety standards.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.3s;">
                    <div class="icon" style="background: rgba(38, 198, 218, 0.1); color: var(--primary);"><i class="fas fa-video"></i></div>
                    <h4 style="margin: 15px 0;">CCTV Monitoring</h4>
                    <p style="font-size: 0.85rem;">24/7 surveillance for absolute transparency.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.4s;">
                    <div class="icon" style="background: rgba(38, 198, 218, 0.1); color: var(--primary);"><i class="fas fa-apple-whole"></i></div>
                    <h4 style="margin: 15px 0;">Healthy Meals</h4>
                    <p style="font-size: 0.85rem;">Nutritious, chef-prepared meals for growing bodies.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.5s;">
                    <div class="icon" style="background: rgba(38, 198, 218, 0.1); color: var(--primary);"><i class="fas fa-seedling"></i></div>
                    <h4 style="margin: 15px 0;">Development Programs</h4>
                    <p style="font-size: 0.85rem;">Curated activities for holistic growth.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="section-padding bg-alt">
        <div class="container">
            <div class="section-header reveal fade-up">
                <h2>Elite <span>Facilities</span></h2>
                <p>A world-class environment where children thrive and feel at home.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card reveal fade-up" style="transition-delay: 0.1s;">
                    <div class="icon"><i class="fas fa-chalkboard-user"></i></div>
                    <h3>Modern Classrooms</h3>
                    <p>Bright, spacious, and equipped with the latest learning tools.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.2s;">
                    <div class="icon"><i class="fas fa-icons"></i></div>
                    <h3>Creative Play Area</h3>
                    <p>Indoor and outdoor spaces designed for safe, imaginative play.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.3s;">
                    <div class="icon"><i class="fas fa-bed"></i></div>
                    <h3>Rest & Sleeping Area</h3>
                    <p>Quiet, comfortable spaces for peaceful naps and relaxation.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.4s;">
                    <div class="icon"><i class="fas fa-lock"></i></div>
                    <h3>Security System</h3>
                    <p>Advanced entry controls and secure premises for every child.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-header reveal fade-up">
                <h2>Voices of <span>Trust</span></h2>
            </div>
            
            <div class="features-grid">
                <div class="testimonial-card reveal zoom-in" style="transition-delay: 0.1s;">
                    <i class="fas fa-quote-right quote"></i>
                    <p>"Little Haven isn't just a daycare; it's a family. The progress my child has made is simply breathtaking."</p>
                    <div style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
                        <img src="https://i.pravatar.cc/100?u=1" alt="Parent" style="width: 50px; height: 50px; border-radius: 50%;">
                        <div>
                            <strong>Charitha Alwis</strong>
                            <span style="display: block; font-size: 0.8rem; color: var(--text-muted);">Parent</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal zoom-in" style="transition-delay: 0.2s;">
                    <i class="fas fa-quote-right quote"></i>
                    <p>"The level of professionalism and care is something I haven't seen elsewhere. Truly an elite institution."</p>
                    <div style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
                        <img src="https://i.pravatar.cc/100?u=2" alt="Parent" style="width: 50px; height: 50px; border-radius: 50%;">
                        <div>
                            <strong>Heshan Fernando</strong>
                            <span style="display: block; font-size: 0.8rem; color: var(--text-muted);">Parent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-column">
                <a href="home.php" class="logo" style="color: white; margin-bottom: 25px;">
                    <i class="fas fa-hands-holding-child logo-icon" style="color: var(--primary); font-size: 2.5rem;"></i>
                    <span class="logo-text" style="background: white; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Little Haven</span>
                </a>
                <p class="footer-info" style="margin-bottom: 25px;">Setting the gold standard for early childhood education and management since 2015. We nurture the next generation of leaders with love and expert care.</p>
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h4>Explore</h4>
                <ul class="footer-links">
                    <li><a href="about.php#vision-mission">Vision & Mission</a></li>
                    <li><a href="home.php#services">Our Services</a></li>
                    <li><a href="home.php#why-choose-us">Why Choose Us</a></li>
                    <li><a href="home.php#facilities">The Facility</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="../login/login.php">Login Portal</a></li>
                    <li><a href="../register/register.php">Registration</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Get In Touch</h4>
                <div class="footer-info">
                    <p style="margin-bottom: 15px;"><i class="fas fa-location-dot" style="color: var(--primary); margin-right: 10px;"></i> 123 Elite Avenue, Sky City, Colombo 07</p>
                    <p style="margin-bottom: 15px;"><i class="fas fa-phone" style="color: var(--primary); margin-right: 10px;"></i> +94 11 234 5678</p>
                    <p style="margin-bottom: 15px;"><i class="fas fa-envelope" style="color: var(--primary); margin-right: 10px;"></i> hello@littlehaven.com</p>
                    <p><i class="fas fa-clock" style="color: var(--primary); margin-right: 10px;"></i> Mon - Fri: 7am - 6pm</p>
                </div>
            </div>
        </div>
        <div class="container footer-bottom">
            &copy; 2026 Little Haven Elite. All Rights Reserved.
        </div>
    </footer>

    <div class="scroll-top glass"><i class="fas fa-arrow-up"></i></div>

    <!-- Custom JS -->
    <script src="home.js?v=<?php echo time(); ?>"></script>
</body>
</html>