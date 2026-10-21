<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $district = trim($_POST["district"] ?? "");
    $fee = floatval($_POST["fee"] ?? 0);
    $telephone = trim($_POST["telephone"] ?? "");


    // ================= VALIDATION =================

    if (
        $name === "" ||
        $district === "" ||
        $fee <= 0 ||
        $telephone === ""
    ) {

        $message = "Please fill all fields correctly.";

    } else {

        // ================= INSERT HOSPITAL =================

        $stmt = $conn->prepare(
            "INSERT INTO hospitals
            (name, district, fee, telephone)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssds",
            $name,
            $district,
            $fee,
            $telephone
        );


        if ($stmt->execute()) {

            header("Location: hospitals.php");
            exit;

        } else {

            $message = "Failed to add hospital.";

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

    <title>Add Hospital - fctCare Admin</title>
    <link rel="icon" type="image/jpeg" href="../logo.jpeg">

    <link rel="stylesheet" href="admin.css">

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
                Add New Hospital
            </h1>

        </div>


        <div>

            <a
                href="hospitals.php"
                class="btnb"
            >
                ← Hospital Directory
            </a>

        </div>

    </div>



    <!-- ================= PAGE CONTENT ================= -->

    <div class="page-content">


        <!-- ================= INTRODUCTION ================= -->

        <div style="
            margin-bottom: 25px;
        ">

            <h2 style="
                font-size: 25px;
                color: #1d2939;
                margin-bottom: 7px;
            ">
                Hospital Registration
            </h2>

            <p style="
                color: #667085;
                font-size: 14px;
            ">
                Add a new hospital to the fctCare system.
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
                    🏥
                </div>


                <div>

                    <h3 style="
                        font-size: 18px;
                        color: #1d2939;
                        margin-bottom: 4px;
                    ">
                        Hospital Information
                    </h3>

                    <p style="
                        font-size: 13px;
                        color: #667085;
                    ">
                        Enter the hospital details below.
                    </p>

                </div>

            </div>



            <!-- ================= ERROR MESSAGE ================= -->

            <?php if ($message !== ""): ?>

                <div class="error-message">

                    ⚠️

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>



            <!-- ================= FORM ================= -->

            <form method="POST">


                <!-- Hospital Name -->

                <label for="name">
                    Hospital Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Example: Asiri Hospital"
                    maxlength="150"
                    required
                >

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Enter the full name of the hospital.
                </div>



                <!-- District -->

                <label for="district">
                    District
                </label>

                <input
                    type="text"
                    id="district"
                    name="district"
                    placeholder="Example: Colombo"
                    maxlength="100"
                    required
                >

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Enter the district where the hospital is located.
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
                    Enter the hospital consultation fee in Sri Lankan Rupees.
                </div>



                <!-- Telephone -->

                <label for="telephone">
                    Telephone Number
                </label>

                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    placeholder="Example: 0112345678"
                    maxlength="20"
                    required
                >

                <div style="
                    font-size: 11px;
                    color: #98a2b3;
                    margin-top: 5px;
                ">
                    Enter the hospital contact telephone number.
                </div>



                <!-- ================= BUTTONS ================= -->

                <div class="form-actions">

    <button type="submit" class="btn btn-confirm">
        ✓ Add Hospital
    </button>

    <a href="hospitals.php" class="btn btn-secondary">
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
                        Before adding a hospital
                    </div>


                    <div style="
                        color: #4f6f6c;
                        font-size: 12px;
                        line-height: 1.6;
                    ">
                        Make sure the hospital name and telephone number
                        are correct. The consultation fee should also be
                        entered accurately.
                    </div>

                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>