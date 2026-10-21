<?php

require_once "backend/config/session.php";

$avatar_image = "img 1.jpg";

$dashboards = [
    "patient" => "patient/dashboard.php",
    "doctor"  => "doctor/dashboard.php",
    "admin"   => "admin/dashboard.php"
];

$user_type = strtolower((string) ($_SESSION["user_type"] ?? ""));

$profile_link = $dashboards[$user_type] ?? "login.php";

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>fctCare - Home</title>
	<link rel="stylesheet" href="style.css?v=3">
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <style>
        .banner {
            background: linear-gradient(135deg, rgba(14, 116, 144, 0.45) 0%, rgba(13, 148, 136, 0.35) 100%), url('images/banner.jpg') center/cover no-repeat;
            color:#ffffff;
            padding: 80px 32px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 48px;
            box-shadow: 0 4px 6px -1px rgba(0,0, 0, 0.05),0 2px 4px -1px rgba(0, 0,0,0.03); 
              
        }
        .banner h1 {
            font-size: 45px;
            margin-bottom: 16px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4); 
        }
        .banner p {
            font-size: 19px;
            margin-bottom: 32px;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4); 
        }
        .title {
            text-align: center;
            color: #0a2d63;
            margin-bottom: 32px;
            font-size: 32px;
        }
        .title::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background-color: #00a896;
            margin: 8px auto 0;
            border-radius: 2px;
        }
        .images {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin: 32px 0 64px 0;
        }
        .item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0, 0, 0.05),0 2px 4px -1px rgba(0, 0,0,0.03);
            height: 200px;
        }
        .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        .profile-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white !important;
            
    
            background: rgba(5, 123, 115, 0.55);
            padding: 5px 16px 5px 5px;
            border-radius: 50px;
        }

        .profile-btn img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }
        .item:hover img {
            transform: scale(1.05);
            
        }

        @media (max-width: 768px) {
            .banner h1 { font-size: 32px; }
        }
 

       
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">
            <img src="logo.jpeg" alt="fctCare Logo" class="logo">
            fct<span>Care</span>
        </a>
        <button type="button" class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="doctors.php">Doctors</a></li>
            <li><a href="hospitals.php">Hospitals</a></li>
            <li><a href="appointment.php">Appointment</a></li>
            <li><a href="contact.php">Contact</a></li>
             <?php
                if (session_status() === PHP_SESSION_NONE) {
    session_start();
                }
            ?>

<li>
    <?php if (isset($_SESSION["user_type"])): ?>

        <a href="<?= htmlspecialchars($profile_link, ENT_QUOTES, 'UTF-8') ?>"
           class="profile-btn">

            <img src="<?= htmlspecialchars($avatar_image, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Profile">

            Profile

        </a>

    <?php else: ?>

        <a href="login.php" class="login-btn">🔒 Login</a>

    <?php endif; ?>
</li>


           
        </ul>
    </nav>

    <main class="container">
        <div id="greeting" style="background: #ffffff; padding: 16px 24px; 
				border-radius:8px;
				box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); 
				margin-bottom: 32px; 
				display: flex; 
				justify-content: space-between; 
				align-items: center; 
				flex-wrap: wrap; 
				gap: 10px; 
				border-left: 4px solid #00a896;">
				
			<h3 id="welcome" style="color: #0a2d63; font-weight: 600; margin: 0;"> Loading greetings...</h3>
			
            <div id="time" style="color:#1e293b; font-weight: 500; font-variant-numeric: tabular-nums; background: #f1f5f9; padding: 6px 16px; border-radius: 20px; font-size: 0.95rem;">Loading current time...</div>
        </div>

        <header class="banner">
            <h1>Your Health, Our Commitment</h1>
            <p>Experience Sri Lanka's leading e-channeling framework. Connect instantly with top medical experts and schedule care sessions right from the comfort of your home.</p>
            <div>
                <a href="appointment.php" class="btn">Book Appointment Now</a>
                <a href="doctors.php" class="btn btn_2" style="color: white; border-color: white; margin-left: 10px;">Find Specialized Doctors</a>
            </div>
        </header>

        <section>
            <h2 class="title">Inside Our Healthcare Network</h2>
            <div class="images">
                <div class="item"><img src="images/consulation.webp" alt="Consultation Room"></div>
                <div class="item"><img src="images/doctor_consulation.jpg" alt="Doctor consultation"></div>
                <div class="item"><img src="images/Hospital_Reception.jpg" alt="Hospital Reception"></div>
            </div>
        </section>

        <section style="margin-top: 48px;">
            <h2 class="title">Our Core Healthcare Services</h2>
            <div class="grids">
                <div class="card">
                    <h3 style="color: #0a2d63; margin-bottom: 8px;">Expert E-Channeling</h3>
                    <p>Gain priority routing access to renowned consultants and medical professors across various clinics effortlessly.</p>
                </div>
                <div class="card">
                    <h3 style="color: #0a2d63; margin-bottom: 8px;">Hospital Integration</h3>
                    <p>Choose from a curated network of leading medical centers and private hospitals tailored to your regional needs.</p>
                </div>
                <div class="card">
                    <h3 style="color: #0a2d63; margin-bottom: 8px;">24/7 Digital Care Desk</h3>
                    <p>Easily access real-time appointment tracking, cancellation notifications, and immediate care schedule queries.</p>
                </div>
            </div>
        </section>

        <div class="images">
                <div class="item"><img src="images/experts.jpg" alt="Expert E-Channeling"></div>
                <div class="item"><img src="images/hospitals_intergration.jpg" alt="Hospital Intergration"></div>
                <div class="item"><img src="images/24hour.jpg" alt="24/7 Digital Care Desk"></div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="index.php" class="footer-logo">
                    <img src="logo.jpeg" alt="fctCare Logo">
                    <span>fctCare</span>
                </a>
                <p>Sri Lanka's e-channeling framework. Book appointments with top doctors and leading hospitals from the comfort of your home.</p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="doctors.php">Doctors</a></li>
                    <li><a href="hospitals.php">Hospitals</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col footer-contact">
                <div class="contact-item">
                    <h4>Phone Number</h4>
                    <a href="tel:+94112345678">+94 11 234 5678</a>
                </div>
                <div class="contact-item">
                    <h4>Email Address</h4>
                    <a href="mailto:info@fctcare.com">info@fctcare.com</a>
                </div>
                <div class="contact-item">
                    <h4>eChannelling Hotline Operating Hours</h4>
                    <span>7:00 AM – 9:00 PM</span>
                </div>
            </div>

            <div class="footer-col footer-contact">
                <div class="contact-item">
                    <h4>Address</h4>
                    <address style="font-style: normal;">
                        eChannelling PLC, No: 108,<br>
                        W A D Ramanayake Mawatha,<br>
                        Colombo 2, Sri Lanka
                    </address>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 fctCare. Book. Channel. Care.</p>
            <p class="footer_text">FCT • KELANIYA</p>
            <a href="#" class="back-to-top" aria-label="Back to top">↑ Back to top</a>
        </div>
    </footer>

    <script>
        function updateTime() {
            const hour = new Date().getHours();

            if (hour < 12) {
                document.getElementById("welcome").textContent =
                    "Good Morning! Welcome to fctCare Portal.";
            } else if (hour < 13) {
                document.getElementById("welcome").textContent =
                    "Good Afternoon! Welcome to fctCare Portal.";
            } else if(hour < 19) {
                document.getElementById("welcome").textContent =
                    "Good Evening! Welcome to fctCare Portal.";
            }
            else {
                document.getElementById("welcome").textContent =
                    "Good Night! Welcome to fctCare Portal.";
            }

            document.getElementById("time").innerHTML =
                "Current Time: <strong>" +
                new Date().toLocaleTimeString() +
                "</strong>";
        }

        updateTime();
        setInterval(updateTime, 1000);

        // Mobile hamburger menu toggle
        (function () {
            const btn = document.getElementById("hamburger");
            const menu = document.getElementById("navLinks");

            btn.addEventListener("click", function () {
                const isOpen = menu.classList.toggle("show");
                btn.classList.toggle("open", isOpen);
                btn.setAttribute("aria-expanded", isOpen ? "true" : "false");
            });

            // Close the menu after tapping a link
            menu.querySelectorAll("a").forEach(function (link) {
                link.addEventListener("click", function () {
                    menu.classList.remove("show");
                    btn.classList.remove("open");
                    btn.setAttribute("aria-expanded", "false");
                });
            });
        })();
    </script>
    
    <script src="script.js"></script>
</body>
</html>