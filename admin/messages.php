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

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>


<body>


<div class="admin-container">


    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">
        <div class="sidebar-brand">

          <img src="logo.jpeg" alt="fctCare Logo" class="logo"> 
            <h2>fctCare</h2>
        </div>

        

        <p class="admin-label">
            Admin Panel
        </p>


        <a href="dashboard.php">
            Dashboard
        </a>


        <a href="appointments.php">
            Appointments
        </a>


        <a href="messages.php">
            Messages
        </a>


        <a href="doctors.php">
            Doctors
        </a>


        <a href="hospitals.php">
            Hospitals
        </a>


        <a href="schedules.php">
            Schedules
        </a>


        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

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


</body>

</html>


<?php

$conn->close();

?>