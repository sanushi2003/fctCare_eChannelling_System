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

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>


<body>


<div class="admin-container">


    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

        <h2>fctCare</h2>

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


                <div>

                    <a
                        href="add_schedule.php"
                        class="btn btn-confirm"
                    >
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