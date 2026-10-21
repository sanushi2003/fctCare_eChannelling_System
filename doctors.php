<?php

require_once "backend/config/db.php";
require_once "backend/config/session.php";


$avatar_image = "img 1.jpg";

$dashboards = [
    "patient" => "patient/dashboard.php",
    "doctor"  => "doctor/dashboard.php",
    "admin"   => "admin/dashboard.php"
];

$user_type = strtolower((string) ($_SESSION["user_type"] ?? ""));

$profile_link = $dashboards[$user_type] ?? "login.php";





/*
|--------------------------------------------------------------------------
| DOCTOR NAME SEARCH TERM
|--------------------------------------------------------------------------
*/

$search_name = trim((string)($_GET["name"] ?? ""));

date_default_timezone_set("Asia/Colombo");

$today = $conn->real_escape_string(date("l"));      // Monday, Tuesday ...
$now   = $conn->real_escape_string(date("H:i:s"));

// false = Available the whole day if today has an Active slot
// true  = Available only while the current time is inside the slot
$checkTime = false;

$timeCondition = $checkTime
    ? " AND '$now' BETWEEN s.start_time AND s.end_time"
    : "";

$base_sql = "SELECT d.doctor_code, d.name, d.specialty, d.fee,

                CASE WHEN EXISTS (
                    SELECT 1 FROM schedules s
                    WHERE s.doctor_id = d.id
                      AND s.status = 'Active'
                      AND s.day_of_week = '$today'
                      $timeCondition
                ) THEN 'Available' ELSE 'Unavailable' END AS availability,

                (SELECT GROUP_CONCAT(
                            DISTINCT s2.day_of_week
                            ORDER BY FIELD(s2.day_of_week,
                                'Monday','Tuesday','Wednesday','Thursday',
                                'Friday','Saturday','Sunday')
                            SEPARATOR ', ')
                 FROM schedules s2
                 WHERE s2.doctor_id = d.id
                   AND s2.status = 'Active'
                   AND s2.day_of_week IS NOT NULL
                ) AS available_days

             FROM doctors d";

$order_sql = " ORDER BY d.specialty ASC, d.name ASC";

if ($search_name !== "") {

    // Escape LIKE wildcards typed by the user
    $like = "%" . addcslashes($search_name, "%_\\") . "%";

    $stmt = $conn->prepare($base_sql . " WHERE d.name LIKE ?" . $order_sql);

    if ($stmt) {
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = false;
    }

} else {

    $result = $conn->query($base_sql . $order_sql);

}

$doctorsBySpecialty = [];
$doctorCount = 0;

if ($result) {
    while ($doctor = $result->fetch_assoc()) {
        $specialty = trim($doctor["specialty"] ?? "");

        if ($specialty === "") {
            $specialty = "Other Specialties";
        }

        $doctorsBySpecialty[$specialty][] = $doctor;
        $doctorCount++;
    }
}

$specialtyOrder = [
    "Cardiologist",
    "Pediatrician",
    "Neurologist",
    "Dermatologist",
    "Dentist",
    "Endocrinologist",
    "Ophthalmologist",
    "Gynecology & Obstetrics",
    "Obstetrician",
    "Gynecologist",
    "Orthopedic Surgeon",
    "Oncologist",
    "Hematologist",
    "Pulmonologist",
    "ENT Specialist",
    "Psychiatrist",
    "Urologist"
];

uksort($doctorsBySpecialty, function ($a, $b) use ($specialtyOrder) {
    $posA = array_search($a, $specialtyOrder);
    $posB = array_search($b, $specialtyOrder);

    $posA = ($posA === false) ? PHP_INT_MAX : $posA;
    $posB = ($posB === false) ? PHP_INT_MAX : $posB;

    if ($posA === $posB) {
        return strcasecmp($a, $b);
    }

    return $posA <=> $posB;
});

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>fctCare - Find Doctors</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="logo.jpeg">

    <style>

        /* Doctor name search bar (top right, under navbar) */

        .search-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-bottom: 16px;
        }

        .doctor-search {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            width: 100%;
            max-width: 460px;
            margin-bottom: 6px;
        }

        .doctor-search input[type="text"] {
            flex: 1;
            min-width: 0;
            padding: 12px 16px;
            font-size: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            background: #ffffff;
            color: #1e293b;
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

        .doctor-search input[type="text"]:focus {
            border-color: #00a896;
            box-shadow: 0 0 0 3px rgba(0, 168, 150, 0.15);
        }

        .doctor-search .btn {
            padding: 12px 22px;
            cursor: pointer;
        }

        @media (max-width: 600px) {

            .search-wrap {
                align-items: stretch;
            }

            .doctor-search {
                max-width: 100%;
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

    <!-- Doctor name search (top right, under the navigation bar) -->

    <div class="search-wrap">

        <form class="doctor-search" method="get" action="doctors.php">

            <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($search_name, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Search by doctor name..."
                aria-label="Search doctors by name"
                autocomplete="off"
            >

            <button type="submit" class="btn">Search</button>

        </form>

    </div>

    <h2>Our Specialized Medical Practitioners</h2>

    <p style="margin-bottom: 32px;">
        Browse through registered healthcare professionals available
        for e-channeling appointments.
    </p>

    <?php if (!empty($doctorsBySpecialty)): ?>

        <?php foreach ($doctorsBySpecialty as $specialty => $doctors): ?>

            <h3 style="
                color: #0a2d63;
                margin-top: 32px;
                border-bottom: 2px solid #00a896;
                padding-bottom: 8px;
            ">
                <?= htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <div class="grids">

                <?php foreach ($doctors as $doctor): ?>

                    <?php
                    $availability = trim($doctor["availability"] ?? "Available");
                    $isAvailable = strcasecmp($availability, "Available") === 0;

                    $availableDays = trim((string)($doctor["available_days"] ?? ""));
                    ?>

                    <div class="card">

                        <h3>
                            <?= htmlspecialchars($doctor["name"], ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <p>
                            <strong>Speciality:</strong>
                            <?= htmlspecialchars($doctor["specialty"] ?? "", ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p style="margin-top: 10px;">
                            <strong>Availability:</strong>

                            <span style="
                                color: <?= $isAvailable ? '#16a34a' : '#dc2626' ?>;
                                font-weight: bold;
                            ">
                                <?php
                                if ($isAvailable) {
                                    echo htmlspecialchars($availability, ENT_QUOTES, 'UTF-8');
                                } elseif ($availableDays !== "") {
                                    echo "Unavailable (Available on "
                                        . htmlspecialchars($availableDays, ENT_QUOTES, 'UTF-8')
                                        . ")";
                                } else {
                                    echo "Unavailable";
                                }
                                ?>
                            </span>
                        </p>

                        <p>
                            <strong>Fee:</strong>

                            <span style="color: #00a896; font-weight: bold;">
                                Rs. <?= number_format((float) $doctor["fee"], 2) ?>
                            </span>
                        </p>

                        <?php if ($isAvailable): ?>

                            <a
                                href="appointment.php?doctor_code=<?= urlencode($doctor["doctor_code"]) ?>"
                                class="btn"
                                style="margin-top: 16px;"
                            >
                                Channel Now
                            </a>

                        <?php else: ?>

                            <button
                                type="button"
                                disabled
                                style="
                                    margin-top: 16px;
                                    padding: 10px 18px;
                                    border: none;
                                    border-radius: 6px;
                                    background: #9ca3af;
                                    color: white;
                                    cursor: not-allowed;
                                "
                            >
                                Currently Unavailable
                            </button>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p style="
            margin-top: 30px;
            padding: 20px;
            background: #f5f7fa;
            border-radius: 8px;
        ">
            <?php if ($search_name !== ""): ?>
                No doctors found matching
                "<?= htmlspecialchars($search_name, ENT_QUOTES, 'UTF-8') ?>".
                Please check the spelling or try another name.
            <?php else: ?>
                No doctors are currently available.
            <?php endif; ?>
        </p>

    <?php endif; ?>

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