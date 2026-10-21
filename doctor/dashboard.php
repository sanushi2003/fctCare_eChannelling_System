<?php

require_once "../backend/config/session.php";
require_once "../backend/config/db.php";

requireDoctor();

$doctor_id = $_SESSION["doctor_id"];

/*
|--------------------------------------------------------------------------
| IMAGE PATHS  (change these if your images are in another folder)
|--------------------------------------------------------------------------
*/

$logo_image   = "../logo.jpeg";     // logo beside "fctCare Doctor"
$hero_image   = "doctor.jpg";    // big banner image
$avatar_image = "img 1.jpg";     // small profile picture next to the name

/*
|--------------------------------------------------------------------------
| LOAD DOCTOR APPOINTMENTS
|--------------------------------------------------------------------------
*/

$all_appointments = [];
$today_appointments = [];
$pending_appointments = [];

$total = 0;
$today = 0;
$pending = 0;

date_default_timezone_set("Asia/Colombo");

$today_date = date("Y-m-d");

$stmt = $conn->prepare("
    SELECT
        a.id,
        a.patient_name,
        a.patient_phone,
        a.patient_email,
        a.appointment_date,
        a.reason,
        a.status,
        a.doctor_fee,
        a.hospital_fee,
        a.total_payment,
        h.name AS hospital_name
    FROM appointments a
    LEFT JOIN hospitals h
        ON a.hospital_id = h.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC, a.id DESC
");

$stmt->bind_param("i", $doctor_id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $all_appointments[] = $row;

    $appointment_date = $row["appointment_date"] ?? "";
    $status = strtolower(trim($row["status"] ?? ""));

    // Today's appointments
    if (
        $appointment_date !== "" &&
        date("Y-m-d", strtotime($appointment_date)) === $today_date &&
        $status !== "cancelled"
    ) {
        $today_appointments[] = $row;
    }

    // Pending appointments
    if ($status === "pending") {
        $pending_appointments[] = $row;
    }
}

$stmt->close();

$total = count($all_appointments);
$today = count($today_appointments);
$pending = count($pending_appointments);


/*
|--------------------------------------------------------------------------
| LOAD DOCTOR PROFILE
|--------------------------------------------------------------------------
*/

$doctor_profile = [];

$stmt = $conn->prepare("
    SELECT *
    FROM doctors
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $doctor_id);
$stmt->execute();

$profile_result = $stmt->get_result();

if ($profile_result->num_rows > 0) {
    $doctor_profile = $profile_result->fetch_assoc();
}

$stmt->close();

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

$doctor_name = $_SESSION["doctor_name"];
$today_label = date("l, d F Y");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>fctCare - Doctor Dashboard</title>
<link rel="icon" type="image/jpeg" href="../logo.jpeg">

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #eef4f7;
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
    margin: 0 0 22px;
    line-height: 1.65;
    opacity: 0.93;
}

.hero-date {
    display: inline-block;
    background: #ffffff;
    color: #000000;
    font-weight: 600;
    padding: 11px 22px;
    border-radius: 10px;
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
    box-shadow: 0 14px 30px rgba(11, 115, 133, 0.31);
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

    .cards {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 520px) {

    .navbar {
        padding: 12px 16px;
    }

    .logo {
        font-size: 20px;
    }

    .user-name {
        display: none;
    }

    .user-box {
        padding: 4px;
    }

}

/* CLICKABLE APPOINTMENT CARDS */

.clickable-card {
    cursor: pointer;
    transition: transform 0.25s, box-shadow 0.25s;
}

.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(11, 114, 133, 0.18);
}

.clickable-card:focus-visible {
    outline: 3px solid #0b7285;
    outline-offset: 4px;
}

.card-hint {
    display: block;
    margin-top: 10px;
    color: #64748b;
    font-size: 12px;
}


/* DOCTOR PROFILE SECTION */

.profile-section-title {
    margin-top: 35px;
}

.doctor-profile-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 6px 22px rgba(0, 0, 0, 0.06);
    margin-bottom: 30px;
}

.doctor-profile-icon {
    width: 65px;
    height: 65px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    background: #e3f4f7;
    border-radius: 50%;
}

.doctor-profile-info {
    flex: 1;
    min-width: 0;
}

.doctor-profile-info h3 {
    margin: 0 0 6px;
    color: #0b7285;
}

.doctor-profile-info p {
    margin: 0 0 6px;
    font-weight: 600;
}

.doctor-profile-info span {
    color: #64748b;
    font-size: 13px;
}

.profile-view-btn {
    border: none;
    border-radius: 9px;
    padding: 12px 18px;
    background: #0b7a8e;
    color: white;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.profile-view-btn:hover {
    background: #0b2854;
}


/* MODALS */

.dashboard-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    padding: 20px;
    background: rgba(15, 23, 42, 0.65);
    align-items: center;
    justify-content: center;
}

.dashboard-modal-content {
    background: #ffffff;
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 25px;
    border-radius: 18px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
}

.profile-modal-content {
    max-width: 600px;
}

.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.modal-header h2 {
    margin: 0 0 7px;
    color: #0b7285;
    font-size: 23px;
}

.modal-header p {
    margin: 0;
    color: #64748b;
    font-size: 13px;
}

.modal-close {
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 32px;
    cursor: pointer;
    line-height: 1;
}

.modal-close:hover {
    color: #c92a2a;
}


/* APPOINTMENT ITEMS */

.appointment-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.appointment-item {
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
    background: #f8fafc;
}

.appointment-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
}

.appointment-item-header h3 {
    margin: 0;
    color: #0b7285;
    font-size: 17px;
}

.appointment-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    background: #e2e8f0;
    color: #334155;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-confirmed {
    background: #dcfce7;
    color: #166534;
}

.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.appointment-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.appointment-detail-grid > div {
    min-width: 0;
}

.appointment-detail-grid span,
.profile-detail-row span {
    display: block;
    color: #64748b;
    font-size: 12px;
    margin-bottom: 5px;
}

.appointment-detail-grid strong,
.profile-detail-row strong {
    display: block;
    color: #1e293b;
    font-size: 14px;
    overflow-wrap: anywhere;
}

.empty-appointments {
    padding: 45px 15px;
    text-align: center;
    color: #64748b;
}

.empty-appointments > div {
    font-size: 42px;
}

.empty-appointments h3 {
    color: #0b7285;
    margin-bottom: 8px;
}


/* DOCTOR PROFILE DETAILS */

.doctor-profile-details {
    display: flex;
    flex-direction: column;
}

.profile-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    padding: 16px 0;
    border-bottom: 1px solid #e2e8f0;
}

.profile-detail-row strong {
    text-align: right;
}

.profile-close-btn {
    width: 100%;
    margin-top: 25px;
}


/* RESPONSIVE MODAL */

@media (max-width: 600px) {

    .dashboard-modal {
        padding: 10px;
    }

    .dashboard-modal-content {
        padding: 18px;
        max-height: 94vh;
    }

    .appointment-detail-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .doctor-profile-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .profile-view-btn {
        width: 100%;
    }

    .profile-detail-row {
        gap: 12px;
    }

    .modal-header h2 {
        font-size: 20px;
    }

}
/* MY PROFILE CARD */

.profile-section-title {
    margin-top: 35px;
}

.clickable-card {
    cursor: pointer;
    transition: transform 0.25s, box-shadow 0.25s;
}

.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(11, 114, 133, 0.18);
}

.clickable-card:focus-visible {
    outline: 3px solid #0b7285;
    outline-offset: 4px;
}

.card-hint {
    display: block;
    margin-top: 10px;
    color: #64748b;
    font-size: 12px;
}

/* MY PROFILE SECTION */
.my-profile-section {
    margin-top: 35px;
    width: 100%;
}

/* MY PROFILE CARD - LEFT SIDE */
.my-profile-card-container {
    display: flex;
    justify-content: flex-start;
    width: 100%;
}

.my-profile-card-container .card {
    width: 100%;
    max-width: 320px;
}

/* MOBILE RESPONSIVE */
@media (max-width: 800px) {
    .my-profile-card-container {
        justify-content: stretch;
    }

    .my-profile-card-container .card {
        max-width: 100%;
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

    <a href="dashboard.php" class="logo">
        <img
            src="<?= htmlspecialchars($logo_image, ENT_QUOTES, 'UTF-8') ?>"
            alt="fctCare Logo"
            class="logo-img"
        >
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
                <?= htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') ?>
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
                <?= htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <p>
                Here is an overview of your patients and appointments
                on your fctCare doctor account.
            </p>

            <span class="hero-date">📅 <?= $today_label ?></span>

        </div>

        <div class="hero-image">

            <img
                src="<?= htmlspecialchars($hero_image, ENT_QUOTES, 'UTF-8') ?>"
                alt="Doctor"
            >

        </div>

    </div>


    <!-- STATS -->

   <!-- APPOINTMENT OVERVIEW -->
<h3 class="section-title">Appointment Overview</h3>

<div class="cards">

    <!-- TOTAL APPOINTMENTS -->
    <div class="card clickable-card"
         onclick="showAppointments('all')"
         role="button"
         tabindex="0">

        <div class="card-icon">📋</div>
        <h3>Total Appointments</h3>
        <div class="number"><?= $total ?></div>
        <small class="card-hint">Click to view all appointments</small>

    </div>

    <!-- TODAY APPOINTMENTS -->
    <div class="card clickable-card"
         onclick="showAppointments('today')"
         role="button"
         tabindex="0">

        <div class="card-icon">🗓️</div>
        <h3>Today's Appointments</h3>
        <div class="number"><?= $today ?></div>
        <small class="card-hint">Click to view today's appointments</small>

    </div>

    <!-- PENDING APPOINTMENTS -->
    <div class="card clickable-card"
         onclick="showAppointments('pending')"
         role="button"
         tabindex="0">

        <div class="card-icon">⏳</div>
        <h3>Pending Appointments</h3>
        <div class="number"><?= $pending ?></div>
        <small class="card-hint">Click to view pending appointments</small>

    </div>

</div>

<!-- MY PROFILE SECTION -->
<div class="my-profile-section">

    <h3 class="section-title">My Profile</h3>

    <div class="my-profile-card-container">

        <div class="card clickable-card"
             onclick="openDoctorProfile()"
             role="button"
             tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openDoctorProfile();}">

            <div class="card-icon">👨‍⚕️</div>

            <h3>My Profile</h3>

            <div class="number">👤</div>

            <small class="card-hint">
                Click to view your profile details
            </small>

        </div>

    </div>

</div>
<!-- APPOINTMENT MODAL -->

<div id="appointmentModal"
     class="dashboard-modal"
     onclick="if(event.target === this) closeAppointmentModal()">

    <div class="dashboard-modal-content">

        <div class="modal-header">

            <div>
                <h2 id="appointmentModalTitle">Appointments</h2>
                <p id="appointmentModalSubtitle">
                    Appointment details
                </p>
            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeAppointmentModal()">
                &times;
            </button>

        </div>

        <div id="appointmentList" class="appointment-list"></div>

    </div>

</div>


<!-- DOCTOR PROFILE MODAL -->

<div id="doctorProfileModal"
     class="dashboard-modal"
     onclick="if(event.target === this) closeDoctorProfile()">

    <div class="dashboard-modal-content profile-modal-content">

        <div class="modal-header">

            <div>
                <h2>👨‍⚕️ My Profile</h2>
                <p>Your registered doctor information</p>
            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeDoctorProfile()">
                &times;
            </button>

        </div>

        <div class="doctor-profile-details">

            <div class="profile-detail-row">
                <span>Doctor Code</span>
                <strong><?= htmlspecialchars($doctor_profile["doctor_code"] ?? "Not Available", ENT_QUOTES, "UTF-8") ?></strong>
            </div>

            <div class="profile-detail-row">
                <span>Full Name</span>
                <strong><?= htmlspecialchars($doctor_profile["name"] ?? "Not Available", ENT_QUOTES, "UTF-8") ?></strong>
            </div>

            <div class="profile-detail-row">
                <span>Specialty</span>
                <strong><?= htmlspecialchars($doctor_profile["specialty"] ?? "Not Available", ENT_QUOTES, "UTF-8") ?></strong>
            </div>

            <div class="profile-detail-row">
                <span>Consultation Fee</span>
                <strong>
                    Rs. <?= number_format((float)($doctor_profile["fee"] ?? 0), 2) ?>
                </strong>
            </div>

            <?php if (isset($doctor_profile["availability"])): ?>
                <div class="profile-detail-row">
                    <span>Availability</span>
                    <strong>
                        <?= htmlspecialchars(
                            (string)$doctor_profile["availability"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </strong>
                </div>
            <?php endif; ?>

        </div>

        <button
            type="button"
            class="profile-view-btn profile-close-btn"
            onclick="closeDoctorProfile()">

            Close

        </button>

    </div>

</div>

</div> <!-- /container -->

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
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

const todayAppointments = <?= json_encode(
    $today_appointments,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

const pendingAppointments = <?= json_encode(
    $pending_appointments,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;


function escapeHTML(value) {
    const element = document.createElement("div");

    element.textContent =
        value === null || value === undefined || value === ""
            ? "Not Available"
            : String(value);

    return element.innerHTML;
}


function showAppointments(type) {

    let appointments = [];
    let title = "";

    if (type === "all") {

        appointments = allAppointments;
        title = "All Appointments";

    } else if (type === "today") {

        appointments = todayAppointments;
        title = "Today's Appointments";

    } else if (type === "pending") {

        appointments = pendingAppointments;
        title = "Pending Appointments";

    }

    document.getElementById("appointmentModalTitle").textContent = title;

    document.getElementById("appointmentModalSubtitle").textContent =
        appointments.length + " appointment(s) found";

    const list = document.getElementById("appointmentList");

    if (appointments.length === 0) {

        list.innerHTML = `
            <div class="empty-appointments">
                <div>📭</div>
                <h3>No Appointments Found</h3>
                <p>There are no appointments in this category.</p>
            </div>
        `;

    } else {

        list.innerHTML = appointments.map(function(a) {

            let formattedDate = "Not Available";

            if (a.appointment_date) {

                const parsedDate = new Date(
                    String(a.appointment_date).replace(" ", "T")
                );

                if (!isNaN(parsedDate.getTime())) {
                    formattedDate = parsedDate.toLocaleString();
                } else {
                    formattedDate = a.appointment_date;
                }
            }

            const status = a.status || "Unknown";

            const statusClass = status
                .toLowerCase()
                .replace(/[^a-z0-9_-]/g, "");

            return `
                <div class="appointment-item">

                    <div class="appointment-item-header">

                        <h3>Appointment Id ${escapeHTML(a.id)}</h3>

                        <span class="appointment-status status-${statusClass}">
                            ${escapeHTML(status)}
                        </span>

                    </div>

                    <div class="appointment-detail-grid">

                        <div>
                            <span>Patient Name</span>
                            <strong>${escapeHTML(a.patient_name)}</strong>
                        </div>

                        <div>
                            <span>Phone</span>
                            <strong>${escapeHTML(a.patient_phone)}</strong>
                        </div>

                        <div>
                            <span>Email</span>
                            <strong>${escapeHTML(a.patient_email)}</strong>
                        </div>

                        <div>
                            <span>Hospital</span>
                            <strong>${escapeHTML(a.hospital_name)}</strong>
                        </div>

                        <div>
                            <span>Date & Time</span>
                            <strong>${escapeHTML(formattedDate)}</strong>
                        </div>

                        <div>
                            <span>Reason</span>
                            <strong>${escapeHTML(a.reason)}</strong>
                        </div>

                        <div>
                            <span>Doctor Fee</span>
                            <strong>Rs. ${Number(a.doctor_fee || 0).toFixed(2)}</strong>
                        </div>

                        <div>
                            <span>Hospital Fee</span>
                            <strong>Rs. ${Number(a.hospital_fee || 0).toFixed(2)}</strong>
                        </div>

                        <div>
                            <span>Total Payment</span>
                            <strong>Rs. ${Number(a.total_payment || 0).toFixed(2)}</strong>
                        </div>

                    </div>

                </div>
            `;

        }).join("");

    }

    document.getElementById("appointmentModal").style.display = "flex";

    document.body.style.overflow = "hidden";
}


function closeAppointmentModal() {

    document.getElementById("appointmentModal").style.display = "none";

    document.body.style.overflow = "";
}


function openDoctorProfile() {

    document.getElementById("doctorProfileModal").style.display = "flex";

    document.body.style.overflow = "hidden";
}


function closeDoctorProfile() {

    document.getElementById("doctorProfileModal").style.display = "none";

    document.body.style.overflow = "";
}


document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {

        closeAppointmentModal();
        closeDoctorProfile();

    }

});
</script>

</body>

</html>