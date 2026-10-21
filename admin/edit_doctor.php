<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: doctors.php");
    exit;
}


/* ================= GET DOCTOR ================= */

$stmt = $conn->prepare(
    "SELECT * FROM doctors WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$doctor = $result->fetch_assoc();

$stmt->close();


if (!$doctor) {
    header("Location: doctors.php");
    exit;
}


$message = "";


/* ================= UPDATE DOCTOR ================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doctor_code = trim($_POST["doctor_code"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $specialty = trim($_POST["specialty"] ?? "");
    $fee = floatval($_POST["fee"] ?? 0);


    if (
        $doctor_code === "" ||
        $name === "" ||
        $specialty === "" ||
        $fee <= 0
    ) {

        $message = "Please fill all fields correctly.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE doctors
             SET doctor_code = ?,
                 name = ?,
                 specialty = ?,
                 fee = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssdi",
            $doctor_code,
            $name,
            $specialty,
            $fee,
            $id
        );


        if ($stmt->execute()) {

            header("Location: doctors.php");
            exit;

        } else {

            $message = "Failed to update doctor.";

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

    <title>Edit Doctor - fctCare Admin</title>
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


    <!-- ================= HEADER ================= -->

    <div class="admin-header">

        <div>

            <div class="brand">
                <img src="logo.jpeg" alt="fctCare Logo" class="logo">
                fct<span>Care</span>
            </div>

            <h1>
                Edit Doctor
            </h1>

        </div>


        <div>

            <a
                href="doctors.php"
                class="btnb"
            >
                ← Doctor Directory
            </a>

        </div>

    </div>



    <!-- ================= PAGE CONTENT ================= -->

    <div class="page-content">


        <!-- Page Introduction -->

        <div style="
            margin-bottom: 25px;
        ">

            <h2 style="
                font-size: 25px;
                color: #1d2939;
                margin-bottom: 7px;
            ">
                Update Doctor Profile
            </h2>

            <p style="
                color: #667085;
                font-size: 14px;
            ">
                Update the professional information and consultation
                fee of this doctor.
            </p>

        </div>



        <!-- ================= FORM CARD ================= -->

        <div class="form-container">


            <!-- Doctor Header -->

            <div style="
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 25px;
            ">


                <!-- Doctor Avatar -->

                <div style="
                    width: 55px;
                    height: 55px;

                    border-radius: 15px;

                    background: linear-gradient(
                        135deg,
                        #dff7f4,
                        #bceae6
                    );

                    display: flex;
                    align-items: center;
                    justify-content: center;

                    color: #087f78;

                    font-size: 23px;
                    font-weight: 700;
                ">

                    <?= strtoupper(
                        substr($doctor["name"], 0, 1)
                    ) ?>

                </div>


                <div>

                    <h3 style="
                        font-size: 18px;
                        color: #1d2939;
                        margin-bottom: 4px;
                    ">

                        Dr.
                        <?= htmlspecialchars(
                            $doctor["name"]
                        ) ?>

                    </h3>


                    <p style="
                        font-size: 13px;
                        color: #667085;
                    ">

                        Doctor ID:
                        #<?= htmlspecialchars(
                            $doctor["id"]
                        ) ?>

                    </p>

                </div>

            </div>



            <!-- Error Message -->

            <?php if ($message !== ""): ?>

                <div class="error-message">

                    ⚠️

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>



            <!-- ================= FORM ================= -->

            <form method="POST">


                <!-- Doctor Code -->

                <label for="doctor_code">

                    Doctor Code

                </label>


                <input
                    type="text"
                    id="doctor_code"
                    name="doctor_code"
                    value="<?= htmlspecialchars(
                        $doctor["doctor_code"]
                    ) ?>"
                    maxlength="20"
                    required
                >


                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">

                    Unique identification code for the doctor.

                </div>



                <!-- Doctor Name -->

                <label for="name">

                    Doctor Name

                </label>


                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars(
                        $doctor["name"]
                    ) ?>"
                    maxlength="100"
                    required
                >


                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">

                    Full name of the medical professional.

                </div>



                <!-- Specialty -->

                <label for="specialty">

                    Medical Specialty

                </label>


                <input
                    type="text"
                    id="specialty"
                    name="specialty"
                    value="<?= htmlspecialchars(
                        $doctor["specialty"]
                    ) ?>"
                    maxlength="100"
                    required
                >


                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">

                    Example: Cardiologist, Neurologist,
                    Dermatologist.

                </div>



                <!-- Consultation Fee -->

                <label for="fee">

                    Consultation Fee

                </label>


                <div style="
                    position: relative;
                ">


                    <span style="
                        position: absolute;

                        left: 15px;
                        top: 50%;

                        transform: translateY(-50%);

                        color: #667085;

                        font-size: 13px;
                        font-weight: 600;

                        pointer-events: none;
                    ">

                        Rs.

                    </span>


                    <input
                        type="number"
                        id="fee"
                        name="fee"
                        value="<?= htmlspecialchars(
                            $doctor["fee"]
                        ) ?>"
                        step="0.01"
                        min="1"
                        style="
                            padding-left: 48px;
                        "
                        required
                    >

                </div>


                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">

                    Current consultation fee in Sri Lankan Rupees.

                </div>



                <!-- ================= BUTTONS ================= -->

                <div class="form-actions">


                    <button
                        type="submit"
                        class="btn btn-confirm"
                        
                    >

                        ✓ Update Doctor

                    </button>


                    <a
                        href="doctors.php"
                        class="btn btn-secondary"
                        
                            
                    >

                        Cancel

                    </a>


                </div>


            </form>


        </div>



        <!-- ================= INFORMATION CARD ================= -->

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

                        Update Information

                    </div>


                    <div style="
                        color: #4f6f6c;

                        font-size: 12px;

                        line-height: 1.6;
                    ">

                        Review the doctor's information before
                        saving changes. Updated details will be
                        reflected in the fctCare doctor directory.

                    </div>

                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>