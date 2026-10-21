<?php

require_once "../backend/config/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $nic = trim($_POST["nic"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $gender = $_POST["gender"] ?? "Other";
    $date_of_birth = !empty($_POST["date_of_birth"])
        ? $_POST["date_of_birth"]
        : null;
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Validate required fields
    if (
        $full_name === "" ||
        $nic === "" ||
        $phone === "" ||
        $email === "" ||
        $password === ""
    ) {
        $error = "Please fill all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";

    } elseif (!in_array($gender, ["Male", "Female", "Other"], true)) {
        $error = "Please select a valid gender.";

    } else {

        // Check whether email or NIC already exists
        $stmt = $conn->prepare(
            "SELECT id FROM patients WHERE email = ? OR nic = ?"
        );

        if (!$stmt) {
            $error = "Database error. Please try again.";

        } else {

            $stmt->bind_param("ss", $email, $nic);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $error = "Email or NIC already registered.";

            } else {

                // Hash password before saving
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Save new patient account
                $insert = $conn->prepare(
                    "INSERT INTO patients
                    (
                        full_name,
                        nic,
                        phone,
                        email,
                        password,
                        gender,
                        date_of_birth,
                        address,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')"
                );

                if (!$insert) {

                    $error = "Database error: " . $conn->error;

                } else {

                    $insert->bind_param(
                        "ssssssss",
                        $full_name,
                        $nic,
                        $phone,
                        $email,
                        $hashed_password,
                        $gender,
                        $date_of_birth,
                        $address
                    );

                    if ($insert->execute()) {

                        $success = "Registration successful! You can now login.";

                    } else {

                        if ($insert->errno == 1062) {
                            $error = "Email or NIC already registered.";
                        } else {
                            $error = "Registration failed: " . $insert->error;
                        }
                    }

                    $insert->close();
                }
            }

            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>fctCare - Patient Registration</title>
<link rel="icon" type="image/jpeg" href="../logo.jpeg">

<style>

:root {
   --primary: #0b7285;
    --primary-dark: #095c6b;
    --primary-soft: #e3f4f7;
    --text: #1f2d3d;
    --muted: #6b7a89;
    --border: #d9e3ea;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
    color: var(--text);
    background: #eef7f9;
    
}

/* ================= LAYOUT ================= */

.container {
    width: 94%;
    max-width: 1020px;
    margin: 40px auto;
}

.card {
    display: grid;
    grid-template-columns: 0.85fr 1.35fr;
    background: rgba(255, 255, 255, 0.46);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(9, 92, 107, 0.25);
    animation: rise 0.7s ease both;
}

@keyframes rise {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ================= LEFT BRAND PANEL ================= */

.brand-panel {
    position: relative;
    overflow: hidden;
    padding: 44px 34px;
    color: white;
    background: linear-gradient(160deg, var(--primary) 0%, var(--primary-dark) 100%);
    display: flex;
    flex-direction: column;
}

.brand-panel::before,
.brand-panel::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.09);
}

.brand-panel::before {
    width: 260px;
    height: 260px;
    top: -90px;
    right: -90px;
}

.brand-panel::after {
    width: 200px;
    height: 200px;
    bottom: -70px;
    left: -60px;
}

.brand-top {
    display: flex;
    align-items: center;
    gap: 0;
    font-size: 25px;
    font-weight: 700;
    letter-spacing: .5px;
    color: #0a2d63;
    text-decoration: none;
    margin-bottom: 22px;
}

.logo {
    width: 50px;
    height: 50px;
    margin-right: 10px;
    border-radius: 14px;
    object-fit: cover;
    box-shadow: 0 8px 22px rgba(5,40,50,0.20);
}

.brand-top span {
    color: #00a897d0;
}

.brand-body {
    position: relative;
    z-index: 1;
    margin-top: 48px;
}

.brand-body h2 {
    margin: 0 0 12px;
    font-size: 28px;
    line-height: 1.25;
}

.brand-body p {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    opacity: 0.88;
}

.features {
    position: relative;
    z-index: 1;
    list-style: none;
    padding: 0;
    margin: 34px 0 0;
}

.features li {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 14px;
}

.features .dot {
    flex-shrink: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.brand-foot {
    position: relative;
    z-index: 1;
    margin-top: auto;
    padding-top: 26px;
    font-size: 12px;
    opacity: 0.7;
}

/* ================= RIGHT FORM PANEL ================= */

.form-panel {
    padding: 42px 40px;
}

.form-panel h1 {
    margin: 0 0 6px;
    font-size: 28px;
    color: black;
}

.subtitle {
    margin: 0 0 26px;
    color: rgba(5, 5, 5, 0.56);
    font-size: 14px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 26px 0 16px;
    font-size: 13px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--primary);
}

.section-title:first-of-type {
    margin-top: 0;
}

.section-title::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--border);
}

.section-title .num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--primary-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-size: 13px;
    font-weight: bold;
    color: #344054;
}

input,
select,
textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    background: #fbfdfe;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(11, 114, 133, 0.14);
}

textarea {
    resize: vertical;
}

/* password eye + strength */

.pw-wrap {
    position: relative;
}

.pw-wrap input {
    padding-right: 46px;
}

.pw-wrap .toggle {
    position: absolute;
    top: 50%;
    right: 8px;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 8px;
    background: transparent;
    color: var(--muted);
    font-size: 16px;
}

.pw-wrap .toggle:hover {
    background: var(--primary-soft);
    color: var(--primary);
}

.strength {
    height: 5px;
    margin-top: 8px;
    border-radius: 5px;
    background: #e9eef2;
    overflow: hidden;
}

.strength span {
    display: block;
    height: 100%;
    width: 0;
    border-radius: 5px;
    transition: width 0.3s, background 0.3s;
}

.hint {
    margin-top: 6px;
    min-height: 16px;
    font-size: 12px;
    color: var(--muted);
}

/* ================= BUTTONS ================= */

button {
    border: none;
    cursor: pointer;
}

.submit-btn {
    width: 100%;
    margin-top: 10px;
    padding: 14px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), #1098ad);
    color: #fff;
    color: white;
    font-size: 16px;
    font-weight: bold;
    letter-spacing: 0.5px;
    box-shadow: 0 8px 20px rgba(11, 114, 133, 0.35);
    transition: transform 0.2s, box-shadow 0.2s;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 26px rgba(11, 114, 133, 0.45);
}

.submit-btn:active {
    transform: translateY(0);
}

/* ================= MESSAGES ================= */

.error {
    background: #ffe3e3;
    color: #c92a2a;
    padding: 12px 14px;
    border-radius: 10px;
    border-left: 4px solid #c92a2a;
    margin-bottom: 20px;
    font-size: 14px;
}

.success {
    background: #d3f9d8;
    color: #2b8a3e;
    padding: 12px 14px;
    border-radius: 10px;
    border-left: 4px solid #2b8a3e;
    margin-bottom: 20px;
    font-size: 14px;
}

.login-link {
    text-align: center;
    margin-top: 22px;
    font-size: 14px;
    color: var(--muted);
}

.login-link a {
    color: var(--primary);
    font-weight: bold;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

.brand-top {
    font-size: 24px;
    font-weight: 700;
    color: #0a2d63;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0;
}

.brand-top span {
    color: #00a897d0;
    
}

/* ================= RESPONSIVE ================= */

@media (max-width: 860px) {

    .card {
        grid-template-columns: 1fr;
    }

    .brand-panel {
        padding: 30px 26px;
    }

    .brand-body {
        margin-top: 26px;
    }

    .features,
    .brand-foot {
        display: none;
    }

    .form-panel {
        padding: 32px 26px;
    }
}

@media (max-width: 600px) {

    .form-row {
        grid-template-columns: 1fr;
    }
}


.footer {
    background-color: #0a2d63;
    color: #ffffff;
    text-align: center;
    padding: 32px;
    margin-top: 64px;
    border-top: 4px solid #00a896;
}

.footer_text {
    font-style: italic;
    opacity: 0.8;
    margin-top: 24px;
    font-size: 14px;
}

/* Footer contact details */
.footer-contact {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 75px;
    margin-bottom: 20px;
    text-align: left;
}

.footer-contact .contact-item h4 {
    margin: 0 0 6px 0;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.8;
}

.footer-contact .contact-item span,
.footer-contact .contact-item address {
    font-weight: 600;
}

.footer-contact .contact-item a {
    color: inherit;
    text-decoration: none;
    font-weight: 600;
}

.footer-contact .contact-item a:hover {
    text-decoration: underline;
}


/* Mobile view */
@media (max-width: 768px) {
    .footer-contact {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
}
        /* ===== Advanced footer layout (layout only - colors come from style.css) ===== */
                

        .footer-top {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1.2fr 1.4fr;
            gap: 32px;
            text-align: left;
            padding-bottom: 28px;
            margin-bottom: 20px;
            border-bottom: 1px solid color-mix(in srgb, currentColor 25%, transparent);
        }
        .footer-top h4 {
            margin: 0 0 12px 0;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }
        .footer-brand .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            color: inherit;
            margin-bottom: 12px;
        }
        .footer-brand .footer-logo img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
        }
        .footer-brand p {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.6;
            opacity: 0.85;
        }
        .footer-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .footer-links a {
            text-decoration: none;
            color: inherit;
            opacity: 0.85;
            transition: opacity 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }
        .footer-links a:hover {
            opacity: 1;
            transform: translateX(4px);
        }
        .footer-col .contact-item {
            margin-bottom: 14px;
        }
        .footer-col .contact-item h4 {
            margin-bottom: 4px;
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 600;
        }
        .footer-col .contact-item a,
        .footer-col .contact-item span,
        .footer-col .contact-item address {
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.88rem;
        }
        .footer-bottom p {
            margin: 0;
        }
        .footer-bottom .back-to-top {
            text-decoration: none;
            color: inherit;
            opacity: 0.85;
            border: 1px solid color-mix(in srgb, currentColor 35%, transparent);
            padding: 4px 14px;
            border-radius: 20px;
            transition: opacity 0.2s ease;
        }
        .footer-bottom .back-to-top:hover {
            opacity: 1;
        }

        @media (max-width: 992px) {
            .footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 560px) {
            .footer-top { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { justify-content: center; text-align: center; }
        }


</style>

</head>

<body style="
    background:
        linear-gradient(rgba(255,255,255,0.35), rgba(255,255,255,0.35)),
        url('../bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

<div class="container">

<div class="card">


    <!-- ================= LEFT BRAND PANEL ================= -->

    <aside class="brand-panel">

        <div class="brand-top">
            <img src="../logo.jpeg" alt="fctCare Logo" class="logo">
            fct<span>Care</span>
        </div>

        <div class="brand-body">
            <h2>Your health journey starts here.</h2>
            <p>
                Create a patient account to book appointments,
                find doctors and hospitals, and manage your care in one place.
            </p>
        </div>

        <ul class="features">
            <li><span class="dot">📅</span> Book appointments easily</li>
            <li><span class="dot">👨‍⚕️</span> Find trusted doctors</li>
            <li><span class="dot">🏥</span> Browse hospitals near you</li>
            <li><span class="dot">🔒</span> Your data stays secure</li>
        </ul>

        <div class="brand-foot">
            © fctCare. All rights reserved.
        </div>

    </aside>


    <!-- ================= RIGHT FORM PANEL ================= -->

    <section class="form-panel">

        <h1>Create Account</h1>

        <p class="subtitle">
            Fill in your details to register as a patient.
        </p>

        <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

        <?php endif; ?>


        <?php if ($success): ?>

        <div class="success">
            <?= htmlspecialchars($success) ?>
        </div>

        <?php endif; ?>


        <form method="POST">


            <!-- ===== PERSONAL ===== -->

            <div class="section-title">
                <span class="num">1</span> Personal Details
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Full Name *</label>
                    <input
                        type="text"
                        name="full_name"
                        placeholder="e.g. Kasun Perera"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>NIC *</label>
                    <input
                        type="text"
                        name="nic"
                        placeholder="National ID number"
                        required
                    >
                </div>

            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input
                        type="date"
                        name="date_of_birth"
                    >
                </div>

            </div>


            <!-- ===== CONTACT ===== -->

            <div class="section-title">
                <span class="num">2</span> Contact Details
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Phone *</label>
                    <input
                        type="text"
                        name="phone"
                        placeholder="07X XXX XXXX"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="you@example.com"
                        required
                    >
                </div>

            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea
                    name="address"
                    rows="2"
                    placeholder="Your home address"
                ></textarea>
            </div>


            <!-- ===== SECURITY ===== -->

            <div class="section-title">
                <span class="num">3</span> Account Security
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Password *</label>
                    <div class="pw-wrap">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            minlength="8"
                            placeholder="At least 8 characters"
                            required
                        >
                        <button type="button" class="toggle" data-target="password" aria-label="Show password">👁</button>
                    </div>
                    <div class="strength"><span id="strengthBar"></span></div>
                    <div class="hint" id="strengthText"></div>
                </div>

                <div class="form-group">
                    <label>Confirm Password *</label>
                    <div class="pw-wrap">
                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            minlength="8"
                            placeholder="Re-enter password"
                            required
                        >
                        <button type="button" class="toggle" data-target="confirm_password" aria-label="Show password">👁</button>
                    </div>
                    <div class="hint" id="matchText"></div>
                </div>

            </div>


            <button type="submit" class="submit-btn">
                Create Account
            </button>

        </form>


        <div class="login-link">

            Already have an account?

            <a href="../login.php">
                Login
            </a>

        </div>

    </section>

</div>

</div>


<script>

// Show / hide password
document.querySelectorAll(".toggle").forEach(function (btn) {
    btn.addEventListener("click", function () {
        var input = document.getElementById(btn.dataset.target);
        input.type = input.type === "password" ? "text" : "password";
    });
});

// Password strength + match check
var pw = document.getElementById("password");
var cpw = document.getElementById("confirm_password");
var bar = document.getElementById("strengthBar");
var strengthText = document.getElementById("strengthText");
var matchText = document.getElementById("matchText");

pw.addEventListener("input", function () {

    var v = pw.value;
    var score = 0;

    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
    if (/\d/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    var levels = [
        { w: "0%",   c: "transparent", t: "" },
        { w: "25%",  c: "#e03131",     t: "Weak" },
        { w: "50%",  c: "#f59f00",     t: "Fair" },
        { w: "75%",  c: "#66a80f",     t: "Good" },
        { w: "100%", c: "#2b8a3e",     t: "Strong" }
    ];

    if (v.length === 0) score = 0;

    bar.style.width = levels[score].w;
    bar.style.background = levels[score].c;
    strengthText.textContent = levels[score].t ? "Strength: " + levels[score].t : "";

    checkMatch();
});

cpw.addEventListener("input", checkMatch);

function checkMatch() {

    if (cpw.value.length === 0) {
        matchText.textContent = "";
        return;
    }

    if (pw.value === cpw.value) {
        matchText.textContent = "✔ Passwords match";
        matchText.style.color = "#2b8a3e";
    } else {
        matchText.textContent = "✖ Passwords do not match";
        matchText.style.color = "#c92a2a";
    }
}

</script>


<footer class="footer">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="../index.php" class="footer-logo">
                    <img src="../logo.jpeg" alt="fctCare Logo">
                    <span>fctCare</span>
                </a>
                <p>Sri Lanka's e-channeling framework. Book appointments with top doctors and leading hospitals from the comfort of your home.</p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../about.php">About</a></li>
                    <li><a href="../doctors.php">Doctors</a></li>
                    <li><a href="../hospitals.php">Hospitals</a></li>
                    <li><a href="../appointment.php">Appointment</a></li>
                    <li><a href="../contact.php">Contact</a></li>
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

    

</body>

</html>