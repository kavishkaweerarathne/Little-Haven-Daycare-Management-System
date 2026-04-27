<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Haven | Premium Daycare Management System</title>
    <meta name="description" content="Welcome to Little Haven, the best daycare management system. Providing a safe, fun, and educational environment for your little ones.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="home.css">
</head>
<body>

    <!-- Header / Navbar -->
    <header class="header">
        <nav class="container nav">
            <a href="home.php" class="logo">
                <img src="../assets/logo.png" alt="Little Haven Logo" style="height: 50px;">
                <span>Little Haven</span>
            </a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="login.php" class="btn btn-secondary">Login</a></li>
            </ul>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container hero-container">
            <div class="hero-content animate-fade-in">
                <h1>A Second Home for Your <span>Little Ones</span></h1>
                <p>Experience the perfect blend of safety, love, and early education. At Little Haven, we nurture every child's unique potential.</p>
                <div class="hero-btns">
                    <a href="#contact" class="btn btn-primary">Enroll Now</a>
                    <a href="#services" class="btn btn-secondary">Our Programs</a>
                </div>
            </div>
            <div class="hero-image animate-fade-in" style="animation-delay: 0.2s;">
                <img src="../assets/hero.png" alt="Children playing at Little Haven Daycare">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="services" class="section-padding bg-alt">
        <div class="container text-center">
            <h2 class="section-title">Why Choose Little Haven?</h2>
            <p class="section-subtitle">We provide a holistic environment designed to stimulate growth and happiness.</p>
            
            <div class="features-grid">
                <div class="feature-card animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="icon"><i class="fas fa-shield-heart"></i></div>
                    <h3>Safe & Secure</h3>
                    <p>CCTV monitoring and rigorous safety protocols to ensure peace of mind for parents.</p>
                </div>
                <div class="feature-card animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3>Expert Care</h3>
                    <p>Certified and passionate educators dedicated to early childhood development.</p>
                </div>
                <div class="feature-card animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="icon"><i class="fas fa-apple-whole"></i></div>
                    <h3>Healthy Meals</h3>
                    <p>Nutritious and delicious meals prepared daily to fuel growing minds and bodies.</p>
                </div>
                <div class="feature-card animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="icon"><i class="fas fa-puzzle-piece"></i></div>
                    <h3>Fun Learning</h3>
                    <p>Play-based curriculum that sparks curiosity and builds foundational skills.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section-padding">
        <div class="container about-container">
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="About Little Haven" style="border-radius: var(--radius-md);">
            </div>
            <div class="about-content">
                <h2 class="section-title">Dedicated to Excellence in <span>Early Education</span></h2>
                <p>Founded with a vision to create a nurturing environment, Little Haven has been a trusted partner for parents for over a decade. We believe every child is a star waiting to shine.</p>
                <ul class="about-list">
                    <li><i class="fas fa-check-circle"></i> Individualized attention for every child</li>
                    <li><i class="fas fa-check-circle"></i> Spacious and stimulating play areas</li>
                    <li><i class="fas fa-check-circle"></i> Regular progress reports for parents</li>
                </ul>
                <a href="#contact" class="btn btn-primary">Learn More About Us</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-padding bg-alt">
        <div class="container text-center">
            <h2 class="section-title">What Parents Say</h2>
            <p class="section-subtitle">Read how Little Haven has made a difference in the lives of families.</p>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p>"Little Haven has been a blessing. My daughter loves her teachers and has learned so much in just six months!"</p>
                    <div class="parent">
                        <img src="https://i.pravatar.cc/100?u=1" alt="Parent">
                        <div>
                            <strong>Sarah Johnson</strong>
                            <span>Mother of Lily</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p>"The security and care at Little Haven are unmatched. I can go to work knowing my son is in the best hands."</p>
                    <div class="parent">
                        <img src="https://i.pravatar.cc/100?u=2" alt="Parent">
                        <div>
                            <strong>Michael Chen</strong>
                            <span>Father of Leo</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p>"A wonderful environment that truly cares about early childhood education. Highly recommended!"</p>
                    <div class="parent">
                        <img src="https://i.pravatar.cc/100?u=3" alt="Parent">
                        <div>
                            <strong>Emily Davis</strong>
                            <span>Mother of Mia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-padding">
        <div class="container">
            <div class="contact-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
                <div class="contact-form">
                    <h2 class="section-title">Get in Touch</h2>
                    <p class="section-subtitle" style="margin-left: 0; text-align: left;">Have questions? We'd love to hear from you. Fill out the form and we'll get back to you shortly.</p>
                    <form action="#" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                        <input type="text" placeholder="Your Name" required style="padding: 15px; border-radius: var(--radius-sm); border: 1px solid #ddd;">
                        <input type="email" placeholder="Your Email" required style="padding: 15px; border-radius: var(--radius-sm); border: 1px solid #ddd;">
                        <select required style="padding: 15px; border-radius: var(--radius-sm); border: 1px solid #ddd;">
                            <option value="">Select Inquiry Type</option>
                            <option value="enrollment">Enrollment</option>
                            <option value="visit">Schedule a Visit</option>
                            <option value="other">Other</option>
                        </select>
                        <textarea placeholder="Your Message" rows="5" required style="padding: 15px; border-radius: var(--radius-sm); border: 1px solid #ddd;"></textarea>
                        <button type="submit" class="btn btn-primary" style="width: fit-content;">Send Message</button>
                    </form>
                </div>
                <div class="contact-info" style="background: var(--bg-alt); padding: 50px; border-radius: var(--radius-md);">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.8rem;">Visit Us</h3>
                    <p style="margin-bottom: 2rem;">Stop by our center to see our wonderful environment firsthand. We're open Monday to Friday, 7:00 AM - 6:00 PM.</p>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary); font-size: 1.2rem;"></i>
                            <span>123 Sunshine Street, Dreamland City</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-phone" style="color: var(--primary); font-size: 1.2rem;"></i>
                            <span>+1 234 567 890</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-envelope" style="color: var(--primary); font-size: 1.2rem;"></i>
                            <span>info@littlehaven.com</span>
                        </div>
                    </div>
                    <div class="map-placeholder" style="margin-top: 30px; height: 200px; background: #ddd; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: #888;">
                        <i class="fas fa-map" style="font-size: 3rem; margin-right: 10px;"></i> Interactive Map
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-info">
                <a href="#" class="logo">
                    <img src="../assets/logo.png" alt="Little Haven Logo" style="height: 40px;">
                    <span>Little Haven</span>
                </a>
                <p>Nurturing the leaders of tomorrow, one step at a time.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <p><i class="fas fa-map-marker-alt"></i> 123 Sunshine Street, Dreamland</p>
                <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                <p><i class="fas fa-envelope"></i> info@littlehaven.com</p>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p>&copy; 2026 Little Haven Daycare Management System. All Rights Reserved.</p>
        </div>
    </footer>

    <div class="scroll-top"><i class="fas fa-arrow-up"></i></div>

    <!-- Custom JS -->
    <script src="home.js"></script>
</body>
</html>