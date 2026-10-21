
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


/*
|--------------------------------------------------------------------------
| GET APPOINTMENTS
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        a.id,
        a.patient_name,
        a.patient_nic,
        a.patient_phone,
        a.patient_email,

        d.name AS doctor_name,
        d.specialty,

        h.name AS hospital_name,
        h.district,

        s.day_of_week,
        s.start_time,
        s.end_time,
        s.status AS schedule_status,

        a.appointment_date,
        a.reason,

        a.doctor_fee,
        a.hospital_fee,
        a.total_payment,

        a.status,
        a.created_at

    FROM appointments a

    INNER JOIN doctors d
        ON a.doctor_id = d.id

    INNER JOIN hospitals h
        ON a.hospital_id = h.id

    INNER JOIN schedules s
        ON a.schedule_id = s.id

    ORDER BY a.created_at DESC
";


$result = $conn->query($sql);


/*
|--------------------------------------------------------------------------
| FORMAT TIME
|--------------------------------------------------------------------------
*/

function formatTime($time)
{
    if (!$time) {
        return "-";
    }

    return date(
        "h:i A",
        strtotime($time)
    );
}

$current = basename($_SERVER["PHP_SELF"]);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>fctCare - Appointments</title>
    <link rel="icon" type="image/jpeg" href="../logo.jpeg">

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>


<body style="
    background:
        linear-gradient(rgba(255,255,255,0.35), rgba(255,255,255,0.35)),
        url('../bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

<div class="admin-container">


    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">
        <div class="sidebar-brand">

          <img src="logo.jpeg" alt="fctCare Logo" class="logo"> 
            fct<span>Care</span>
        </div>

        

        <p class="admin-label">
            Admin Panel
        </p>


        <a href="dashboard.php"    class="<?= $current === 'dashboard.php'    ? 'active' : '' ?>">Dashboard</a>
    <a href="appointments.php" class="<?= $current === 'appointments.php' ? 'active' : '' ?>">Appointments</a>
    <a href="messages.php"     class="<?= $current === 'messages.php'     ? 'active' : '' ?>">Messages</a>
    <a href="doctors.php"      class="<?= $current === 'doctors.php'      ? 'active' : '' ?>">Doctors</a>
    <a href="hospitals.php"    class="<?= $current === 'hospitals.php'    ? 'active' : '' ?>">Hospitals</a>
    <a href="schedules.php"    class="<?= $current === 'schedules.php'    ? 'active' : '' ?>">Schedules</a>

    <a href="logout.php" class="logout">Logout</a>

    </aside>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-content">

    <div class="page-head">

        <div>
            <h1>Appointments</h1>
            <p>Manage patient appointment requests.</p>
        </div>

        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
            </svg>
            <input
                type="text"
                id="patientSearch"
                placeholder="Search by patient name..."
                aria-label="Search by patient name"
                autocomplete="off"
            >
        </div>

    </div>

        <!-- =========================
             APPOINTMENT TABLE
        ========================== -->

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>


                        <th>
                            Patient
                        </th>


                        <th>
                            Doctor
                        </th>


                        <th>
                            Hospital
                        </th>


                        <th>
                            Date
                        </th>


                        <th>
                            Time
                        </th>


                        <th>
                            Total
                        </th>


                        <th>
                            Status
                        </th>


                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if (
                    $result &&
                    $result->num_rows > 0
                ): ?>


                    <?php while (
                        $row =
                        $result->fetch_assoc()
                    ): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["id"]
                                ) ?>

                            </td>


                            <!-- PATIENT -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row["patient_name"]
                                    ) ?>

                                </strong>

                                <br>

                                <small>

                                    <?= htmlspecialchars(
                                        $row["patient_phone"]
                                    ) ?>

                                </small>

                            </td>


                            <!-- DOCTOR -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["doctor_name"]
                                ) ?>

                                <br>

                                <small>

                                    <?= htmlspecialchars(
                                        $row["specialty"]
                                    ) ?>

                                </small>

                            </td>


                            <!-- HOSPITAL -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["hospital_name"]
                                ) ?>

                                <br>

                                <small>

                                    <?= htmlspecialchars(
                                        $row["district"]
                                    ) ?>

                                </small>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["appointment_date"]
                                ) ?>

                            </td>


                            <!-- TIME -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["day_of_week"]
                                ) ?>

                                <br>

                                <small>

                                    <?= htmlspecialchars(
                                        formatTime(
                                            $row["start_time"]
                                        )
                                    ) ?>

                                    -

                                    <?= htmlspecialchars(
                                        formatTime(
                                            $row["end_time"]
                                        )
                                    ) ?>

                                </small>

                            </td>


                            <!-- TOTAL -->

                            <td>

                                Rs.

                                <?= number_format(
                                    $row["total_payment"],
                                    2
                                ) ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="status status-<?= strtolower(
                                        htmlspecialchars(
                                            $row["status"]
                                        )
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        $row["status"]
                                    ) ?>

                                </span>

                            </td>


                            <!-- ACTION -->

                            <td>


                                <?php if (
                                    $row["status"] === "Pending"
                                ): ?>


                                    <form
                                        action="update_appointment.php"
                                        method="POST"
                                        class="status-form"
                                    >


                                        <input
                                            type="hidden"
                                            name="appointment_id"
                                            value="<?= htmlspecialchars(
                                                $row["id"]
                                            ) ?>"
                                        >


                                        <button
                                            type="submit"
                                            name="status"
                                            value="Confirmed"
                                            class="btn-confirm"
                                        >
                                            Confirm
                                        </button>


                                        <button
                                            type="submit"
                                            name="status"
                                            value="Cancelled"
                                            class="btn-cancel"
                                        >
                                            Cancel
                                        </button>


                                    </form>


                                <?php elseif (
                                    $row["status"] === "Confirmed"
                                ): ?>


                                    <form
                                        action="update_appointment.php"
                                        method="POST"
                                        class="status-form"
                                    >


                                        <input
                                            type="hidden"
                                            name="appointment_id"
                                            value="<?= htmlspecialchars(
                                                $row["id"]
                                            ) ?>"
                                        >


                                        <button
                                            type="submit"
                                            name="status"
                                            value="Completed"
                                            class="btn-complete"
                                        >
                                            Complete
                                        </button>


                                        <button
                                            type="submit"
                                            name="status"
                                            value="Cancelled"
                                            class="btn-cancel"
                                        >
                                            Cancel
                                        </button>


                                    </form>


                                <?php else: ?>


                                    <span class="no-action">

                                        No Action

                                    </span>


                                <?php endif; ?>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="9"
                            style="text-align:center;"
                        >

                            No appointments found.

                        </td>

                    </tr>


                <?php endif; ?>

                  <tr id="noMatchRow" style="display:none;">
                    <td colspan="9" style="text-align:center; padding:30px;">
                        No matching patient found
                    </td>
                </tr>

                </tbody>

            </table>

        </div>

    </main>

</div>

<footer class="footer">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="index.php" class="footer-logo">
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
    (function () {
        var input = document.getElementById("patientSearch");
        var rows = document.querySelectorAll(".table-container tbody tr:not(#noMatchRow)");
        var noMatch = document.getElementById("noMatchRow");

        if (!input) return;

        input.addEventListener("input", function () {
            var q = input.value.trim().toLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var cell = row.children[1];                    // Patient column
                if (!cell || row.children.length < 2) return;

                var nameEl = cell.querySelector("strong");     // patient name only (not phone)
                var name = (nameEl ? nameEl.textContent : cell.textContent).toLowerCase();

                var match = name.indexOf(q) !== -1;
                row.style.display = match ? "" : "none";
                if (match) visible++;
            });

            if (noMatch) {
                noMatch.style.display = (visible === 0 && q !== "") ? "" : "none";
            }
        });
    })();
</script>

</body>

</html>


<?php

$conn->close();

?>

