<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$message = "";
$selected_doctor = intval($_GET["doctor_id"] ?? $_POST["doctor_id"] ?? 0);


// ======================================================
// GET ALL DOCTORS
// ======================================================

$doctors = $conn->query(
    "SELECT id, doctor_code, name, specialty
     FROM doctors
     ORDER BY name ASC"
);


// ======================================================
// GET ALL HOSPITALS
// ======================================================

$hospitals = $conn->query(
    "SELECT id, name, district
     FROM hospitals
     ORDER BY name ASC"
);


// ======================================================
// SAVE DOCTOR-HOSPITAL RELATIONSHIP
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doctor_id = intval($_POST["doctor_id"] ?? 0);

    $selected_hospitals = $_POST["hospitals"] ?? [];


    if ($doctor_id <= 0) {

        $message = "Please select a doctor.";

    } else {

        // Remove existing hospital assignments

        $delete = $conn->prepare(
            "DELETE FROM doctor_hospitals
             WHERE doctor_id = ?"
        );

        $delete->bind_param(
            "i",
            $doctor_id
        );

        $delete->execute();

        $delete->close();


        // Add new hospital assignments

        if (!empty($selected_hospitals)) {

            $insert = $conn->prepare(
                "INSERT INTO doctor_hospitals
                (doctor_id, hospital_id)
                VALUES (?, ?)"
            );


            foreach ($selected_hospitals as $hospital_id) {

                $hospital_id = intval($hospital_id);

                if ($hospital_id > 0) {

                    $insert->bind_param(
                        "ii",
                        $doctor_id,
                        $hospital_id
                    );

                    $insert->execute();
                }
            }


            $insert->close();

        }


        $selected_doctor = $doctor_id;

        $message = "Hospital assignments updated successfully.";

    }

}


// ======================================================
// GET CURRENT HOSPITAL ASSIGNMENTS
// ======================================================

$assigned_hospitals = [];

if ($selected_doctor > 0) {

    $stmt = $conn->prepare(
        "SELECT hospital_id
         FROM doctor_hospitals
         WHERE doctor_id = ?"
    );

    $stmt->bind_param(
        "i",
        $selected_doctor
    );

    $stmt->execute();

    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {

        $assigned_hospitals[] = $row["hospital_id"];

    }


    $stmt->close();
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

    <title>Doctor Hospitals - fctCare Admin</title>
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


    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="admin-header">

        <div>

            <div style="
                font-size: 13px;
                opacity: 0.85;
                margin-bottom: 5px;
            ">
                fctCare Administration
            </div>

            <h1>
                Doctor ↔ Hospital
            </h1>

        </div>


        <div>

            <a
                href="doctors.php"
                class="btn"
            >
                ← Doctor Directory
            </a>

        </div>

    </div>



    <!-- ==================================================
         PAGE CONTENT
    ================================================== -->

    <div class="page-content">


        <div style="
            margin-bottom: 25px;
        ">

            <h2 style="
                font-size: 25px;
                color: #1d2939;
                margin-bottom: 7px;
            ">
                Assign Hospitals to Doctor
            </h2>

            <p style="
                color: #667085;
                font-size: 14px;
            ">
                Select a doctor and assign the hospitals where
                the doctor provides consultations.
            </p>

        </div>



        <!-- ==================================================
             MESSAGE
        ================================================== -->

        <?php if ($message !== ""): ?>

            <div
                style="
                    max-width: 700px;
                    margin: 0 auto 20px;
                    padding: 14px 18px;
                    border-radius: 10px;
                    background: #ecfdf3;
                    border: 1px solid #abefc6;
                    color: #067647;
                    font-size: 14px;
                "
            >

                ✓

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>



        <!-- ==================================================
             FORM
        ================================================== -->

        <div class="form-container">


            <form method="POST">


                <!-- DOCTOR -->

                <label for="doctor_id">
                    Select Doctor
                </label>


                <select
                    id="doctor_id"
                    name="doctor_id"
                    required
                    onchange="this.form.submit()"
                >

                    <option value="">
                        -- Select Doctor --
                    </option>


                    <?php if ($doctors && $doctors->num_rows > 0): ?>

                        <?php while ($doctor = $doctors->fetch_assoc()): ?>

                            <option
                                value="<?= $doctor["id"] ?>"
                                <?= $selected_doctor == $doctor["id"] ? "selected" : "" ?>
                            >

                                <?= htmlspecialchars($doctor["name"]) ?>

                                -
                                <?= htmlspecialchars($doctor["specialty"]) ?>

                            </option>

                        <?php endwhile; ?>

                    <?php endif; ?>

                </select>



                <?php if ($selected_doctor > 0): ?>


                    <!-- HOSPITALS -->

                    <label style="
                        margin-top: 25px;
                        display: block;
                    ">
                        Select Hospitals
                    </label>


                    <div style="
                        border: 1px solid #d0d5dd;
                        border-radius: 10px;
                        padding: 15px;
                        max-height: 350px;
                        overflow-y: auto;
                    ">


                        <?php if ($hospitals && $hospitals->num_rows > 0): ?>


                            <?php while ($hospital = $hospitals->fetch_assoc()): ?>


                                <label style="
                                    display: flex;
                                    align-items: center;
                                    gap: 10px;
                                    padding: 12px;
                                    margin: 0;
                                    border-bottom: 1px solid #eaecf0;
                                    cursor: pointer;
                                ">


                                    <input
                                        type="checkbox"
                                        name="hospitals[]"
                                        value="<?= $hospital["id"] ?>"
                                        <?= in_array(
                                            $hospital["id"],
                                            $assigned_hospitals
                                        ) ? "checked" : "" ?>
                                        style="
                                            width: 18px;
                                            height: 18px;
                                        "
                                    >


                                    <span>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $hospital["name"]
                                            ) ?>
                                        </strong>

                                        <span style="
                                            color: #667085;
                                            font-size: 12px;
                                        ">

                                            -
                                            <?= htmlspecialchars(
                                                $hospital["district"]
                                            ) ?>

                                        </span>

                                    </span>


                                </label>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <p style="
                                color: #667085;
                                font-size: 14px;
                            ">
                                No hospitals found.
                            </p>


                        <?php endif; ?>


                    </div>



                    <!-- SAVE BUTTON -->

                    <div style="
                        display: flex;
                        gap: 12px;
                        margin-top: 25px;
                    ">


                        <button
                            type="submit"
                            class="btn btn-confirm"
                            style="
                                flex: 1;
                                padding: 14px;
                            "
                        >
                            ✓ Save Hospital Assignments
                        </button>


                        <a
                            href="doctors.php"
                            class="btn"
                            style="
                                flex: 1;
                                padding: 14px;
                                background: #f2f4f7;
                                color: #344054;
                                text-align: center;
                            "
                        >
                            Cancel
                        </a>


                    </div>


                <?php endif; ?>


            </form>


        </div>



        <!-- ==================================================
             INFORMATION CARD
        ================================================== -->

        <div style="
            max-width: 700px;
            margin: 20px auto 0;
            background: #f0fafa;
            border: 1px solid #ccefeb;
            border-radius: 14px;
            padding: 18px 20px;
        ">


            <div style="
                display: flex;
                gap: 12px;
                align-items: flex-start;
            ">


                <div style="
                    font-size: 20px;
                ">
                    💡
                </div>


                <div>

                    <div style="
                        font-weight: 700;
                        color: #066b65;
                        margin-bottom: 5px;
                        font-size: 14px;
                    ">
                        How this works
                    </div>


                    <div style="
                        color: #4f6f6c;
                        font-size: 12px;
                        line-height: 1.6;
                    ">

                        One doctor can be assigned to multiple hospitals.
                        Select all hospitals where the doctor provides
                        consultations and click
                        <strong>
                            Save Hospital Assignments
                        </strong>.

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