<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


// ================= MARK MESSAGE AS READ =================

if (isset($_GET["read"])) {

    $id = intval($_GET["read"]);

    if ($id > 0) {

        $stmt = $conn->prepare(
            "UPDATE messages
             SET status = 'Read'
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: messages.php");
    exit;
}


// ================= DELETE MESSAGE =================

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    if ($id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM messages
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: messages.php");
    exit;
}


// ================= GET MESSAGES =================

$result = $conn->query(
    "SELECT
        id,
        name,
        email,
        message,
        status,
        created_at
     FROM messages
     ORDER BY created_at DESC"
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

    <title>Messages - Admin</title>
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


        <!-- TOP BAR -->

        <div class="top-bar">

            <div>

                <h1>
                    Messages
                </h1>

                <p>
                    Manage contact messages
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



        <!-- ================= MESSAGE SECTION ================= -->

        <section class="dashboard-section">


            <div class="page-title-row">

                <div>

                    <h2>
                        Contact Messages
                    </h2>

                    <p>

                        Total Messages:

                        <strong>
                            <?= $result ? $result->num_rows : 0 ?>
                        </strong>

                    </p>

                </div>

            </div>



            <!-- ================= RESPONSIVE TABLE ================= -->

            <div class="messages-table-container">


                <table class="messages-table">


                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Message
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Date
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


                                <!-- ID -->

                                <td>

                                    <?= htmlspecialchars(
                                        $row["id"]
                                    ) ?>

                                </td>



                                <!-- NAME -->

                                <td>

                                    <?= htmlspecialchars(
                                        $row["name"]
                                    ) ?>

                                </td>



                                <!-- EMAIL -->

                                <td class="message-email">

                                    <?= htmlspecialchars(
                                        $row["email"]
                                    ) ?>

                                </td>



                                <!-- MESSAGE -->

                                <td class="message-text">

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $row["message"]
                                        )
                                    ) ?>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <?php if (
                                        $row["status"] === "Unread"
                                    ): ?>

                                        <span
                                            class="message-status unread"
                                        >
                                            Unread
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="message-status read"
                                        >
                                            Read
                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- DATE -->

                                <td>

                                    <?= date(
                                        "Y-m-d H:i",
                                        strtotime(
                                            $row["created_at"]
                                        )
                                    ) ?>

                                </td>



                                <!-- ACTIONS -->

                                <td>

                                    <div
                                        class="message-actions"
                                    >


                                        <?php if (
                                            $row["status"] === "Unread"
                                        ): ?>

                                            <a
                                                href="messages.php?read=<?= $row["id"] ?>"
                                                class="btn btn-confirm"
                                            >
                                                Mark Read
                                            </a>

                                        <?php endif; ?>


                                        <a
                                            href="messages.php?delete=<?= $row["id"] ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this message?');"
                                        >
                                            Delete
                                        </a>


                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="7"
                                style="
                                    text-align:center;
                                    padding:30px;
                                "
                            >
                                No Messages Found
                            </td>

                        </tr>


                    <?php endif; ?>


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

    


</body>

</html>


<?php

$conn->close();

?>