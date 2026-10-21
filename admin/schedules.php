<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


// ================= GET SCHEDULES =================

$result = $conn->query(
    "SELECT
        s.id,
        s.doctor_id,
        s.hospital_id,
        s.day_of_week,
        s.start_time,
        s.end_time,
        s.status,
        d.name AS doctor_name,
        h.name AS hospital_name
     FROM schedules s
     INNER JOIN doctors d
        ON s.doctor_id = d.id
     INNER JOIN hospitals h
        ON s.hospital_id = h.id
     ORDER BY s.id DESC"
);

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

    <title>Schedules - Admin</title>
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


    <!-- ================= SIDEBAR ================= -->

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



    <!-- ================= MAIN CONTENT ================= -->

    <main class="main-content">


        <div class="top-bar">

            <div>

                <h1>
                    Schedules
                </h1>

                <p>
                    Manage doctor appointment schedules
                </p>

            </div>


            <div>

                Welcome,

                <strong>
                    <?= htmlspecialchars(
                        $_SESSION["admin_username"] ?? "Admin"
                    ) ?>
                </strong>

            </div>

        </div>



        <!-- ================= SCHEDULE SECTION ================= -->

        <section class="dashboard-section">


            <div class="page-title-row">

                <div>

                    <h2>
                        Schedule Management
                    </h2>

                    <p>
                        Manage doctor time slots
                    </p>

                </div>


                <div class="title-tools">

                    <div class="table-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
                        </svg>
                        <input
                            type="text"
                            id="doctorSearch"
                            placeholder="Search by doctor name..."
                            aria-label="Search by doctor name"
                            autocomplete="off"
                        >
                    </div>

                    <a href="add_schedule.php" class="btn btn-confirm">
                        + Add Schedule
                    </a>

                </div>

            </div>



            <!-- ================= TABLE ================= -->

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Doctor
                            </th>

                            <th>
                                Hospital
                            </th>

                            <th>
                                Day
                            </th>

                            <th>
                                Start Time
                            </th>

                            <th>
                                End Time
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $result &&
                        $result->num_rows > 0
                    ): ?>


                        <?php while (
                            $row = $result->fetch_assoc()
                        ): ?>


                            <tr>


                                <td>
                                    <?= htmlspecialchars(
                                        $row["id"]
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row["doctor_name"]
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row["hospital_name"]
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row["day_of_week"]
                                    ) ?>
                                </td>


                                <td>

                                    <?= date(
                                        "h:i A",
                                        strtotime(
                                            $row["start_time"]
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= date(
                                        "h:i A",
                                        strtotime(
                                            $row["end_time"]
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $row["status"] === "Active"
                                    ): ?>

                                        <span
                                            style="
                                                background:#d4edda;
                                                color:#155724;
                                                padding:5px 10px;
                                                border-radius:15px;
                                                font-size:12px;
                                                font-weight:600;
                                            "
                                        >
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span
                                            style="
                                                background:#f8d7da;
                                                color:#721c24;
                                                padding:5px 10px;
                                                border-radius:15px;
                                                font-size:12px;
                                                font-weight:600;
                                            "
                                        >
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <a
                                        href="edit_schedule.php?id=<?= $row["id"] ?>"
                                        class="btn"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="delete_schedule.php?id=<?= $row["id"] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this schedule?');"
                                    >
                                        Delete
                                    </a>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="8"
                                style="
                                    text-align:center;
                                    padding:30px;
                                "
                            >
                                No Schedules Found
                            </td>

                        </tr>


                    <?php endif; ?>
                        <tr id="noMatchRow" style="display:none;">
                            <td colspan="8" style="text-align:center; padding:30px;">
                                No matching doctor found
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


        </section>


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
        var input = document.getElementById("doctorSearch");
        var rows = document.querySelectorAll(".table-container tbody tr:not(#noMatchRow)");
        var noMatch = document.getElementById("noMatchRow");

        if (!input) return;

        input.addEventListener("input", function () {
            var q = input.value.trim().toLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var cell = row.children[1];            // Doctor column
                if (!cell || row.children.length < 2) return;

                var match = cell.textContent.toLowerCase().indexOf(q) !== -1;
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