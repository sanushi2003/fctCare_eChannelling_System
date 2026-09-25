
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


// ================================
// COUNT APPOINTMENTS
// ================================

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM appointments"
);

$appointmentCount =
    $result->fetch_assoc()["total"];


// ================================
// COUNT PENDING APPOINTMENTS
// ================================

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM appointments
     WHERE status = 'Pending'"
);

$pendingCount =
    $result->fetch_assoc()["total"];


// ================================
// COUNT DOCTORS
// ================================

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM doctors"
);

$doctorCount =
    $result->fetch_assoc()["total"];


// ================================
// COUNT HOSPITALS
// ================================

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM hospitals"
);

$hospitalCount =
    $result->fetch_assoc()["total"];


// ================================
// COUNT MESSAGES
// ================================

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM messages"
);

$messageCount =
    $result->fetch_assoc()["total"];


// ================================
// COUNT SCHEDULES
// ================================

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM schedules"
);

$scheduleCount =
    $result->fetch_assoc()["total"];


$conn->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>fctCare Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>

<body>

    <div class="admin-container">


        <!-- ================================
             SIDEBAR
        ================================= -->

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


            <!-- SCHEDULES LINK -->

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


        <!-- ================================
             MAIN CONTENT
        ================================= -->

        <main class="main-content">


            <div class="top-bar">

                <div>

                    <h1>Dashboard</h1>

                    <p>
                        Welcome,
                        <?= htmlspecialchars(
                            $_SESSION["admin_username"] ?? "Admin"
                        ) ?>
                    </p>

                </div>

            </div>


            <!-- ================================
                 STATISTICS
            ================================= -->

            <div class="stats-grid">


                <div class="stat-card">

                    <h3>Total Appointments</h3>

                    <p>
                        <?= $appointmentCount ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>Pending Appointments</h3>

                    <p>
                        <?= $pendingCount ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>Doctors</h3>

                    <p>
                        <?= $doctorCount ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>Hospitals</h3>

                    <p>
                        <?= $hospitalCount ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>Schedules</h3>

                    <p>
                        <?= $scheduleCount ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>Messages</h3>

                    <p>
                        <?= $messageCount ?>
                    </p>

                </div>


            </div>


            <!-- ================================
                 QUICK ACTIONS
            ================================= -->

            <div class="dashboard-section">

                <h2>Quick Actions</h2>


                <div class="quick-actions">


                    <a href="appointments.php">
                        View Appointments
                    </a>


                    <a href="messages.php">
                        View Messages
                    </a>


                    <a href="doctors.php">
                        Manage Doctors
                    </a>


                    <a href="hospitals.php">
                        Manage Hospitals
                    </a>


                    <!-- SCHEDULE MANAGEMENT -->

                    <a href="schedules.php">
                        Manage Schedules
                    </a>


                </div>

            </div>


        </main>

    </div>

</body>

</html>

