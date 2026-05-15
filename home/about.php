<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Little Haven Elite</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Shared CSS -->
    <link rel="stylesheet" href="home.css">
    
    <style>
        .about-hero {
            padding: 180px 0 100px;
            background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
            text-align: center;
            border-radius: 0 0 100px 100px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-top: 80px;
        }

        .about-image {
            width: 100%;
            border-radius: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        }

        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 60px;
        }

        .mv-card {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .mv-card:hover { transform: translateY(-10px); }

        .mv-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }

        .team-card {
            text-align: center;
        }

        .team-img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
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
                <li><a href="about.php" class="active">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-cta">
                <a href="../login/login.php" class="btn btn-primary btn-nav">Login</a>
            </div>
        </nav>
    </header>

    <!-- Hero -->
    <section class="about-hero">
        <div class="container">
            <span class="breadcrumb">Our Story</span>
            <h1 style="font-size: 3.5rem; margin: 10px 0;">Nurturing the <br><span>Next Generation</span></h1>
            <p style="max-width: 700px; margin: 20px auto; color: #64748b; font-size: 1.2rem;">Little Haven began with a simple vision: to create a space where every child feels seen, loved, and inspired to explore their full potential.</p>
        </div>
    </section>

    <!-- Content -->
    <section class="section-padding">
        <div class="container">
            <div class="about-grid">
                <div class="reveal fade-up">
                    <h2 style="font-size: 2.5rem; margin-bottom: 30px;">Founded on <span>Excellence</span></h2>
                    <p style="line-height: 1.8; color: #475569; margin-bottom: 20px;">For over a decade, Little Haven has been a cornerstone of premium early childhood education. We believe that the first five years are the most critical in a human's life, and we dedicate ourselves to making them extraordinary.</p>
                    <p style="line-height: 1.8; color: #475569;">Our state-of-the-art facility is designed not just for safety, but for wonder. From our bio-dynamic gardens to our STEAM-focused play zones, every corner of Little Haven is a learning opportunity.</p>
                </div>
                <div class="reveal zoom-in">
                    <img src="../assets/about_hero.png" alt="About Little Haven" class="about-image">
                </div>
            </div>

            <div class="mission-vision">
                <div class="mv-card reveal fade-up" style="transition-delay: 0.1s;">
                    <i class="fas fa-eye"></i>
                    <h3>Our Vision</h3>
                    <p>To be the world's most trusted name in early childhood management, setting the standard for care and innovation.</p>
                </div>
                <div class="mv-card reveal fade-up" style="transition-delay: 0.2s;">
                    <i class="fas fa-bullseye"></i>
                    <h3>Our Mission</h3>
                    <p>To provide an elite, safe, and stimulating environment that empowers children to become confident, lifelong learners.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section-padding bg-alt" style="border-radius: 100px 100px 0 0;">
        <div class="container">
            <div class="section-header reveal fade-up">
                <h2>Meet the <span>Leadership</span></h2>
                <p>An elite team of educators and child development experts.</p>
            </div>
            
            <div class="team-grid">
                <div class="team-card reveal fade-up" style="transition-delay: 0.1s;">
                    <img src="https://i.pravatar.cc/300?u=a" alt="Director" class="team-img">
                    <h3>Ms. Kavishka Weerarathne</h3>
                    <p style="color: var(--primary); font-weight: 600;">Head Administrator</p>
                </div>
                <div class="team-card reveal fade-up" style="transition-delay: 0.2s;">
                    <img src="https://i.pravatar.cc/300?u=b" alt="Head of Education" class="team-img">
                    <h3>Thomas Jayasekara</h3>
                    <p style="color: var(--primary); font-weight: 600;">Head of Curriculum</p>
                </div>
                <div class="team-card reveal fade-up" style="transition-delay: 0.3s;">
                    <img src="https://i.pravatar.cc/300?u=c" alt="Operations Manager" class="team-img">
                    <h3>Ms. Thisari Chamathka</h3>
                    <p style="color: var(--primary); font-weight: 600;">Chief of Operations</p>
                </div>
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
