<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$sql = "SELECT * FROM doctors ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Doctors - fctCare Admin</title>

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
                margin-bottom: 10px;
            ">
                fctCare Administration
            </div>

            <h1>
                Doctor Management
            </h1>

        </div>

        <div>

            <a href="dashboard.php" class="btn">
                ← Dashboard
            </a>

            <a href="logout.php" class="btn btn-danger" style="margin-top: 20px;">
                Logout
            </a>

        </div>

    </div>


    <!-- ================= PAGE CONTENT ================= -->

    <div class="page-content">


        <!-- ================= TITLE ================= -->

        <div class="page-title-row">

            <div>

                <h2>
                    Our Doctors
                </h2>

                <p style="
                    margin-top: 8px;
                    color: #667085;
                    font-size: 14px;
                ">
                    Manage doctor profiles, specialties and consultation fees.
                </p>

            </div>


            <a
                href="add_doctor.php"
                class="btn btn-confirm"
                style="
                    padding: 12px 20px;
                    font-size: 14px;
                "
            >
                + Add New Doctor
            </a>

        </div>


        <!-- ================= SUMMARY CARD ================= -->

        <?php

        $doctorCount = 0;

        if ($result) {
            $doctorCount = $result->num_rows;
        }

        ?>

        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        ">

            <div style="
                background: white;
                border-radius: 16px;
                padding: 22px;
                border: 1px solid #e4e7ec;
                box-shadow: 0 8px 25px rgba(15,23,42,0.06);
            ">

                <div style="
                    width: 45px;
                    height: 45px;
                    border-radius: 12px;
                    background: #e8f8f6;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 22px;
                    margin-bottom: 12px;
                ">
                    👨‍⚕️
                </div>

                <div style="
                    color: #667085;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Total Doctors
                </div>

                <div style="
                    font-size: 27px;
                    font-weight: 700;
                    color: #0b8f87;
                ">
                    <?= $doctorCount ?>
                </div>

            </div>


            <div style="
                background: linear-gradient(135deg, #0b8f87, #0da59b);
                border-radius: 16px;
                padding: 22px;
                color: white;
                box-shadow: 0 8px 25px rgba(11,143,135,0.20);
            ">

                <div style="
                    font-size: 13px;
                    opacity: 0.85;
                    margin-bottom: 10px;
                ">
                    Management
                </div>

                <div style="
                    font-size: 20px;
                    font-weight: 700;
                    margin-bottom: 7px;
                ">
                    Doctor Directory
                </div>

                <div style="
                    font-size: 13px;
                    opacity: 0.9;
                ">
                    Add, update and manage doctors.
                </div>

            </div>

        </div>


        <!-- ================= DOCTOR TABLE ================= -->

        <div class="table-container">

            <div style="
                padding: 20px 22px;
                border-bottom: 1px solid #e4e7ec;
                display: flex;
                justify-content: space-between;
                align-items: center;
            ">

                <div>

                    <h3 style="
                        font-size: 17px;
                        color: #1d2939;
                        margin-bottom: 4px;
                    ">
                        Doctor Directory
                    </h3>

                    <p style="
                        font-size: 13px;
                        color: #667085;
                    ">
                        Registered doctors in the fctCare system
                    </p>

                </div>

                <div style="
                    background: #e8f8f6;
                    color: #066b65;
                    padding: 7px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                ">
                    <?= $doctorCount ?> Doctors
                </div>

            </div>


            <div style="overflow-x: auto;">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Doctor Code
                            </th>

                            <th>
                                Doctor
                            </th>

                            <th>
                                Specialty
                            </th>

                            <th>
                                Consultation Fee
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if ($result && $result->num_rows > 0): ?>


                        <?php while ($doctor = $result->fetch_assoc()): ?>


                            <tr>


                                <!-- ID -->

                                <td>

                                    <span style="
                                        background: #f2f4f7;
                                        padding: 6px 10px;
                                        border-radius: 7px;
                                        font-size: 12px;
                                        font-weight: 600;
                                    ">
                                        #<?= htmlspecialchars($doctor["id"]) ?>
                                    </span>

                                </td>


                                <!-- Doctor Code -->

                                <td>

                                    <span style="
                                        display: inline-block;
                                        background: #e8f8f6;
                                        color: #066b65;
                                        padding: 6px 10px;
                                        border-radius: 7px;
                                        font-family: monospace;
                                        font-size: 12px;
                                        font-weight: 700;
                                    ">

                                        <?= htmlspecialchars($doctor["doctor_code"]) ?>

                                    </span>

                                </td>


                                <!-- Doctor Name -->

                                <td>

                                    <div style="
                                        display: flex;
                                        align-items: center;
                                        gap: 11px;
                                    ">

                                        <div style="
                                            width: 40px;
                                            height: 40px;
                                            min-width: 40px;
                                            border-radius: 50%;
                                            background: linear-gradient(
                                                135deg,
                                                #dff7f4,
                                                #bceae6
                                            );
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: #087f78;
                                            font-weight: 700;
                                            font-size: 16px;
                                        ">

                                            <?= strtoupper(
                                                substr($doctor["name"], 0, 1)
                                            ) ?>

                                        </div>


                                        <div>

                                            <div style="
                                                font-weight: 700;
                                                color: #1d2939;
                                            ">

                                                Dr.
                                                <?= htmlspecialchars(
                                                    $doctor["name"]
                                                ) ?>

                                            </div>

                                            <div style="
                                                font-size: 11px;
                                                color: #98a2b3;
                                                margin-top: 2px;
                                            ">

                                                Medical Professional

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- Specialty -->

                                <td>

                                    <span style="
                                        display: inline-block;
                                        background: #f2f4f7;
                                        color: #344054;
                                        padding: 7px 11px;
                                        border-radius: 8px;
                                        font-size: 12px;
                                        font-weight: 600;
                                    ">

                                        <?= htmlspecialchars(
                                            $doctor["specialty"]
                                        ) ?>

                                    </span>

                                </td>


                                <!-- Fee -->

                                <td>

                                    <div style="
                                        font-weight: 700;
                                        color: #157347;
                                        font-size: 14px;
                                    ">

                                        Rs.
                                        <?= number_format(
                                            $doctor["fee"],
                                            2
                                        ) ?>

                                    </div>

                                    <div style="
                                        font-size: 11px;
                                        color: #98a2b3;
                                        margin-top: 2px;
                                    ">

                                        Consultation

                                    </div>

                                </td>


                                <!-- Actions -->

                                <td>

                                    <div style="
                                        display: flex;
                                        gap: 6px;
                                        align-items: center;
                                    ">

                                        <a
                                            href="edit_doctor.php?id=<?= $doctor["id"] ?>"
                                            class="btn btn-confirm"
                                            style="
                                                padding: 8px 12px;
                                                font-size: 12px;
                                            "
                                        >
                                            ✎ Edit
                                        </a>


                                        <a
                                            href="delete_doctor.php?id=<?= $doctor["id"] ?>"
                                            class="btn btn-cancel"
                                            style="
                                                padding: 8px 12px;
                                                font-size: 12px;
                                            "
                                            onclick="return confirm(
                                                'Are you sure you want to delete this doctor?'
                                            );"
                                        >
                                            🗑 Delete
                                        </a>

                                        <a
                                            href="doctor_hospitals.php?doctor_id=<?= $doctor["id"] ?>"
                                            class="btn"
                                            style="
                                                padding: 8px 14px;
                                                font-size: 12px;
                                                background: #e6f7f5;
                                                color: #06756f;
                                            "
                                        >
                                             Hospitals
                                        </a>

                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="6">

                                <div style="
                                    padding: 45px 20px;
                                    text-align: center;
                                ">

                                    <div style="
                                        font-size: 40px;
                                        margin-bottom: 12px;
                                    ">
                                        👨‍⚕️
                                    </div>

                                    <div style="
                                        font-weight: 600;
                                        color: #344054;
                                        margin-bottom: 5px;
                                    ">
                                        No Doctors Found
                                    </div>

                                    <div style="
                                        font-size: 13px;
                                        color: #667085;
                                        margin-bottom: 18px;
                                    ">
                                        No doctors have been added yet.
                                    </div>

                                    <a
                                        href="add_doctor.php"
                                        class="btn btn-confirm"
                                    >
                                        + Add First Doctor
                                    </a>

                                </div>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>


    </div>

</div>


<?php
$conn->close();
?>

</body>

</html>