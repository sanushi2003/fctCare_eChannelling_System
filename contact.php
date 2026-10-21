<?php
require_once "backend/config/session.php";

/*
|--------------------------------------------------------------------------
| PROFILE LINK SETTINGS  (change these to match your folders)
|--------------------------------------------------------------------------
*/

$avatar_image = "img 1.jpg";   // small picture shown left of "Profile"

// user_type value  =>  dashboard page of that user
$dashboards = [
    "patient" => "patient/dashboard.php",
    "doctor"  => "doctor/dashboard.php",
    "admin"   => "admin/dashboard.php",
];

$user_type     = $_SESSION["user_type"] ?? null;
$profile_link  = $dashboards[strtolower((string) $user_type)] ?? "login.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>fctCare - Contact Us</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <style>
        body{
            background-image:url("images/c2.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .container {
            background: rgba(255, 255, 255, 0.45);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 64px;
            margin-bottom: 64px;
        }

        .contact {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .contact_info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .map iframe {
            width: 100%;
            height: 300px;
            border: 0;
            border-radius: 8px;
        }

        /* Profile button (shown after login) */
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

        @media (min-width: 768px) {
            .contact {
                flex-direction: row;
            }
            .contact_form, .contact_us {
                flex: 1;
            }
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

                    <a
                        href="<?= htmlspecialchars($profile_link, ENT_QUOTES, 'UTF-8') ?>"
                        class="profile-btn"
                    >
                        <img
                            src="<?= htmlspecialchars($avatar_image, ENT_QUOTES, 'UTF-8') ?>"
                            alt=""
                        >
                        Profile
                    </a>

                <?php else: ?>

                    <a href="login.php" class="login-btn">🔒 Login</a>

                <?php endif; ?>
            </li>
        </ul>
    </nav>

    <main class="container" style="max-width: 1000px; width: 90%;">
        <h2>Connect With Our Office</h2>
        <p style="margin-bottom: 32px;">Have inquiries regarding booking systems or medical options? Drop a message.</p>

        <div class="contact">

            <div class="contact_form">
                <form id="contactForm" action="#" method="POST">
                    <div class="form">
                        <label for="cname">Your Name</label>
                        <input type="text" id="cname" class="form_field" required>
                    </div>

                    <div class="form">
                        <label for="cEmail">Your Email Address</label>
                        <input type="email" id="cEmail" class="form_field" required>
                    </div>

                    <div class="form">
                        <label for="msg">Message / Inquiry</label>
                        <textarea id="msg" class="form_field" rows="5" placeholder="Type details here..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn_2" style="width: 100%; margin-top: 16px;">Send Inquiry Message</button>
                </form>
            </div>

            <div class="contact_us">
                <div class="contact_info">
                    <div class="info">
                        <h4>Phone Number</h4>
                        <p><a href="tel:+94112345678" style="color: inherit; text-decoration: none;">+94 11 234 5678</a></p>
                    </div>

                    <div class="info">
                        <h4>Email Address</h4>
                        <p><a href="mailto:info@fctcare.com" style="color: inherit; text-decoration: none;">info@fctcare.com</a></p>
                    </div>

                    <div class="info map">
                        <h4>Our Location</h4>
                        <iframe src ="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.331478743802!2d79.90258547475752!3d6.970164593030487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2599250f53c1d%3A0x646e6fe43eca56f3!2sFaculty%20of%20Computing%20and%20Technology%20-%20University%20of%20Kelaniya!5e0!3m2!1sen!2slk!4v1781416679292!5m2!1sen!2slk" style="border:0;"></iframe>
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
    <script>

// =========================================
// CONTACT FORM
// =========================================

const contactForm = document.getElementById("contactForm");

if (contactForm) {

    contactForm.addEventListener("submit", async function (e) {

        e.preventDefault();

        // GET VALUES
        const name    = document.getElementById("cname").value.trim();
        const email   = document.getElementById("cEmail").value.trim();
        const message = document.getElementById("msg").value.trim();

        // VALIDATION
        if (name === "") {
            alert("Please enter your name.");
            return;
        }

        if (!/^[A-Za-z\s.]+$/.test(name)) {
            alert("Please enter a valid name.");
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert("Please enter a valid email address.");
            return;
        }

        if (message === "") {
            alert("Please enter your message.");
            return;
        }

        // CREATE FORM DATA
        const formData = new FormData();

        formData.append("name", name);
        formData.append("email", email);
        formData.append("message", message);

        // SEND TO PHP
        try {

            const response = await fetch("backend/contact.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            console.log("Contact result:", result);

            if (result.success) {
                alert(result.message);
                contactForm.reset();
            } else {
                alert(result.message || "Unable to send message.");
            }

        } catch (error) {

            console.error("Contact form error:", error);
            alert("Unable to connect to the server.");

        }

    });
}


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