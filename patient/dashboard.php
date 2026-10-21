<?php

require_once "../backend/config/session.php";
require_once "../backend/config/db.php";

requirePatient();

$patient_id = $_SESSION["patient_id"];

/* =========================
   LOAD PATIENT PROFILE
========================= */

$profile = [];

$stmt = $conn->prepare("
    SELECT *
    FROM patients
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$profile_result = $stmt->get_result();

if ($profile_result->num_rows > 0) {
    $profile = $profile_result->fetch_assoc();
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| IMAGE PATHS  (change these if your images are in another folder)
|--------------------------------------------------------------------------
*/

$logo_image   = "../logo.jpeg";      // logo beside "fctCare"
$hero_image   = "patients.jpg";   // big banner image
$avatar_image = "img 1.jpg";     // small profile picture next to the name

/* =========================
   APPOINTMENT COUNTS & DETAILS
========================= */

$total_appointments = 0;
$pending = 0;
$confirmed = 0;

$all_appointments = [];
$pending_appointments = [];
$confirmed_appointments = [];

$stmt = $conn->prepare("
    SELECT email
    FROM patients
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$patient_result = $stmt->get_result();
$patient_data = $patient_result->fetch_assoc();

$stmt->close();

$patient_email = $patient_data["email"] ?? "";

if ($patient_email !== "") {

    $stmt = $conn->prepare("
        SELECT
            a.id,
            a.appointment_date,
            a.status,
            a.reason,
            a.doctor_fee,
            a.hospital_fee,
            a.total_payment,
            a.patient_name,
            d.name AS doctor_name,
            d.specialty,
            h.name AS hospital_name

        FROM appointments a

        LEFT JOIN doctors d
            ON a.doctor_id = d.id

        LEFT JOIN hospitals h
            ON a.hospital_id = h.id

        WHERE a.patient_id = ?
           OR (
                a.patient_id IS NULL
                AND LOWER(TRIM(a.patient_email)) =
                    LOWER(TRIM(?))
           )

        ORDER BY a.appointment_date DESC, a.id DESC
    ");

    $stmt->bind_param(
        "is",
        $patient_id,
        $patient_email
    );

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $all_appointments[] = $row;

        $status = strtolower(trim($row["status"] ?? ""));

        if ($status === "pending") {
            $pending_appointments[] = $row;
        }

        if ($status === "confirmed") {
            $confirmed_appointments[] = $row;
        }
    }

    $stmt->close();
}

$total_appointments = count($all_appointments);
$pending = count($pending_appointments);
$confirmed = count($confirmed_appointments);
/*
|--------------------------------------------------------------------------
| GREETING
|--------------------------------------------------------------------------
*/

date_default_timezone_set("Asia/Colombo");

$hour = (int) date("G");

if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

$patient_name = $_SESSION["patient_name"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>fctCare - Patient Dashboard</title>
<link rel="icon" type="image/jpeg" href="../logo.jpeg">

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #ffffff;
    color: #1e293b;
}

/* ---------- NAVBAR ---------- */

.navbar {
    background: white;
    color: white;
    padding: 0.5rem 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    position: sticky;
    top: 0;
    z-index: 1000;
}



.logo-img {
    
    height: 70px;
    width: auto;
    object-fit: contain;
    margin-right: 15px;
}

.user-area {
    display: flex;
    align-items: center;
    gap: 14px;
}

.user-box {
    display: flex;
    align-items: center;
    gap: 11px;
    background: rgba(5, 123, 115, 0.15);
    padding: 5px 16px 5px 5px;
    border-radius: 50px;
    color: #000000;
}

.avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #15705e;
    background: #ffffff;
   
}

.user-name {
    font-weight: 600;
    font-size: 15px;
}

.logout {
    color: white;
    text-decoration: none;
    background: #c92a2a;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 14px;
    transition: background 0.2s, transform 0.2s;
}

.logout:hover {
    background: #a61e1e;
    transform: translateY(-1px);
}

/* ---------- LAYOUT ---------- */

.container {
    width: 92%;
    max-width: 1100px;
    margin: 30px auto 50px;
}

/* ---------- HERO ---------- */

.hero {
    position: relative;
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    align-items: stretch;
    background: linear-gradient(120deg, #0b7285 0%, #0f8ca3 60%, #17a2b8 100%);
    color: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 14px 38px rgba(11, 114, 133, 0.28);
    margin-bottom: 28px;
}

.hero::before {
    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    top: -90px;
    left: -70px;
}

.hero-text {
    position: relative;
    padding: 42px 40px;
    z-index: 1;
}

.hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 6px 15px;
    border-radius: 50px;
    font-size: 13px;
    margin-bottom: 16px;
}

.hero-text h2 {
    margin: 0 0 12px;
    font-size: 32px;
    line-height: 1.25;
}

.hero-text p {
    margin: 0 0 24px;
    line-height: 1.65;
    opacity: 0.93;
}

.hero-btn {
    display: inline-block;
    background: #ffffff;
    color: #000000;
    font-weight: 600;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 10px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.hero-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.hero-image {
    position: relative;
    min-height: 280px;
}

.hero-image img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* fades the picture into the teal panel */
.hero-image::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, #0f8ca3 0%, rgba(15, 140, 163, 0) 45%);
}

/* ---------- STATS ---------- */

.section-title {
    margin: 0 0 16px;
    font-size: 19px;
    color: #000000;
}

.cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    position: relative;
    background: rgba(250, 250, 250, 0.64);
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 6px 22px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    transition: transform 0.25s, box-shadow 0.25s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(11, 114, 133, 0.18);
}

.card::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    background: #ffffff3c;
}

.card-icon {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    background: #9fd9e2b7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
}

.card h3 {
    margin: 0 0 6px;
    font-size: 15px;
    color: #576d8c;
    font-weight: 600;
}

.number {
    font-size: 38px;
    font-weight: bold;
    color: #000000;
}

/* ---------- QUICK ACTIONS ---------- */

.actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.action {
    display: flex;
    align-items: center;
    gap: 16px;
    text-decoration: none;
    background: #0b7285;
    color: white;
    padding: 20px 22px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(11, 114, 133, 0.25);
    transition: transform 0.25s, background 0.25s;
}

.action:hover {
    background: #095c6b;
    transform: translateY(-4px);
}

.action-icon {
    width: 46px;
    height: 46px;
    flex-shrink: 0;
    border-radius: 13px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.action strong {
    display: block;
    font-size: 16px;
}

.action small {
    opacity: 0.85;
    font-size: 13px;
}

/* ---------- RESPONSIVE ---------- */

@media (max-width: 800px) {

    .hero {
        grid-template-columns: 1fr;
    }

    .hero-image {
        min-height: 220px;
        order: -1;
    }

    .hero-image::after {
        background: linear-gradient(180deg, rgba(15, 140, 163, 0) 55%, #0f8ca3 100%);
    }

    .hero-text {
        padding: 28px 24px;
    }

    .hero-text h2 {
        font-size: 26px;
    }

    .cards,
    .actions {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 520px) {

    .navbar {
        padding: 12px 16px;
    }

    .user-name {
        display: none;
    }

    .user-box {
        padding: 4px;
    }

}

/* Clickable appointment cards */

.clickable-card {
    cursor: pointer;
}

.clickable-card small {
    display: block;
    margin-top: 10px;
    color: #64748b;
    font-size: 12px;
}


/* Modal background */

.appointment-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15, 23, 42, 0.65);
    padding: 25px 15px;
    overflow-y: auto;
}


/* Modal box */

.appointment-modal-content {
    background: #ffffff;
    width: 100%;
    max-width: 750px;
    margin: 30px auto;
    padding: 25px;
    border-radius: 18px;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25);
}


/* Modal header */

.appointment-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.appointment-modal-header h2 {
    color: #000000;
    font-size: 23px;
    margin: 0;
}

.close-modal {
    border: none;
    background: #fee2e2;
    color: #b91c1c;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    font-size: 23px;
    cursor: pointer;
}


/* Appointment details */

.appointment-list {
    display: grid;
    gap: 15px;
    max-height: 65vh;
    overflow-y: auto;
    padding: 3px;
}

.appointment-detail-card {
    border: 1px solid #dbe5ec;
    border-left: 5px solid #000000;
    border-radius: 12px;
    padding: 18px;
    background: #f8fafc;
}

.appointment-detail-card h3 {
    color: #0b7285;
    margin: 0 0 12px;
    font-size: 17px;
}

.appointment-detail-card p {
    margin: 7px 0;
    font-size: 14px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.appointment-status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-confirmed {
    background: #dcfce7;
    color: #166534;
}

.status-other {
    background: #e2e8f0;
    color: #334155;
}

.no-appointments {
    text-align: center;
    padding: 35px 15px;
    color: #64748b;
    background: #f8fafc;
    border-radius: 12px;
}

@media (max-width: 520px) {
    .appointment-modal-content {
        padding: 18px;
        margin: 10px auto;
    }

    .appointment-modal-header h2 {
        font-size: 19px;
    }
}

/* =========================
   MY PROFILE MODAL
========================= */

.profile-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    inset: 0;

    background: rgba(0, 0, 0, 0.6);

    justify-content: center;
    align-items: center;

    padding: 20px;
}

.profile-modal-content {
    background: #ffffff;

    width: 100%;
    max-width: 500px;

    padding: 30px;

    border-radius: 18px;

    position: relative;

    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25);

    animation: profilePopup 0.25s ease;
}

.profile-modal-content h2 {
    color: #0b7285;
    margin-bottom: 8px;
}

.profile-subtitle {
    color: #777;
    margin-bottom: 25px;
}

.profile-close {
    position: absolute;
    top: 12px;
    right: 18px;

    border: none;
    background: none;

    font-size: 30px;
    color: #555;

    cursor: pointer;
}

.profile-close:hover {
    color: #e53935;
}

.profile-details {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.profile-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    gap: 15px;

    padding: 14px 0;

    border-bottom: 1px solid #eeeeee;
}

.profile-row span {
    color: #666;
    font-size: 14px;
}

.profile-row strong {
    color: #000000;
    font-size: 14px;

    text-align: right;
    overflow-wrap: anywhere;
}

.profile-done-btn {
    width: 100%;

    margin-top: 25px;
    padding: 12px;

    border: none;
    border-radius: 8px;

    background: #0b7285;
    color: white;

    font-size: 15px;
    font-weight: bold;

    cursor: pointer;
    transition: background 0.2s;
}

.profile-done-btn:hover {
    background: #0a2d63;
}

.profile-error {
    color: #d32f2f;
    padding: 15px 0;
}

@keyframes profilePopup {
    from {
        opacity: 0;
        transform: translateY(15px) scale(0.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 480px) {
    .profile-modal-content {
        padding: 24px 18px;
    }

    .profile-row {
        gap: 10px;
    }
}

.logo {
    font-size: 24px;
    font-weight: 700;
    color: #00a896;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.logo span {
    
    color: #0a2d63;
}

/* =====================================================
   FOOTER
===================================================== */
 
.footer {
    position: relative;
    z-index: 2;
    background-color: #0a2d63;
    color: #ffffff;
    text-align: center;
    padding: 32px;
    margin-top: auto;
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
    font-style: normal;
}
 
.footer-contact .contact-item a {
    color: inherit;
    text-decoration: none;
    font-weight: 600;
}
 
.footer-contact .contact-item a:hover {
    text-decoration: underline;
}
 
@media (max-width: 768px) {
 
    .footer {
        padding: 26px 18px;
    }
 
    .footer-contact {
        gap: 22px;
        text-align: center;
        flex-direction: column;
        align-items: center;
    }
}
 
 
/* respect "reduce motion" */
@media (prefers-reduced-motion: reduce) {
 
    *, *::before, *::after {
        animation: none !important;
        transition: none !important;
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

<div class="navbar">

    <a class="logo">
        <img src="<?= htmlspecialchars($logo_image, ENT_QUOTES, 'UTF-8') ?>" alt="fctCare Logo" class="logo-img">
        <span>fct</span>Care
    </a>

    <div class="user-area">

        <div class="user-box">

            <img
                src="<?= htmlspecialchars($avatar_image, ENT_QUOTES, 'UTF-8') ?>"
                alt="Profile"
                class="avatar"
            >

            <span class="user-name">
                <?= htmlspecialchars($patient_name, ENT_QUOTES, 'UTF-8') ?>
            </span>

        </div>

        <a href="../logout.php" class="logout">Logout</a>

    </div>

</div>


<div class="container">

    <!-- HERO -->

    <div class="hero">

        <div class="hero-text">

            <span class="hero-badge">👋 <?= $greeting ?></span>

            <h2>
                Welcome,
                <?= htmlspecialchars($patient_name, ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <p>
                Manage your appointments and healthcare services
                from your fctCare account, all in one place.
            </p>

            <a href="../appointment.php" class="hero-btn">
                Book an Appointment
            </a>

        </div>

        <div class="hero-image">

            <img
                src="<?= htmlspecialchars($hero_image, ENT_QUOTES, 'UTF-8') ?>"
                alt="Patient care"
            >

        </div>

    </div>


            <!-- APPOINTMENT STATISTICS -->

        <h3 class="section-title">Your Appointments</h3>

        <div class="cards">

            <!-- Total Appointments -->

            <div class="card clickable-card"
                onclick="showAppointments('all')">

                <div class="card-icon">📅</div>

                <h3>Total Appointments</h3>

                <div class="number">
                    <?= $total_appointments ?>
                </div>

                <small>Click to view all appointments</small>

            </div>


            <!-- Pending Appointments -->

            <div class="card clickable-card"
                onclick="showAppointments('pending')">

                <div class="card-icon">⏳</div>

                <h3>Pending Appointments</h3>

                <div class="number">
                    <?= $pending ?>
                </div>

                <small>Click to view pending appointments</small>

            </div>


            <!-- Confirmed Appointments -->

            <div class="card clickable-card"
                onclick="showAppointments('confirmed')">

                <div class="card-icon">✅</div>

                <h3>Confirmed Appointments</h3>

                <div class="number">
                    <?= $confirmed ?>
                </div>

                <small>Click to view confirmed appointments</small>

            </div>

        </div>


    <!-- QUICK ACTIONS -->

    <h3 class="section-title">Quick Actions</h3>

    <div class="actions">

        <a href="../appointment.php" class="action">
            <span class="action-icon">🩺</span>
            <span>
                <strong>Book Appointment</strong>
                <small>Channel a doctor now</small>
            </span>
        </a>

        <a href="#" class="action" onclick="openProfileModal(); return false;">
            <span class="action-icon">👤</span>
            <span>
                <strong>My Profile</strong>
                <small>View and edit your details</small>
            </span>
        </a>


        <a href="appointments.php" class="action">
            <span class="action-icon">📋</span>
            <span>
                <strong>My Appointments</strong>
                <small>Track your bookings</small>
            </span>
        </a>

    </div>

</div>
<!-- APPOINTMENT DETAILS MODAL -->

<div id="appointmentModal"
     class="appointment-modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modalTitle">

    <div class="appointment-modal-content">

        <div class="appointment-modal-header">

            <h2 id="modalTitle">My Appointments</h2>

            <button type="button"
                    class="close-modal"
                    onclick="closeAppointments()"
                    aria-label="Close">
                &times;
            </button>

        </div>

        <div id="appointmentList" class="appointment-list"></div>

    </div>

</div>

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



<script>
const allAppointments = <?= json_encode(
    $all_appointments,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
) ?>;

const pendingAppointments = <?= json_encode(
    $pending_appointments,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
) ?>;

const confirmedAppointments = <?= json_encode(
    $confirmed_appointments,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
) ?>;


/* Safely display database values as text */

function escapeHTML(value) {
    return String(value ?? "").replace(/[&<>"']/g, function(char) {
        const entities = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        };

        return entities[char];
    });
}


/* Format appointment date */

function formatDate(value) {
    if (!value) {
        return "Not specified";
    }

    const date = new Date(value + "T00:00:00");

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    });
}


/* Display appointments in modal */

function showAppointments(type) {

    let appointments = [];
    let title = "";

    if (type === "pending") {

        appointments = pendingAppointments;
        title = "⏳ Pending Appointments";

    } else if (type === "confirmed") {

        appointments = confirmedAppointments;
        title = "✅ Confirmed Appointments";

    } else {

        appointments = allAppointments;
        title = "📅 All My Appointments";
    }

    document.getElementById("modalTitle").textContent =
        title + " (" + appointments.length + ")";

    const list = document.getElementById("appointmentList");

    if (appointments.length === 0) {

        list.innerHTML = `
            <div class="no-appointments">
                <h3>No Appointments Found</h3>
                <p>There are no appointments in this category.</p>
            </div>
        `;

    } else {

        list.innerHTML = appointments.map(function(appointment) {

            const status = String(
                appointment.status ?? "Unknown"
            );

            const normalizedStatus = status.trim().toLowerCase();

            let statusClass = "status-other";

            if (normalizedStatus === "pending") {
                statusClass = "status-pending";
            } else if (normalizedStatus === "confirmed") {
                statusClass = "status-confirmed";
            }

            return `
                <div class="appointment-detail-card">

                    <h3>
                        Appointment Id ${escapeHTML(appointment.id)}
                    </h3>

                    <p>
                        <strong>Patient:</strong>
                        ${escapeHTML(appointment.patient_name)}
                    </p>

                    <p>
                        <strong>Doctor:</strong>
                        ${escapeHTML(appointment.doctor_name || "Not available")}
                    </p>

                    <p>
                        <strong>Specialty:</strong>
                        ${escapeHTML(appointment.specialty || "Not available")}
                    </p>

                    <p>
                        <strong>Hospital:</strong>
                        ${escapeHTML(appointment.hospital_name || "Not available")}
                    </p>

                    <p>
                        <strong>Date:</strong>
                        ${escapeHTML(formatDate(appointment.appointment_date))}
                    </p>

                    <p>
                        <strong>Reason:</strong>
                        ${escapeHTML(appointment.reason || "Not specified")}
                    </p>

                    <p>
                        <strong>Doctor Fee:</strong>
                        Rs. ${escapeHTML(appointment.doctor_fee)}
                    </p>

                    <p>
                        <strong>Hospital Fee:</strong>
                        Rs. ${escapeHTML(appointment.hospital_fee)}
                    </p>

                    <p>
                        <strong>Total Payment:</strong>
                        Rs. ${escapeHTML(appointment.total_payment)}
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <span class="appointment-status ${statusClass}">
                            ${escapeHTML(status)}
                        </span>
                    </p>

                </div>
            `;

        }).join("");
    }

    document.getElementById("appointmentModal").style.display = "block";

    document.body.style.overflow = "hidden";
}


/* Close modal */

function closeAppointments() {

    document.getElementById("appointmentModal").style.display = "none";

    document.body.style.overflow = "";
}


/* Close when clicking outside */

document.getElementById("appointmentModal").addEventListener(
    "click",
    function(event) {

        if (event.target === this) {
            closeAppointments();
        }
    }
);


/* Close using Escape key */

document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {
        closeAppointments();
    }
});


/* My Appointments quick action */

document.addEventListener("DOMContentLoaded", function() {

    const appointmentLinks = document.querySelectorAll(
        '.actions a[href="appointments.php"]'
    );

    appointmentLinks.forEach(function(link) {

        link.addEventListener("click", function(event) {

            event.preventDefault();

            showAppointments("all");
        });
    });
});
</script>
<!-- MY PROFILE MODAL -->

<div id="profileModal" class="profile-modal">

    <div class="profile-modal-content">

        <button type="button"
                class="profile-close"
                onclick="closeProfileModal()">
            &times;
        </button>

        <h2>👤 My Profile</h2>

        <p class="profile-subtitle">
            Your registered information
        </p>

        <?php if (!empty($profile)): ?>

            <div class="profile-details">

                <div class="profile-row">
                    <span>Full Name</span>
                    <strong>
                        <?= htmlspecialchars($profile['full_name'] ?? 'Not Available') ?>
                    </strong>
                </div>

                <div class="profile-row">
                    <span>NIC</span>
                    <strong>
                        <?= htmlspecialchars($profile['nic'] ?? 'Not Available') ?>
                    </strong>
                </div>

                <div class="profile-row">
                    <span>Phone</span>
                    <strong>
                        <?= htmlspecialchars($profile['phone'] ?? 'Not Available') ?>
                    </strong>
                </div>

                <div class="profile-row">
                    <span>Email</span>
                    <strong>
                        <?= htmlspecialchars($profile['email'] ?? 'Not Available') ?>
                    </strong>
                </div>

                <div class="profile-row">
                    <span>Gender</span>
                    <strong>
                        <?= htmlspecialchars($profile['gender'] ?? 'Not Available') ?>
                    </strong>
                </div>

            </div>

        <?php else: ?>

            <p class="profile-error">
                Profile information could not be found.
            </p>

        <?php endif; ?>

        <button type="button"
                class="profile-done-btn"
                onclick="closeProfileModal()">
            Close
        </button>

    </div>

</div>
<script>
        function openProfileModal() {
            document.getElementById("profileModal").style.display = "flex";
        }

        function closeProfileModal() {
            document.getElementById("profileModal").style.display = "none";
        }

        document.getElementById("profileModal").addEventListener("click", function(event) {
            if (event.target === this) {
                closeProfileModal();
            }
        });

        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closeProfileModal();
            }
        });
</script>

</body>

</html>