
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

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>


<body>

<div class="admin-container">


    <!-- =========================
         SIDEBAR
    ========================== -->

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


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-content">

        <h1>
            Appointments
        </h1>

        <p>
            Manage patient appointment requests.
        </p>


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


                </tbody>

            </table>

        </div>

    </main>

</div>


</body>

</html>


<?php

$conn->close();

?>

