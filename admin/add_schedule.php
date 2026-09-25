<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


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


// ================= ADD SCHEDULE =================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doctor_id = intval($_POST["doctor_id"] ?? 0);

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
            "INSERT INTO schedules
            (
                doctor_id,
                hospital_id,
                day_of_week,
                start_time,
                end_time,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iissss",
            $doctor_id,
            $hospital_id,
            $day_of_week,
            $start_time,
            $end_time,
            $status
        );


        if ($stmt->execute()) {

            header(
                "Location: schedules.php"
            );

            exit;

        } else {

            $error = "Failed to add schedule.";

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

    <title>Add Schedule - Admin</title>

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
                    Add Schedule
                </h1>

                <p>
                    Create a new doctor time slot
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

                        <option value="">
                            Select Doctor
                        </option>

                        <?php while (
                            $doctor = $doctors->fetch_assoc()
                        ): ?>

                            <option
                                value="<?= $doctor["id"] ?>"
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

                        <option value="">
                            Select Hospital
                        </option>

                        <?php while (
                            $hospital =
                            $hospitals->fetch_assoc()
                        ): ?>

                            <option
                                value="<?= $hospital["id"] ?>"
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

                        <option value="">
                            Select Day
                        </option>

                        <option value="Monday">
                            Monday
                        </option>

                        <option value="Tuesday">
                            Tuesday
                        </option>

                        <option value="Wednesday">
                            Wednesday
                        </option>

                        <option value="Thursday">
                            Thursday
                        </option>

                        <option value="Friday">
                            Friday
                        </option>

                        <option value="Saturday">
                            Saturday
                        </option>

                        <option value="Sunday">
                            Sunday
                        </option>

                    </select>

                </div>



                <div class="form-group">

                    <label>
                        Start Time
                    </label>

                    <input
                        type="time"
                        name="start_time"
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

                        <option value="Active">
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>

                </div>



                <button
                    type="submit"
                    class="btn btn-confirm"
                >
                    Add Schedule
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