<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

$result = $conn->query("SELECT * FROM hospitals ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Management - fctCare Admin</title>

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
                Hospital Management
            </h1>

        </div>


        <div>

            <a href="dashboard.php" class="btn">
                ← Dashboard
            </a>

            <a
                href="logout.php"
                class="btn btn-danger"
                style="margin-left: 8px;"
            >
                Logout
            </a>

        </div>

    </div>



    <!-- ================= PAGE CONTENT ================= -->

    <div class="page-content">


        <!-- ================= TITLE ROW ================= -->

        <div class="page-title-row">

            <div>

                <h2>
                    Hospital Directory
                </h2>

                <p>
                    Manage hospitals available in the fctCare system.
                </p>

            </div>


            <div>

                <a
                    href="add_hospital.php"
                    class="btn btn-confirm"
                >
                    + Add New Hospital
                </a>

            </div>

        </div>



        <!-- ================= HOSPITAL COUNT ================= -->

        <div style="
            margin-bottom: 20px;
            color: #667085;
            font-size: 14px;
        ">

            Total Hospitals:

            <strong>
                <?= $result ? $result->num_rows : 0 ?>
            </strong>

        </div>



        <!-- ================= TABLE ================= -->

        <div class="table-container">

            <?php if ($result && $result->num_rows > 0): ?>

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Hospital
                            </th>

                            <th>
                                District
                            </th>

                            <th>
                                Fee
                            </th>

                            <th>
                                Telephone
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($hospital = $result->fetch_assoc()): ?>

                        <tr>

                            <!-- ID -->

                            <td>
                                <?= htmlspecialchars($hospital["id"]) ?>
                            </td>


                            <!-- Hospital Name -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars($hospital["name"]) ?>
                                </strong>

                            </td>


                            <!-- District -->

                            <td>

                                <?= htmlspecialchars($hospital["district"]) ?>

                            </td>


                            <!-- Fee -->

                            <td>

                                Rs.
                                <?= number_format(
                                    (float)$hospital["fee"],
                                    2
                                ) ?>

                            </td>


                            <!-- Telephone -->

                            <td>

                                <?= htmlspecialchars(
                                    $hospital["telephone"]
                                ) ?>

                            </td>


                            <!-- Actions -->

                            <td>

                                <a
                                    href="edit_hospital.php?id=<?= $hospital["id"] ?>"
                                    class="btn"
                                    style="
                                        padding: 8px 14px;
                                        font-size: 12px;
                                    "
                                >
                                    Edit
                                </a>


                                <a
                                    href="delete_hospital.php?id=<?= $hospital["id"] ?>"
                                    class="btn btn-danger"
                                    style="
                                        padding: 8px 14px;
                                        font-size: 12px;
                                        margin-left: 5px;
                                    "
                                    onclick="return confirm(
                                        'Are you sure you want to delete this hospital?'
                                    );"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>


            <?php else: ?>

                <!-- ================= NO HOSPITALS ================= -->

                <div style="
                    text-align: center;
                    padding: 50px 20px;
                    color: #667085;
                ">

                    <div style="
                        font-size: 45px;
                        margin-bottom: 15px;
                    ">
                        🏥
                    </div>


                    <h3 style="
                        color: #344054;
                        margin-bottom: 8px;
                    ">
                        No Hospitals Found
                    </h3>


                    <p style="
                        font-size: 14px;
                        margin-bottom: 20px;
                    ">
                        No hospitals have been added to the system yet.
                    </p>


                    <a
                        href="add_hospital.php"
                        class="btn btn-confirm"
                    >
                        + Add First Hospital
                    </a>

                </div>

            <?php endif; ?>

        </div>


    </div>

</div>


</body>

</html>

<?php

$conn->close();

?>