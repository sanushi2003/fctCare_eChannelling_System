<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

date_default_timezone_set("Asia/Colombo");


// ================================
// HELPER: SINGLE COUNT QUERY
// ================================

function getCount($conn, $sql)
{
    $result = $conn->query($sql);

    if (!$result) {
        return 0;
    }

    return (int) ($result->fetch_assoc()["total"] ?? 0);
}


// ================================
// STATISTICS
// ================================

$appointmentCount = getCount($conn, "SELECT COUNT(*) AS total FROM appointments");
$pendingCount     = getCount($conn, "SELECT COUNT(*) AS total FROM appointments WHERE status = 'Pending'");
$confirmedCount   = getCount($conn, "SELECT COUNT(*) AS total FROM appointments WHERE status = 'Confirmed'");
$cancelledCount   = getCount($conn, "SELECT COUNT(*) AS total FROM appointments WHERE status = 'Cancelled'");
$doctorCount      = getCount($conn, "SELECT COUNT(*) AS total FROM doctors");
$hospitalCount    = getCount($conn, "SELECT COUNT(*) AS total FROM hospitals");
$messageCount     = getCount($conn, "SELECT COUNT(*) AS total FROM messages");
$scheduleCount    = getCount($conn, "SELECT COUNT(*) AS total FROM schedules");

$otherCount = max(0, $appointmentCount - $pendingCount - $confirmedCount - $cancelledCount);

function pct($part, $total)
{
    return $total > 0 ? round(($part / $total) * 100, 1) : 0;
}

$conn->close();

$hour = (int) date("G");
$greeting = $hour < 12 ? "Good morning" : ($hour < 17 ? "Good afternoon" : "Good evening");
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

    <title>fctCare Admin Dashboard</title>
    <link rel="icon" type="image/jpeg" href="logo.jpeg">

    <link
        rel="stylesheet"
        href="admin.css"
    >

    <!-- ================================
         DASHBOARD ENHANCEMENTS
         (layout / shape only – colours come from admin.css)
    ================================= -->
    <style>
        :root {
            --brand: currentColor;           /* replaced at runtime from your existing theme */
            --glass: rgba(255, 255, 255, 0.78);
            --glass-border: rgba(255, 255, 255, 0.9);
            --ink-soft: rgba(0, 0, 0, 0.55);
            --line: rgba(0, 0, 0, 0.08);
            --radius: 18px;
            --shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 14px 36px rgba(0, 0, 0, 0.14);
        }

        .main-content { animation: fadeUp .5s ease both; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: none; }
        }

        /* ---------- Top bar ---------- */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px 26px;
            margin-bottom: 24px;
        }

        .top-bar h1 { margin: 0 0 4px; }
        .top-bar p  { margin: 0; color: var(--ink-soft); }

        .top-tools {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 8px 16px;
            min-width: 260px;
            transition: box-shadow .2s, border-color .2s;
        }

        .search-box:focus-within {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand) 18%, transparent);
        }

        .search-box svg { width: 18px; height: 18px; flex: none; opacity: .55; }

        .search-box input {
            border: 0;
            outline: 0;
            background: transparent;
            font: inherit;
            width: 100%;
        }

        .date-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: color-mix(in srgb, var(--brand) 12%, #fff);
            border-radius: 999px;
            padding: 9px 16px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        .date-chip svg { width: 16px; height: 16px; }

        /* ---------- Statistics ---------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 20px;
            margin-bottom: 26px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 22px 22px 20px;
            transition: transform .25s, box-shadow .25s;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
            background: var(--brand);
            opacity: .85;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: color-mix(in srgb, var(--brand) 14%, #fff);
            margin-bottom: 6px;
        }

        .stat-icon svg { width: 22px; height: 22px; }

        .stat-card h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            opacity: .75;
        }

        .stat-card p {
            margin: 0;
            font-size: 34px;
            font-weight: 700;
            line-height: 1.1;
        }

        .stat-card small { color: var(--ink-soft); font-size: 12.5px; }

        /* ---------- Two column panels ---------- */
        .panel-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 22px;
            margin-bottom: 26px;
        }

        .panel,
        .dashboard-section {
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px 26px;
        }

        .panel h2,
        .dashboard-section h2 {
            margin: 0 0 18px;
            font-size: 18px;
        }

        /* status overview */
        .status-bar {
            display: flex;
            height: 14px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--line);
            margin-bottom: 20px;
        }

        .status-bar span { display: block; height: 100%; background: var(--brand); }
        .status-bar .s-pending   { opacity: 1; }
        .status-bar .s-confirmed { opacity: .65; }
        .status-bar .s-cancelled { opacity: .35; }
        .status-bar .s-other     { opacity: .15; }

        .status-list { display: grid; gap: 12px; }

        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
        }

        .status-row .label { display: flex; align-items: center; gap: 10px; font-weight: 600; }

        .dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            background: var(--brand);
        }

        .dot.s-pending   { opacity: 1; }
        .dot.s-confirmed { opacity: .65; }
        .dot.s-cancelled { opacity: .35; }
        .dot.s-other     { opacity: .15; }

        .status-row .value { font-weight: 700; }
        .status-row .value small { font-weight: 500; color: var(--ink-soft); margin-left: 6px; }

        /* ---------- Quick actions ---------- */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .quick-actions a {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            background: linear-gradient(
                145deg,
                #ffffff 0%,
                color-mix(in srgb, var(--brand) 8%, #fff) 100%
            );
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px 18px 16px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            transition: transform .25s, box-shadow .25s, border-color .25s, background .25s;
        }

        /* same accent bar as the stat cards */
        .quick-actions a::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
            background: var(--brand);
            opacity: .85;
        }

        /* soft glow circle in the corner */
        .quick-actions a::after {
            content: "";
            position: absolute;
            right: -26px; top: -26px;
            width: 90px; height: 90px;
            border-radius: 50%;
            background: color-mix(in srgb, var(--brand) 12%, transparent);
            transition: transform .35s;
        }

        .quick-actions a:hover {
            transform: translateY(-5px);
            border-color: var(--brand);
            box-shadow: var(--shadow-hover);
            background: linear-gradient(
                145deg,
                color-mix(in srgb, var(--brand) 6%, #fff) 0%,
                color-mix(in srgb, var(--brand) 16%, #fff) 100%
            );
        }

        .quick-actions a:hover::after { transform: scale(1.8); }

        .quick-actions .qa-emoji {
            position: relative;
            z-index: 1;
            width: 50px;
            height: 50px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 26px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
            transition: transform .3s;
        }

        .quick-actions a:hover .qa-emoji { transform: scale(1.12) rotate(-6deg); }

        .quick-actions .qa-text { position: relative; z-index: 1; }

        .quick-actions .qa-text small {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            font-weight: 500;
            color: var(--ink-soft);
        }

        .quick-actions .qa-go {
            position: relative;
            z-index: 1;
            margin-top: auto;
            font-size: 13px;
            font-weight: 600;
            opacity: .6;
            transition: opacity .2s, transform .2s;
        }

        .quick-actions a:hover .qa-go { opacity: 1; transform: translateX(4px); }

        @media (max-width: 420px) {
            .quick-actions { grid-template-columns: 1fr; }
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 980px) {
            .panel-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .search-box { min-width: 0; width: 100%; }
            .top-tools  { width: 100%; }
        }
    </style>

</head>

<body style="
    background:
        linear-gradient(rgba(255,255,255,0.35), rgba(255,255,255,0.35)),
        url('../bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

    <!-- SVG icon sprite -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
        <symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-doctor" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M5 21v-2a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v2M12 14v4M10 16h4"/></symbol>
        <symbol id="i-hospital" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14M9 21v-4h6v4M12 9v4M10 11h4"/></symbol>
        <symbol id="i-schedule" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M8 15l2.5 2.5L16 13"/></symbol>
        <symbol id="i-message" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></symbol>
        <symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></symbol>
    </svg>

    <div class="admin-container">


        <!-- ================================
             SIDEBAR
        ================================= -->

        <aside class="sidebar">
        <div class="sidebar-brand">

          <img src="logo.jpeg" alt="fctCare Logo" class="logo"> 
            fct<span>Care</span>
        </div>
            <p class="admin-label">
                Admin Panel
            </p>


    <a href="dashboard.php"    class="<?= $current === 'dashboard.php'    ? 'active' : '' ?>">Dashboard</a>
    <a href="appointments.php" class="<?= $current === 'appointments.php' ? 'active' : '' ?>">Appointments</a>
    <a href="messages.php"     class="<?= $current === 'messages.php'     ? 'active' : '' ?>">Messages</a>
    <a href="doctors.php"      class="<?= $current === 'doctors.php'      ? 'active' : '' ?>">Doctors</a>
    <a href="hospitals.php"    class="<?= $current === 'hospitals.php'    ? 'active' : '' ?>">Hospitals</a>
    <a href="schedules.php"    class="<?= $current === 'schedules.php'    ? 'active' : '' ?>">Schedules</a>

    <a href="logout.php" class="logout">Logout</a>

        </aside>


        <!-- ================================
             MAIN CONTENT
        ================================= -->

        <main class="main-content">


            <div class="top-bar">

                <div>

                    <h1>Dashboard</h1>

                    <p>
                        <?= $greeting ?>,
                        <?= htmlspecialchars(
                            $_SESSION["admin_username"] ?? "Admin"
                        ) ?>
                        &mdash; here's what's happening today.
                    </p>

                </div>

                <div class="top-tools">

                    <form class="search-box" action="appointments.php" method="get">
                        <svg><use href="#i-search"/></svg>
                        <input
                            type="text"
                            name="search"
                            placeholder="Search appointments, patients..."
                            aria-label="Search appointments"
                        >
                    </form>

                    <div class="date-chip">
                        <svg><use href="#i-calendar"/></svg>
                        <?= date("D, d M Y") ?>
                    </div>

                </div>

            </div>


            <!-- ================================
                 STATISTICS
            ================================= -->

            <div class="stats-grid">


                <div class="stat-card">

                    <div class="stat-icon"><svg><use href="#i-calendar"/></svg></div>

                    <h3>Total Appointments</h3>

                    <p>
                        <?= $appointmentCount ?>
                    </p>

                    <small>All booked channelings</small>

                </div>


                <div class="stat-card">

                    <div class="stat-icon"><svg><use href="#i-clock"/></svg></div>

                    <h3>Pending Appointments</h3>

                    <p>
                        <?= $pendingCount ?>
                    </p>

                    <small>Waiting for confirmation</small>

                </div>


                <div class="stat-card">

                    <div class="stat-icon"><svg><use href="#i-doctor"/></svg></div>

                    <h3>Doctors</h3>

                    <p>
                        <?= $doctorCount ?>
                    </p>

                    <small>Registered specialists</small>

                </div>


                <div class="stat-card">

                    <div class="stat-icon"><svg><use href="#i-hospital"/></svg></div>

                    <h3>Hospitals</h3>

                    <p>
                        <?= $hospitalCount ?>
                    </p>

                    <small>Partner hospitals</small>

                </div>


                <div class="stat-card">

                    <div class="stat-icon"><svg><use href="#i-schedule"/></svg></div>

                    <h3>Schedules</h3>

                    <p>
                        <?= $scheduleCount ?>
                    </p>

                    <small>Doctor time slots</small>

                </div>


                <div class="stat-card">

                    <div class="stat-icon"><svg><use href="#i-message"/></svg></div>

                    <h3>Messages</h3>

                    <p>
                        <?= $messageCount ?>
                    </p>

                    <small>Patient enquiries</small>

                </div>


            </div>


            <!-- ================================
                 APPOINTMENT STATUS OVERVIEW
            ================================= -->

            <div class="panel-grid">

                <div class="panel">

                    <h2>Appointment Status Overview</h2>

                    <div class="status-bar" role="img" aria-label="Appointment status breakdown">
                        <span class="s-pending"   style="width: <?= pct($pendingCount, $appointmentCount) ?>%"></span>
                        <span class="s-confirmed" style="width: <?= pct($confirmedCount, $appointmentCount) ?>%"></span>
                        <span class="s-cancelled" style="width: <?= pct($cancelledCount, $appointmentCount) ?>%"></span>
                        <span class="s-other"     style="width: <?= pct($otherCount, $appointmentCount) ?>%"></span>
                    </div>

                    <div class="status-list">

                        <div class="status-row">
                            <span class="label"><i class="dot s-pending"></i> Pending</span>
                            <span class="value"><?= $pendingCount ?><small><?= pct($pendingCount, $appointmentCount) ?>%</small></span>
                        </div>

                        <div class="status-row">
                            <span class="label"><i class="dot s-confirmed"></i> Confirmed</span>
                            <span class="value"><?= $confirmedCount ?><small><?= pct($confirmedCount, $appointmentCount) ?>%</small></span>
                        </div>

                        <div class="status-row">
                            <span class="label"><i class="dot s-cancelled"></i> Cancelled</span>
                            <span class="value"><?= $cancelledCount ?><small><?= pct($cancelledCount, $appointmentCount) ?>%</small></span>
                        </div>

                        <?php if ($otherCount > 0): ?>
                        <div class="status-row">
                            <span class="label"><i class="dot s-other"></i> Other</span>
                            <span class="value"><?= $otherCount ?><small><?= pct($otherCount, $appointmentCount) ?>%</small></span>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>


                <!-- ================================
                     QUICK ACTIONS
                ================================= -->

                <div class="dashboard-section">

                    <h2>Quick Actions</h2>


                    <div class="quick-actions">


                        <a href="appointments.php">
                            <span class="qa-emoji">📅</span>
                            <span class="qa-text">
                                View Appointments
                                <small>Confirm &amp; track bookings</small>
                            </span>
                            <span class="qa-go">Open →</span>
                        </a>


                        <a href="messages.php">
                            <span class="qa-emoji">💬</span>
                            <span class="qa-text">
                                View Messages
                                <small>Reply to patient enquiries</small>
                            </span>
                            <span class="qa-go">Open →</span>
                        </a>


                        <a href="doctors.php">
                            <span class="qa-emoji">👨‍⚕️</span>
                            <span class="qa-text">
                                Manage Doctors
                                <small>Add, edit &amp; remove</small>
                            </span>
                            <span class="qa-go">Open →</span>
                        </a>


                        <a href="hospitals.php">
                            <span class="qa-emoji">🏥</span>
                            <span class="qa-text">
                                Manage Hospitals
                                <small>Partner hospital list</small>
                            </span>
                            <span class="qa-go">Open →</span>
                        </a>


                        <!-- SCHEDULE MANAGEMENT -->

                        <a href="schedules.php">
                            <span class="qa-emoji">🗓️</span>
                            <span class="qa-text">
                                Manage Schedules
                                <small>Doctor time slots</small>
                            </span>
                            <span class="qa-go">Open →</span>
                        </a>


                    </div>

                </div>

            </div>


        </main>

    </div>

<footer class="footer">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="../index.php" class="footer-logo">
                    <img src="../logo.jpeg" alt="fctCare Logo">
                    <span>fctCare</span>
                </a>
                <p>Sri Lanka's e-channeling framework. Book appointments with top doctors and leading hospitals from the comfort of your home.</p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../about.php">About</a></li>
                    <li><a href="../doctors.php">Doctors</a></li>
                    <li><a href="../hospitals.php">Hospitals</a></li>
                    <li><a href="../appointment.php">Appointment</a></li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col footer-contact">
                <div class="contact-item">
                    <h4>Phone Number</h4>
                    <a href="tel:+94112345678">+94 11 234 5678</a>
                </div>
                <div class="contact-item">
                    <h4>Email Address</h4>
                    <a href="mailto:info@fctcare.com">info@fctcare.com</a>
                </div>
                <div class="contact-item">
                    <h4>eChannelling Hotline Operating Hours</h4>
                    <span>7:00 AM – 9:00 PM</span>
                </div>
            </div>

            <div class="footer-col footer-contact">
                <div class="contact-item">
                    <h4>Address</h4>
                    <address style="font-style: normal;">
                        eChannelling PLC, No: 108,<br>
                        W A D Ramanayake Mawatha,<br>
                        Colombo 2, Sri Lanka
                    </address>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 fctCare. Book. Channel. Care.</p>
            <p class="footer_text">FCT • KELANIYA</p>
            <a href="#" class="back-to-top" aria-label="Back to top">↑ Back to top</a>
        </div>
    </footer>

    

    <script>
        // Pick up the accent colour already used by your theme (admin.css)
        // so the new accents match exactly without hard-coding any colour.
        (function () {
            function usable(c) {
                return c && c !== "transparent" && c !== "rgba(0, 0, 0, 0)";
            }

            var candidates = [
                [".sidebar", "backgroundColor"],
                [".quick-actions a", "color"],
                [".stat-card p", "color"],
                [".top-bar h1", "color"]
            ];

            for (var i = 0; i < candidates.length; i++) {
                var el = document.querySelector(candidates[i][0]);
                if (!el) continue;
                var c = getComputedStyle(el)[candidates[i][1]];
                if (usable(c)) {
                    document.documentElement.style.setProperty("--brand", c);
                    break;
                }
            }
        })();
    </script>

</body>

</html>