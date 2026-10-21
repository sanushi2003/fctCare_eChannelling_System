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





/*
DISTRICT SEARCH TERM
*/

$search_district = trim((string)($_GET["district"] ?? ""));

/*
LOAD HOSPITALS FROM DATABASE (FILTERED BY DISTRICT IF SEARCHED)
*/

$hospitals = [];
$hospital_error = "";

$base_sql = "
    SELECT
        id,
        hospital_code,
        name,
        district,
        telephone,
        facilities,
        fee
    FROM hospitals
";

$order_sql = " ORDER BY district ASC, name ASC";

if ($search_district !== "") {

    // Escape LIKE wildcards typed by the user
    $like = "%" . addcslashes($search_district, "%_\\") . "%";

    $stmt = $conn->prepare($base_sql . " WHERE district LIKE ? " . $order_sql);

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

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }

    $result->free();

} else {

    $hospital_error = "Unable to load hospitals from the database.";

}



//HTML ESCAPE FUNCTION

function e($value)
{
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>fctCare - Partnered Hospitals</title>

    <link rel="stylesheet" href="style.css">

    <link rel="icon" type="image/jpeg" href="logo.jpeg">

    <style>

        /* District search bar */

        .search-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-bottom: 16px;
        }

        .hospital-search {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            width: 100%;
            max-width: 460px;
            margin-bottom: 6px;
        }

        .hospital-search input[type="text"] {
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

        .hospital-search input[type="text"]:focus {
            border-color: #00a896;
            box-shadow: 0 0 0 3px rgba(0, 168, 150, 0.15);
        }

        .hospital-search .btn {
            padding: 12px 22px;
            cursor: pointer;
        }

        @media (max-width: 600px) {

            .search-wrap {
                align-items: stretch;
            }

            .hospital-search {
                max-width: 100%;
            }

        }

        .hospital_table {
            width: 100%;
            overflow-x: auto;
            margin: 24px 0 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05),
                        0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border-radius: 8px;
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

        .hospital_data {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            text-align: left;
            font-size: 15px;
        }

        .hospital_data th {
            background-color: #0a2d63;
            color: #ffffff;
            padding: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .hospital_data td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: top;
        }

        .hospital_data tr:hover {
            background-color: #f8fafc;
        }

        .district {
            font-weight: 700;
            color: #00a896;
        }

        .hospital_data b {
            color: #1e293b;
        }

        .fee-text {
            color: #00a896;
            font-weight: 600;
        }

        /* Schedule modal */

        .schedule-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            padding: 20px;
            overflow-y: auto;
        }

        .schedule-modal-content {
            background: #ffffff;
            width: 100%;
            max-width: 750px;
            margin: 50px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
        }

        .schedule-modal-header {
            background: #0a2d63;
            color: #ffffff;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .schedule-modal-header h3 {
            margin: 0;
            color: #ffffff;
        }

        .schedule-close {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 30px;
            cursor: pointer;
            line-height: 1;
        }

        .schedule-modal-body {
            padding: 24px;
        }

        .schedule-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
        }

        .schedule-table th {
            background: #f1f5f9;
            color: #0a2d63;
            text-align: left;
            padding: 12px;
        }

        .schedule-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        .schedule-message {
            padding: 15px;
            background: #f1f5f9;
            border-radius: 8px;
            color: #334155;
            line-height: 1.6;
        }

        .schedule-loading {
            text-align: center;
            padding: 25px;
        }

        @media (max-width: 600px) {

            .schedule-modal {
                padding: 10px;
            }

            .schedule-modal-content {
                margin: 20px auto;
            }

            .schedule-modal-body {
                padding: 15px;
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


    <!-- District search (top right, under the navigation bar) -->

    <div class="search-wrap">

        <form class="hospital-search" method="get" action="hospitals.php">

            <input
                type="text"
                name="district"
                value="<?= e($search_district) ?>"
                placeholder="Search by district..."
                aria-label="Search hospitals by district"
                autocomplete="off"
            >

            <button type="submit" class="btn btn_2">Search</button>

        </form>

    </div>

    <h2>Partnered Medical Centers & Hospitals</h2>

    <p style="margin-bottom: 24px;">

        Select your preferred healthcare facility
        to view available channel consultation schedules.

    </p>


    <div class="hospital_table">

        <table class="hospital_data">

            <thead>

                <tr>

                    <th style="width: 12%;">District</th>

                    <th style="width: 23%;">Hospital Name</th>

                    <th style="width: 13%;">Telephone</th>

                    <th style="width: 25%;">Facilities</th>

                    <th style="width: 12%;">Hospital Fee</th>

                    <th style="width: 15%;"></th>

                </tr>

            </thead>


            <tbody>

                <?php if ($hospital_error !== ""): ?>

                    <tr>
                        <td colspan="6">
                            <?= e($hospital_error) ?>
                        </td>
                    </tr>

                <?php elseif (empty($hospitals)): ?>

                    <tr>
                        <td colspan="6">
                            <?php if ($search_district !== ""): ?>
                                No hospitals found in the district
                                "<?= e($search_district) ?>".
                                Please check the spelling or try another district.
                            <?php else: ?>
                                No hospitals are currently available.
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php else: ?>

                    <?php

                    /*
                     Group hospitals by district.
                     */

                    $district_groups = [];

                    foreach ($hospitals as $hospital) {

                        $district_name = trim(
                            $hospital["district"] ?? ""
                        );

                        if ($district_name === "") {
                            $district_name = "Other";
                        }

                        $district_groups[$district_name][] = $hospital;

                    }

                    foreach (
                        $district_groups as $district_name => $district_hospitals
                    ):

                        $rowspan = count($district_hospitals);

                        $first_row = true;

                        foreach ($district_hospitals as $hospital):

                            $top_border = $first_row
                                ? 'style="border-top: 2px solid #cbd5e1;"'
                                : '';

                            $hospital_id = (int)$hospital["id"];

                            $hospital_name = $hospital["name"] ?? "";

                    ?>

                        <tr>

                            <?php if ($first_row): ?>

                                <td
                                    class="district"
                                    rowspan="<?= (int)$rowspan ?>"
                                    <?= $top_border ?>
                                >
                                    <?= e($district_name) ?>
                                </td>

                            <?php endif; ?>


                            <td <?= $top_border ?>>

                                <b>
                                    <?= e($hospital_name) ?>
                                </b>

                            </td>


                            <td <?= $top_border ?>>

                                <?= e($hospital["telephone"] ?? "") ?>

                            </td>


                            <td <?= $top_border ?>>

                                <?= e($hospital["facilities"] ?? "") ?>

                            </td>


                            <td class="fee-text" <?= $top_border ?>>

                                Rs.
                                <?= number_format(
                                    (float)($hospital["fee"] ?? 0),
                                    2
                                ) ?>

                            </td>


                            <td <?= $top_border ?>>

                                <button
                                    type="button"
                                    class="btn btn_2"
                                    onclick="showSchedule(
                                        <?= $hospital_id ?>,
                                        <?= htmlspecialchars(
                                            json_encode(
                                                $hospital_name,
                                                JSON_HEX_TAG |
                                                JSON_HEX_APOS |
                                                JSON_HEX_QUOT |
                                                JSON_HEX_AMP
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    )"
                                >
                                    View Schedules
                                </button>

                            </td>

                        </tr>

                    <?php

                            $first_row = false;

                        endforeach;

                    endforeach;

                    ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>


<!-- Schedule Modal -->

<div
    id="scheduleModal"
    class="schedule-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="scheduleModalTitle"
>

    <div class="schedule-modal-content">

        <div class="schedule-modal-header">

            <h3 id="scheduleModalTitle">
                Hospital Schedules
            </h3>

            <button
                type="button"
                class="schedule-close"
                onclick="closeScheduleModal()"
                aria-label="Close"
            >
                &times;
            </button>

        </div>


        <div
            class="schedule-modal-body"
            id="scheduleModalBody"
        >

            <div class="schedule-loading">
                Loading schedules...
            </div>

        </div>

    </div>

</div>


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




/*
-----------------------------
SHOW DATABASE SCHEDULES
---------------------------------------
 Hospital ID is used to identify the selected hospital.
*/

async function showSchedule(hospitalId, hospitalName) {

    const modal = document.getElementById("scheduleModal");

    const modalTitle = document.getElementById("scheduleModalTitle");

    const modalBody = document.getElementById("scheduleModalBody");

    modalTitle.textContent = hospitalName + " - Schedules";

    modalBody.innerHTML =
        '<div class="schedule-loading">Loading schedules...</div>';

    modal.style.display = "block";

    document.body.style.overflow = "hidden";

    try {

        const response = await fetch("backend/schedules.php");

        if (!response.ok) {
            throw new Error("HTTP Error: " + response.status);
        }

        const result = await response.json();

        if (!result.success) {

            modalBody.innerHTML =
                '<div class="schedule-message">' +
                'Unable to load schedules.' +
                '</div>';

            return;

        }

        if (!Array.isArray(result.data)) {

            modalBody.innerHTML =
                '<div class="schedule-message">' +
                'Invalid schedule data received.' +
                '</div>';

            return;

        }


        /*
         * Filter using hospital_id.
         * This is more reliable than matching hospital names.
         */

        const hospitalSchedules = result.data.filter(schedule => {

            return Number(schedule.hospital_id) === Number(hospitalId);

        });


        if (hospitalSchedules.length === 0) {

            modalBody.innerHTML =
                '<div class="schedule-message">' +
                'No active schedules are currently available for ' +
                escapeHTML(hospitalName) +
                '.</div>';

            return;

        }


        /*
         * Sort by day and start time.
         */

        hospitalSchedules.sort((a, b) => {

            const dayComparison = String(a.day_of_week || "")
                .localeCompare(String(b.day_of_week || ""));

            if (dayComparison !== 0) {
                return dayComparison;
            }

            return String(a.start_time || "")
                .localeCompare(String(b.start_time || ""));

        });


        /*
         * Build schedule table.
         */

        let html = `
            <div class="schedule-table-wrapper">

                <table class="schedule-table">

                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>

                    <tbody>
        `;


        hospitalSchedules.forEach(schedule => {

            const doctorName = schedule.doctor_name || "Not assigned";

            const day = schedule.day_of_week || "Not specified";

            const startTime = formatTime(schedule.start_time);

            const endTime = formatTime(schedule.end_time);


            html += `
                <tr>
                    <td>${escapeHTML(doctorName)}</td>
                    <td>${escapeHTML(day)}</td>
                    <td>${escapeHTML(startTime)}</td>
                    <td>${escapeHTML(endTime)}</td>
                </tr>
            `;

        });


        html += `
                    </tbody>

                </table>

            </div>

            <p style="margin-top: 18px; color: #64748b;">
                To book an appointment, select Appointment from the menu.
            </p>
        `;


        modalBody.innerHTML = html;

    }

    catch (error) {

        console.error("Schedule loading error:", error);

        modalBody.innerHTML =
            '<div class="schedule-message">' +
            'Unable to connect to the schedule server. ' +
            'Please check your database connection and try again.' +
            '</div>';

    }

}


/*
--------------------------------------------------------------------------
 CLOSE SCHEDULE MODAL
--------------------------------------------------------------------------
*/

function closeScheduleModal() {

    document.getElementById("scheduleModal").style.display = "none";

    document.body.style.overflow = "";

}


/*
--------------------------------------------------------------------------
 FORMAT TIME
--------------------------------------------------------------------------
*/

function formatTime(time) {

    if (!time) {
        return "-";
    }

    const parts = String(time).split(":");

    if (parts.length < 2) {
        return time;
    }

    let hour = parseInt(parts[0], 10);

    const minute = parts[1];

    const period = hour >= 12 ? "PM" : "AM";

    if (hour === 0) {

        hour = 12;

    } else if (hour > 12) {

        hour -= 12;

    }

    return `${hour}:${minute} ${period}`;

}


/*
--------------------------------------------------------------------------
 ESCAPE HTML
--------------------------------------------------------------------------
 Prevent database values from being interpreted as HTML.
*/

function escapeHTML(value) {

    return String(value ?? "").replace(/[&<>"']/g, character => {

        const entities = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        };

        return entities[character];

    });

}


/*
--------------------------------------------------------------------------
 CLOSE MODAL BY CLICKING OUTSIDE
--------------------------------------------------------------------------
*/

document.getElementById("scheduleModal").addEventListener(
    "click",
    function(event) {

        if (event.target === this) {
            closeScheduleModal();
        }

    }
);


/*
--------------------------------------------------------------------------
 CLOSE MODAL WITH ESCAPE KEY
--------------------------------------------------------------------------
*/

document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {
        closeScheduleModal();
    }

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