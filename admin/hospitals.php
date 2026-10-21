<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$result = $conn->query("SELECT * FROM hospitals ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Management - fctCare Admin</title>
    <link rel="icon" type="image/jpeg" href="../logo.jpeg">

    <link rel="stylesheet" href="admin.css">

</head>

<body style="
    background:
        linear-gradient(rgba(255,255,255,0.35), rgba(255,255,255,0.35)),
        url('../bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

<div class="admin-container">


    <!-- ================= HEADER ================= -->

    <div class="admin-header">

        <div>

            <div class="brand">
                <img src="logo.jpeg" alt="fctCare Logo" class="logo">
                fct<span>Care</span>
            </div>


            <h1>
                Hospital Management
            </h1>

        </div>


        <div>

            <a href="dashboard.php" class="btnb">
                ← Dashboard
            </a>

            <a
                href="logout.php"
                class="btnb btnb-danger"
                style="margin-top: 20px;"
            >
                Logout
            </a>

        </div>

    </div>



    <!-- ================= PAGE CONTENT ================= -->

    <div class="page-content">


        <!-- ================= TITLE ROW ================= -->

        <div class="page-title-row">

            <div>

                <h2>
                    Hospital Directory
                </h2>

                <p style="color: #667085;"> 
                    Manage hospitals available in the fctCare system.
                </p>

            </div>


            <div class="title-tools">

                <div class="table-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
                    </svg>
                    <input
                        type="text"
                        id="hospitalSearch"
                        placeholder="Search by hospital name..."
                        aria-label="Search by hospital name"
                        autocomplete="off"
                    >
                </div>

                <a href="add_hospital.php" class="btn btn-confirm">
                    + Add New Hospital
                </a>

            </div>

        </div>



        <!-- ================= HOSPITAL COUNT ================= -->

        <div style="
            margin-bottom: 20px;
            color: #667085;
            font-size: 14px;
        ">

            Total Hospitals:

            <strong>
                <?= $result ? $result->num_rows : 0 ?>
            </strong>

        </div>



        <!-- ================= TABLE ================= -->

        <div class="table-container">

            <?php if ($result && $result->num_rows > 0): ?>

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Hospital
                            </th>

                            <th>
                                District
                            </th>

                            <th>
                                Fee
                            </th>

                            <th>
                                Telephone
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($hospital = $result->fetch_assoc()): ?>

                        <tr>

                            <!-- ID -->

                            <td>
                                <?= htmlspecialchars($hospital["id"]) ?>
                            </td>


                            <!-- Hospital Name -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars($hospital["name"]) ?>
                                </strong>

                            </td>


                            <!-- District -->

                            <td>

                                <?= htmlspecialchars($hospital["district"]) ?>

                            </td>


                            <!-- Fee -->

                            <td>

                                Rs.
                                <?= number_format(
                                    (float)$hospital["fee"],
                                    2
                                ) ?>

                            </td>


                            <!-- Telephone -->

                            <td>

                                <?= htmlspecialchars(
                                    $hospital["telephone"]
                                ) ?>

                            </td>


                            <!-- Actions -->

                            <td>

                                <a
                                    href="edit_hospital.php?id=<?= $hospital["id"] ?>"
                                    class="btn"
                                    style="
                                        padding: 8px 14px;
                                        font-size: 12px;
                                    "
                                >
                                    Edit
                                </a>


                                <a
                                    href="delete_hospital.php?id=<?= $hospital["id"] ?>"
                                    class="btn btn-danger"
                                    style="
                                        padding: 8px 14px;
                                        font-size: 12px;
                                        margin-left: 5px;
                                    "
                                    onclick="return confirm(
                                        'Are you sure you want to delete this hospital?'
                                    );"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    <tr id="noMatchRow" style="display:none;">
                        <td colspan="6" style="text-align:center; padding:30px;">
                            No matching hospital found
                        </td>
                    </tr>

                    </tbody>

                </table>


            <?php else: ?>

                <!-- ================= NO HOSPITALS ================= -->

                <div style="
                    text-align: center;
                    padding: 50px 20px;
                    color: #667085;
                ">

                    <div style="
                        font-size: 45px;
                        margin-bottom: 15px;
                    ">
                        🏥
                    </div>


                    <h3 style="
                        color: #344054;
                        margin-bottom: 8px;
                    ">
                        No Hospitals Found
                    </h3>


                    <p style="
                        font-size: 14px;
                        margin-bottom: 20px;
                    ">
                        No hospitals have been added to the system yet.
                    </p>


                    <a
                        href="add_hospital.php"
                        class="btn btn-confirm"
                    >
                        + Add First Hospital
                    </a>

                </div>

            <?php endif; ?>

        </div>


    </div>

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
        var input = document.getElementById("hospitalSearch");
        var rows = document.querySelectorAll(".table-container tbody tr:not(#noMatchRow)");
        var noMatch = document.getElementById("noMatchRow");

        if (!input) return;

        input.addEventListener("input", function () {
            var q = input.value.trim().toLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var cell = row.children[1];                // Hospital column
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