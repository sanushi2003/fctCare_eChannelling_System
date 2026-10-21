
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$error = "";

$doctors = $conn->query(
    "SELECT id, name, specialty
     FROM doctors
     ORDER BY name ASC"
);

$hospitals = $conn->query(
    "SELECT id, name, district
     FROM hospitals
     ORDER BY name ASC"
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doctor_id = intval($_POST["doctor_id"] ?? 0);
    $hospital_id = intval($_POST["hospital_id"] ?? 0);
    $day_of_week = trim($_POST["day_of_week"] ?? "");
    $start_time = $_POST["start_time"] ?? "";
    $end_time = $_POST["end_time"] ?? "";
    $status = $_POST["status"] ?? "Active";

    $valid_days = [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
    ];

    if (
        $doctor_id <= 0 ||
        $hospital_id <= 0 ||
        $day_of_week === "" ||
        $start_time === "" ||
        $end_time === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (!in_array($day_of_week, $valid_days, true)) {

        $error = "Please select a valid day.";

    } elseif (!in_array($status, ["Active", "Inactive"], true)) {

        $error = "Please select a valid status.";

    } elseif ($start_time >= $end_time) {

        $error = "End time must be later than start time.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM doctors WHERE id = ?"
        );

        $check->bind_param("i", $doctor_id);
        $check->execute();
        $doctor_exists = $check->get_result()->num_rows > 0;
        $check->close();

        $check = $conn->prepare(
            "SELECT id FROM hospitals WHERE id = ?"
        );

        $check->bind_param("i", $hospital_id);
        $check->execute();
        $hospital_exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$doctor_exists || !$hospital_exists) {

            $error = "Please select a valid doctor and hospital.";

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

                $stmt->close();

                header("Location: schedules.php");
                exit;

            } else {

                $error = "Failed to add schedule.";

            }

            $stmt->close();
        }
    }
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

    <title>Add Schedule - fctCare Admin</title>

    <link
        rel="icon"
        type="image/jpeg"
        href="../logo.jpeg"
    >

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>

<body style="
    background:
        linear-gradient(
            rgba(255,255,255,0.35),
            rgba(255,255,255,0.35)
        ),
        url('../bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

<div class="admin-container">

    <div class="admin-header">

        <div>

            <div class="brand">

                <img
                    src="../logo.jpeg"
                    alt="fctCare Logo"
                    class="logo"
                >

                fct<span>Care</span>

            </div>

            <h1>
                Add New Schedule
            </h1>

        </div>

        <div>

            <a
                href="schedules.php"
                class="btnb"
            >
                ← Schedule Directory
            </a>

        </div>

    </div>

    <div class="page-content">

        <div style="
            margin-bottom:25px;
        ">

            <h2 style="
                font-size:25px;
                color:#1d2939;
                margin-bottom:7px;
            ">
                Schedule Registration
            </h2>

            <p style="
                color:#667085;
                font-size:14px;
            ">
                Create a new doctor appointment schedule at a hospital.
            </p>

        </div>

        <div class="form-container">

            <div style="
                display:flex;
                align-items:center;
                gap:15px;
                margin-bottom:25px;
            ">

                <div style="
                    width:55px;
                    height:55px;
                    min-width:55px;
                    border-radius:15px;
                    background:linear-gradient(
                        135deg,
                        #dff7f4,
                        #bceae6
                    );
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:27px;
                ">
                    🗓️
                </div>

                <div>

                    <h3 style="
                        font-size:18px;
                        color:#1d2939;
                        margin-bottom:4px;
                    ">
                        Schedule Information
                    </h3>

                    <p style="
                        font-size:13px;
                        color:#667085;
                    ">
                        Enter the doctor, hospital and appointment times.
                    </p>

                </div>

            </div>

            <?php if ($error !== ""): ?>

                <div class="error-message">

                    ⚠️

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <label for="doctor_id">
                    Doctor
                </label>

                <select
                    name="doctor_id"
                    id="doctor_id"
                    required
                >

                    <option value="">
                        Select Doctor
                    </option>

                    <?php while ($doctor = $doctors->fetch_assoc()): ?>

                        <option
                            value="<?= (int)$doctor["id"] ?>"
                            <?= (($_POST["doctor_id"] ?? "") == $doctor["id"]) ? "selected" : "" ?>
                        >

                            <?= htmlspecialchars($doctor["name"]) ?>
                            -
                            <?= htmlspecialchars($doctor["specialty"]) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

                <div style="
                    font-size:11px;
                    color:#98a2b3;
                    margin-top:5px;
                    margin-bottom:18px;
                ">
                    Select the doctor who will provide the appointments.
                </div>

                <label for="hospital_id">
                    Hospital
                </label>

                <select
                    name="hospital_id"
                    id="hospital_id"
                    required
                >

                    <option value="">
                        Select Hospital
                    </option>

                    <?php while ($hospital = $hospitals->fetch_assoc()): ?>

                        <option
                            value="<?= (int)$hospital["id"] ?>"
                            <?= (($_POST["hospital_id"] ?? "") == $hospital["id"]) ? "selected" : "" ?>
                        >

                            <?= htmlspecialchars($hospital["name"]) ?>
                            -
                            <?= htmlspecialchars($hospital["district"]) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

                <div style="
                    font-size:11px;
                    color:#98a2b3;
                    margin-top:5px;
                    margin-bottom:18px;
                ">
                    Select the hospital where the doctor is available.
                </div>

                <label for="day_of_week">
                    Day of Week
                </label>

                <select
                    name="day_of_week"
                    id="day_of_week"
                    required
                >

                    <option value="">
                        Select Day
                    </option>

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

                    <?php foreach ($days as $day): ?>

                        <option
                            value="<?= $day ?>"
                            <?= (($_POST["day_of_week"] ?? "") === $day) ? "selected" : "" ?>
                        >
                            <?= $day ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <div style="
                    font-size:11px;
                    color:#98a2b3;
                    margin-top:5px;
                    margin-bottom:18px;
                ">
                    Choose the day when the doctor is available.
                </div>

                <label for="start_time">
                    Start Time
                </label>

                <input
                    type="time"
                    id="start_time"
                    name="start_time"
                    value="<?= htmlspecialchars($_POST["start_time"] ?? "") ?>"
                    required
                >

                <div style="
                    font-size:11px;
                    color:#98a2b3;
                    margin-top:5px;
                    margin-bottom:18px;
                ">
                    Enter the starting time of the appointment session.
                </div>

                <label for="end_time">
                    End Time
                </label>

                <input
                    type="time"
                    id="end_time"
                    name="end_time"
                    value="<?= htmlspecialchars($_POST["end_time"] ?? "") ?>"
                    required
                >

                <div style="
                    font-size:11px;
                    color:#98a2b3;
                    margin-top:5px;
                    margin-bottom:18px;
                ">
                    The end time must be later than the start time.
                </div>

                <label for="status">
                    Schedule Status
                </label>

                <select
                    name="status"
                    id="status"
                >

                    <option
                        value="Active"
                        <?= (($_POST["status"] ?? "Active") === "Active") ? "selected" : "" ?>
                    >
                        Active
                    </option>

                    <option
                        value="Inactive"
                        <?= (($_POST["status"] ?? "") === "Inactive") ? "selected" : "" ?>
                    >
                        Inactive
                    </option>

                </select>

                <div style="
                    font-size:11px;
                    color:#98a2b3;
                    margin-top:5px;
                    margin-bottom:25px;
                ">
                    Active schedules can be used to manage doctor availability.
                </div>

                <div class="form-actions">

                    <button
                        type="submit"
                        class="btn btn-confirm"
                    >
                        ✓ Add Schedule
                    </button>

                    <a
                        href="schedules.php"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

        <div style="
            max-width:700px;
            margin:20px auto 0;
            background:#f0fafa;
            border:1px solid #ccefeb;
            border-radius:14px;
            padding:18px 20px;
        ">

            <div style="
                display:flex;
                gap:12px;
                align-items:flex-start;
            ">

                <div style="
                    font-size:20px;
                ">
                    💡
                </div>

                <div>

                    <div style="
                        font-weight:700;
                        color:#066b65;
                        margin-bottom:5px;
                        font-size:14px;
                    ">
                        Before adding a schedule
                    </div>

                    <div style="
                        color:#4f6f6c;
                        font-size:12px;
                        line-height:1.6;
                    ">
                        Make sure the correct doctor and hospital are selected.
                        Verify the day, starting time and ending time before
                        saving the schedule. You can manage schedules later
                        from the Schedule Directory.
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>

<?php

$conn->close();

?>
