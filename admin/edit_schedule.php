<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: schedules.php");
    exit;
}


// ================= GET CURRENT SCHEDULE =================

$stmt = $conn->prepare(
    "SELECT
        id,
        doctor_id,
        hospital_id,
        day_of_week,
        start_time,
        end_time,
        status
     FROM schedules
     WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$schedule = $result->fetch_assoc();

$stmt->close();


if (!$schedule) {

    header("Location: schedules.php");
    exit;
}


// ================= GET DOCTORS =================

$doctors = $conn->query(
    "SELECT id, name, specialty
     FROM doctors
     ORDER BY name ASC"
);


// ================= GET HOSPITALS =================

$hospitals = $conn->query(
    "SELECT id, name, district
     FROM hospitals
     ORDER BY name ASC"
);


// ================= UPDATE =================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doctor_id = intval(
        $_POST["doctor_id"] ?? 0
    );

    $hospital_id = intval(
        $_POST["hospital_id"] ?? 0
    );

    $day_of_week = trim(
        $_POST["day_of_week"] ?? ""
    );

    $start_time = $_POST["start_time"] ?? "";

    $end_time = $_POST["end_time"] ?? "";

    $status = $_POST["status"] ?? "Active";


    if (
        $doctor_id <= 0 ||
        $hospital_id <= 0 ||
        $day_of_week === "" ||
        $start_time === "" ||
        $end_time === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif ($start_time >= $end_time) {

        $error = "End time must be later than start time.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE schedules
             SET
                doctor_id = ?,
                hospital_id = ?,
                day_of_week = ?,
                start_time = ?,
                end_time = ?,
                status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "iissssi",
            $doctor_id,
            $hospital_id,
            $day_of_week,
            $start_time,
            $end_time,
            $status,
            $id
        );


        if ($stmt->execute()) {

            header(
                "Location: schedules.php"
            );

            exit;

        } else {

            $error = "Failed to update schedule.";

        }

        $stmt->close();
    }
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

    <title>Edit Schedule - Admin</title>

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>


<body>


<div class="admin-container">


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



    <main class="main-content">


        <div class="top-bar">

            <div>

                <h1>
                    Edit Schedule
                </h1>

                <p>
                    Update doctor time slot
                </p>

            </div>

        </div>



        <section class="form-section">


            <?php if (isset($error)): ?>

                <div
                    style="
                        background:#f8d7da;
                        color:#721c24;
                        padding:12px;
                        border-radius:6px;
                        margin-bottom:20px;
                    "
                >
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="form-group">

                    <label>
                        Doctor
                    </label>

                    <select
                        name="doctor_id"
                        required
                    >

                        <?php while (
                            $doctor = $doctors->fetch_assoc()
                        ): ?>

                            <option
                                value="<?= $doctor["id"] ?>"
                                <?= $doctor["id"] == $schedule["doctor_id"]
                                    ? "selected"
                                    : "" ?>
                            >

                                <?= htmlspecialchars(
                                    $doctor["name"]
                                ) ?>

                                -
                                <?= htmlspecialchars(
                                    $doctor["specialty"]
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>



                <div class="form-group">

                    <label>
                        Hospital
                    </label>

                    <select
                        name="hospital_id"
                        required
                    >

                        <?php while (
                            $hospital =
                            $hospitals->fetch_assoc()
                        ): ?>

                            <option
                                value="<?= $hospital["id"] ?>"
                                <?= $hospital["id"] == $schedule["hospital_id"]
                                    ? "selected"
                                    : "" ?>
                            >

                                <?= htmlspecialchars(
                                    $hospital["name"]
                                ) ?>

                                -
                                <?= htmlspecialchars(
                                    $hospital["district"]
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>



                <div class="form-group">

                    <label>
                        Day
                    </label>

                    <select
                        name="day_of_week"
                        required
                    >

                        <?php
                        $days = [
                            "Monday",
                            "Tuesday",
                            "Wednesday",
                            "Thursday",
                            "Friday",
                            "Saturday",
                            "Sunday"
                        ];
                        ?>

                        <?php foreach (
                            $days as $day
                        ): ?>

                            <option
                                value="<?= $day ?>"
                                <?= $day === $schedule["day_of_week"]
                                    ? "selected"
                                    : "" ?>
                            >

                                <?= $day ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>



                <div class="form-group">

                    <label>
                        Start Time
                    </label>

                    <input
                        type="time"
                        name="start_time"
                        value="<?= htmlspecialchars(
                            $schedule["start_time"]
                        ) ?>"
                        required
                    >

                </div>



                <div class="form-group">

                    <label>
                        End Time
                    </label>

                    <input
                        type="time"
                        name="end_time"
                        value="<?= htmlspecialchars(
                            $schedule["end_time"]
                        ) ?>"
                        required
                    >

                </div>



                <div class="form-group">

                    <label>
                        Status
                    </label>

                    <select
                        name="status"
                    >

                        <option
                            value="Active"
                            <?= $schedule["status"] === "Active"
                                ? "selected"
                                : "" ?>
                        >
                            Active
                        </option>

                        <option
                            value="Inactive"
                            <?= $schedule["status"] === "Inactive"
                                ? "selected"
                                : "" ?>
                        >
                            Inactive
                        </option>

                    </select>

                </div>



                <button
                    type="submit"
                    class="btn btn-confirm"
                >
                    Update Schedule
                </button>


                <a
                    href="schedules.php"
                    class="btn"
                >
                    Cancel
                </a>


            </form>


        </section>


    </main>


</div>


</body>

</html>

<?php

$conn->close();

?>