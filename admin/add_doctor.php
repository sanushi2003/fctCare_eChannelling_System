<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$message = "";

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
            "INSERT INTO doctors
            (doctor_code, name, specialty, fee)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssd",
            $doctor_code,
            $name,
            $specialty,
            $fee
        );

        if ($stmt->execute()) {

            header("Location: doctors.php");
            exit;

        } else {

            $message = "Failed to add doctor.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Doctor - fctCare Admin</title>

    <link rel="stylesheet" href="admin.css">

</head>


<body>


<div class="admin-container">


    <!-- ================= HEADER ================= -->

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
                Add New Doctor
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
                Doctor Registration
            </h2>

            <p style="
                color: #667085;
                font-size: 14px;
            ">
                Add a new medical professional to the fctCare system.
            </p>

        </div>



        <!-- ================= FORM CARD ================= -->

        <div class="form-container">


            <!-- Form Header -->

            <div style="
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 25px;
            ">

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

                    font-size: 27px;
                ">
                    👨‍⚕️
                </div>


                <div>

                    <h3 style="
                        font-size: 18px;
                        color: #1d2939;
                        margin-bottom: 4px;
                    ">
                        Doctor Information
                    </h3>

                    <p style="
                        font-size: 13px;
                        color: #667085;
                    ">
                        Enter the professional details below.
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

                <div style="position: relative;">

                    <input
                        type="text"
                        id="doctor_code"
                        name="doctor_code"
                        placeholder="Example: DOC001"
                        maxlength="20"
                        required
                    >

                </div>

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Enter a unique identification code for the doctor.
                </div>



                <!-- Doctor Name -->

                <label for="name">
                    Doctor Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Example: John Silva"
                    maxlength="100"
                    required
                >

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Enter the doctor's full name.
                </div>



                <!-- Specialty -->

                <label for="specialty">
                    Medical Specialty
                </label>

                <input
                    type="text"
                    id="specialty"
                    name="specialty"
                    placeholder="Example: Cardiologist"
                    maxlength="100"
                    required
                >

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Example: Cardiologist, Neurologist, Dermatologist.
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
                        step="0.01"
                        min="1"
                        placeholder="2500.00"
                        style="padding-left: 48px;"
                        required
                    >

                </div>

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Enter the doctor's consultation fee in Sri Lankan Rupees.
                </div>



                <!-- ================= BUTTONS ================= -->

                <div style="
                    display: flex;
                    gap: 12px;
                    margin-top: 30px;
                ">

                    <button
                        type="submit"
                        class="btn btn-confirm"
                        style="
                            flex: 1;
                            padding: 14px;
                        "
                    >
                        ✓ Add Doctor
                    </button>


                    <a
                        href="doctors.php"
                        class="btn"
                        style="
                            flex: 1;
                            padding: 14px;
                            background: #f2f4f7;
                            color: #344054;
                        "
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
                        Before adding a doctor
                    </div>

                    <div style="
                        color: #4f6f6c;
                        font-size: 12px;
                        line-height: 1.6;
                    ">
                        Make sure the doctor code is unique and the
                        consultation fee is entered correctly.
                        You can edit the doctor's information later
                        from the Doctor Directory.
                    </div>

                </div>

            </div>

        </div>


    </div>


</div>


</body>

</html>