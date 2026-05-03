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
    <link rel="stylesheet" href="home.css">
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
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
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
                    <a href="#contact" class="btn btn-primary">Enroll Your Child</a>
                    <a href="#services" class="btn btn-outline" style="margin-left: 15px;">Discover Programs</a>
                </div>
            </div>
            <div class="hero-image-wrapper reveal zoom-in">
                <div class="hero-decor"></div>
                <img src="../assets/hero_teal.png" alt="Little Haven Kids" class="hero-image">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="services" class="section-padding bg-alt" style="border-radius: 100px 100px 0 0;">
        <div class="container">
            <div class="section-header reveal fade-up">
                <h2>Excellence in <span>Every Detail</span></h2>
                <p>Designed to stimulate growth, creativity, and absolute safety.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card reveal fade-up" style="transition-delay: 0.1s;">
                    <div class="icon"><i class="fas fa-shield-heart"></i></div>
                    <h3>Premium Security</h3>
                    <p>Advanced biometric access and 24/7 AI-monitored environments for total peace of mind.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.2s;">
                    <div class="icon"><i class="fas fa-feather"></i></div>
                    <h3>Holistic Care</h3>
                    <p>A balanced approach focusing on emotional, physical, and cognitive development.</p>
                </div>
                <div class="feature-card reveal fade-up" style="transition-delay: 0.3s;">
                    <div class="icon"><i class="fas fa-lightbulb"></i></div>
                    <h3>Modern Curriculum</h3>
                    <p>Innovative play-based learning that prepares children for a global future.</p>
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
                            <strong>Isabella Reed</strong>
                            <span style="display: block; font-size: 0.8rem; color: var(--text-muted);">Executive Mother</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal zoom-in" style="transition-delay: 0.2s;">
                    <i class="fas fa-quote-right quote"></i>
                    <p>"The level of professionalism and care is something I haven't seen elsewhere. Truly an elite institution."</p>
                    <div style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
                        <img src="https://i.pravatar.cc/100?u=2" alt="Parent" style="width: 50px; height: 50px; border-radius: 50%;">
                        <div>
                            <strong>Marcus Thorne</strong>
                            <span style="display: block; font-size: 0.8rem; color: var(--text-muted);">Tech Entrepreneur</span>
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
                <a href="#" class="logo" style="color: white; margin-bottom: 30px;">
                    <i class="fas fa-hands-holding-child logo-icon" style="color: var(--primary); font-size: 2.5rem;"></i>
                    <span class="logo-text" style="background: white; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Little Haven</span>
                </a>
                <p class="footer-info">Setting the gold standard for early childhood education and management since 2015.</p>
            </div>
            <div class="footer-column">
                <h4>Explore</h4>
                <ul class="footer-links">
                    <li><a href="#home">Our Philosophy</a></li>
                    <li><a href="#services">Learning Paths</a></li>
                    <li><a href="#about">The Facility</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Connect</h4>
                <p class="footer-info">123 Elite Avenue, Sky City<br>hello@littlehaven.com</p>
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <div class="container footer-bottom">
            &copy; 2026 Little Haven Elite. All Rights Reserved.
        </div>
    </footer>

    <div class="scroll-top glass"><i class="fas fa-arrow-up"></i></div>

    <!-- Custom JS -->
    <script src="home.js"></script>
</body>
</html>