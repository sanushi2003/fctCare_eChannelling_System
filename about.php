


<?php

require_once "backend/config/session.php";
require_once "backend/config/db.php";


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
    <title>fctCare - About Us</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <style>
        .about {
            text-align: center;
            margin-bottom: 80px;
        }
        .about h2, .title {
            font-size: 40px;
            margin-bottom: 24px;
            position: relative;
            display: inline-block;
        }

        .background {
            position: relative;
            width: 100%;
            min-height: 380px;
            background-image: url('images/about.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 24px;
            overflow: hidden;
            margin-bottom: 3rem;
            box-shadow: 0 15px 30px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 64px 32px;
        }
        .background::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(9, 94, 111, 0.85), rgba(20, 153, 242, 0.75));
            z-index: 0;
        }
        .background > * {
            position: relative;
            z-index: 1;
        }
        .background h2 {
            color: #ffffff;
        }
        .background h2::after {
            background-color: #ffffff;
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
        
        .about h2::after, .title::after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background-color: #0d9488;
            margin: 0.5rem auto 0;
            border-radius: 10px;
        }
        .f_colour {
            font-size: 19px;
            color: #e2e8f0; 
            max-width: 800px;
            margin: 0 auto 48px;
        }
        
        .grids {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            width: 100%;
        }
        .card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            text-align: left;
        }
        .card h3 {
            font-size: 24px;
            margin-bottom: 12px;
            color: #ffffff;
        }
        .card p {
            font-size: 16px;
            color: #f1f5f9;
            line-height: 1.5;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            background: #0d9488;
            color: white;
            padding: 64px 32px;
            border-radius: 24px;
            margin-bottom: 80px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(13, 148, 136, 0.2);
        }
        .stat_number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .features {
            text-align: center;
            margin-bottom: 80px;
        }
        .f_grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            margin-top: 48px;
        }
        .f_item {
            background: #ffffff;
            padding: 40px 32px;
            border: 2px solid #0d9488;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(13, 148, 136, 0.05);
            transition: 0.3s ease;
        }
        .f_item:hover {
            background: #f0fdfa;
            transform: translateY(-3px);
        }
        .f_item h4 {
            font-size: 20px;
            margin-bottom: 12px;
            color: #0d9488;
        }
        .f_item p {
            color: #64748b;
            font-size: 19px;
        }

        .faq {
            text-align: center;
            margin-bottom: 80px;
        }
        .title_2 {
            color: #64748b;
            margin-bottom: 48px;
            font-size: 18px;
        }
        .faq_container {
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }
        .faq_box {
            background: #ffffff;
            border: 2px solid #0d9488;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.03);
            overflow: hidden;
            text-align: left;
        }
        .question {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 19px 24px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }
        .faq_num {
            color: #0d9488;
            font-weight: 700;
            font-size: 19px;
        }
        .faq_text {
            flex: 1;
            color: #1e293b;
        }
        .faq_ans {
            background-color: #f0fdfa;
            border-top: 1px solid #0d9488;
            display: none;
        }
        .faq_ans p {
            padding: 19px 24px 19px 56px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
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
            </li>
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
        <section class="about background">
            <h2>About fctCare</h2>
            <p class="f_colour">Welcome to our comprehensive e-channeling platform designed to bridge the gap between patients, healthcare professionals, and medical institutions seamlessly.</p>
            
            <div class="grids">
                <div class="card">
                    <h3>Our Vision</h3>
                    <p>To digitize and ease the healthcare channeling infrastructure for premium efficiency and accessible medical operations.</p>
                </div>
                <div class="card">
                    <h3>Our Mission</h3>
                    <p>Providing real-time scheduling connectivity tools matching patient requirements safely, reliably, and securely.</p>
                </div>
            </div>
        </section>
        
        <?php


/* Count Specialist Doctors */
$doctorQuery = "SELECT COUNT(*) AS total FROM doctors";
$doctorResult = mysqli_query($conn, $doctorQuery);
$doctorData = mysqli_fetch_assoc($doctorResult);
$totalDoctors = $doctorData['total'];

/* Count Partner Hospitals */
$hospitalQuery = "SELECT COUNT(*) AS total FROM hospitals";
$hospitalResult = mysqli_query($conn, $hospitalQuery);
$hospitalData = mysqli_fetch_assoc($hospitalResult);
$totalHospitals = $hospitalData['total'];

/* Count Patients */
$patientQuery = "SELECT COUNT(*) AS total FROM patients";
$patientResult = mysqli_query($conn, $patientQuery);
$patientData = mysqli_fetch_assoc($patientResult);
$totalPatients = $patientData['total'];
?>

<div class="stats">

    <div class="stat_box">
        <h3 class="stat_number">
            <?php echo $totalDoctors; ?>+
        </h3>
        <p>Specialist Doctors</p>
    </div>

    <div class="stat_box">
        <h3 class="stat_number">
            <?php echo $totalHospitals; ?>+
        </h3>
        <p>Partner Hospitals</p>
    </div>

    <div class="stat_box">
        <h3 class="stat_number">
            <?php echo $totalPatients; ?>+
        </h3>
        <p>Patients</p>
    </div>

</div>

        <div class="features">
            <h2 class="title">Why Choose fctCare?</h2>
            <div class="f_grid">
                <div class="f_item">
                    <h4>Secure Booking</h4>
                    <p>Your medical data and personal details are completely encrypted and safe with us.</p>
                </div>
                <div class="f_item">
                    <h4>Real-time Updates</h4>
                    <p>Get instant SMS or Email notification alerts regarding your appointment status.</p>
                </div>
                <div class="f_item">
                    <h4>24/7 Support</h4>
                    <p>Our dedicated team is ready to assist you around the clock for any technical issues.</p>
                </div>
            </div>
        </div>

        <div class="faq">
            <h2 class="title">Frequently Asked Questions</h2>
            <p class="title_2">Have questions? We're here to help you understand how fctCare operates.</p>
            
            <div class="faq_container">
                
                <div class="faq_box">
                    <div class="question">
                        <span class="faq_num">01.</span>
                        <span class="faq_text">How do I cancel or reschedule my appointment?</span>
                    </div>
                    <div class="faq_ans">
                        <p>You can easily cancel or reschedule your appointment through the profile dashboard, or by contacting our 24/7 customer support line at least 2 hours before the scheduled time.</p>
                    </div>
                </div>

                <div class="faq_box">
                    <div class="question">
                        <span class="faq_num">02.</span>
                        <span class="faq_text">Is my online payment and medical data secure?</span>
                    </div>
                    <div class="faq_ans">
                        <p>Yes, absolutely. fctCare uses bank-grade encryption technologies. Your personal information, payment details, and medical history are strictly confidential.</p>
                    </div>
                </div>

                <div class="faq_box">
                    <div class="question">
                        <span class="faq_num">03.</span>
                        <span class="faq_text">Will I get a refund if the doctor cancels the session?</span>
                    </div>
                    <div class="faq_ans">
                        <p>Yes. If a doctor cancels a session, a full refund (including the channeling fee) will be automatically credited back to your bank account within 3-5 working days.</p>
                    </div>
                </div>

            </div>
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


    <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.question').click(function() {
                var answer = $(this).next('.faq_ans');
                $('.faq_ans').not(answer).slideUp();
                answer.slideToggle();
            });
        });

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
</body>
</html>