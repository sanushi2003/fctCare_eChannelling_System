<?php

session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once "../backend/config/db.php";

    $username =
        trim($_POST["username"] ?? "");

    $password =
        $_POST["password"] ?? "";


    if (
        empty($username) ||
        empty($password)
    ) {

        $error = "Please enter username and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM admins
             WHERE username = ?"
        );

        $stmt->bind_param(
            "s",
            $username
        );

        $stmt->execute();

        $result =
            $stmt->get_result();


        if ($result->num_rows === 1) {

            $admin =
                $result->fetch_assoc();


            if (
                password_verify(
                    $password,
                    $admin["password"]
                )
            ) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] =
                    $admin["id"];

                $_SESSION["admin_username"] =
                    $admin["username"];

                header(
                    "Location: dashboard.php"
                );

                exit;

            } else {

                $error =
                    "Invalid username or password.";

            }

        } else {

            $error =
                "Invalid username or password.";

        }

        $stmt->close();
        $conn->close();
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

    <title>fctCare - Admin Login</title>
    <link rel="icon" type="image/jpeg" href="../logo.jpeg">

    <style>

    :root {
        --primary: #0b7285;
        --primary-dark: #095c6b;
        --primary-soft: #e3f4f7;
        --text: #1f2d3d;
        --muted: #7b8794;
        --border: #d9e2ec;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        color: var(--text);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    /* ================= CARD ================= */

    .al-card {
        width: 100%;
        max-width: 900px;
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        background: rgba(255, 255, 255, 0);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 25px 70px rgba(9, 92, 107, 0.35);
        animation: rise 0.7s ease both;
    }

    @keyframes rise {
        from { opacity: 0; transform: translateY(26px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ================= LEFT PANEL ================= */

    .al-brand {
        position: relative;
        overflow: hidden;
        padding: 48px 38px;
        color: white;
        background: linear-gradient(155deg, var(--primary-dark) 0%, var(--primary) 100%);
        display: flex;
        flex-direction: column;
        min-height: 520px;
    }

    .al-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        animation: float 9s ease-in-out infinite;
    }

    .al-circle.c1 { width: 260px; height: 260px; top: -90px; right: -90px; }
    .al-circle.c2 { width: 180px; height: 180px; bottom: -60px; left: -50px; animation-delay: -3s; }
    .al-circle.c3 { width: 90px;  height: 90px;  top: 52%; right: 30px; animation-delay: -6s; }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-16px); }
    }

    .al-logo {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        
    }

    .al-logo img {
        width: 56px;
        height: 56px;
        object-fit: contain;
        border-radius: 14px;
        background: white;
        padding: 4px;
        
    }

    .al-logo span {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #0a2d63;
        text-decoration: none;
    }

    .al-logo span b {
        color:  #00a897d0;
    }

    .al-hero {
        position: relative;
        z-index: 1;
        margin-top: 60px;
    }

    .al-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        font-size: 12px;
        letter-spacing: 1.2px;
        margin-bottom: 18px;
    }

    .al-hero h2 {
        margin: 0 0 12px;
        font-size: 30px;
        line-height: 1.25;
    }

    .al-hero p {
        margin: 0;
        font-size: 14px;
        line-height: 1.65;
        opacity: 0.88;
    }

    .al-points {
        position: relative;
        z-index: 1;
        list-style: none;
        margin: 32px 0 0;
        padding: 0;
    }

    .al-points li {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
        font-size: 14px;
    }

    .al-points i {
        font-style: normal;
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.16);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    /* ================= RIGHT PANEL ================= */

    .al-form {
        padding: 52px 42px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .al-lock {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 18px;
        box-shadow: 0 10px 22px rgba(11, 114, 133, 0.35);
    }

    .al-form h1 {
        margin: 0 0 6px;
        font-size: 28px;
        color: black;
    }

    .al-sub {
        margin: 0 0 26px;
        color:rgba(5, 5, 5, 0.56) ;
        font-size: 14px;
    }

    .al-error {
        background: #ffe3e3;
        color: #c92a2a;
        border-left: 4px solid #c92a2a;
        padding: 12px 14px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .al-group {
        margin-bottom: 18px;
    }

    .al-group label {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: bold;
        color: #344054;
    }

    .al-field {
        position: relative;
    }

    .al-field .ico {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        font-size: 15px;
        opacity: 0.7;
        pointer-events: none;
    }

    .al-field input {
        width: 100%;
        padding: 13px 46px 13px 42px;
        border: 1px solid var(--border);
        border-radius: 12px;
        font-size: 14px;
        background: #fbfdfe;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }

    .al-field input:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(11, 114, 133, 0.14);
    }

    .al-eye {
        position: absolute;
        top: 50%;
        right: 8px;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 9px;
        background: transparent;
        cursor: pointer;
        font-size: 16px;
        color: rgba(5, 5, 5, 0.56);
    }

    .al-eye:hover {
        background: var(--primary-soft);
    }

    .al-caps {
        display: none;
        margin-top: 6px;
        font-size: 12px;
        color: #e8590c;
    }

    .al-btn {
        width: 100%;
        margin-top: 8px;
        padding: 14px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 0.5px;
        color: white;
        background: linear-gradient(135deg, var(--primary), #1098ad);
        box-shadow: 0 10px 24px rgba(11, 114, 133, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .al-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(11, 114, 133, 0.45);
    }

    .al-btn:active {
        transform: translateY(0);
    }

    .al-note {
        margin-top: 22px;
        text-align: center;
        font-size: 12px;
        color:rgba(5, 5, 5, 0.56) ;
    }

    /* ================= RESPONSIVE ================= */

    @media (max-width: 800px) {

        .al-card {
            grid-template-columns: 1fr;
        }

        .al-brand {
            min-height: auto;
            padding: 30px 26px;
        }

        .al-hero {
            margin-top: 24px;
        }

        .al-points {
            display: none;
        }

        .al-form {
            padding: 34px 26px;
        }
    }

</style>

</head>

<body style="
    background:
        linear-gradient(rgba(255,255,255,0.35), rgba(255,255,255,0.35)),
        url('../bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

    <div class="al-card">


        <!-- ================= LEFT BRAND PANEL ================= -->

        <aside class="al-brand">

            <span class="al-circle c1"></span>
            <span class="al-circle c2"></span>
            <span class="al-circle c3"></span>

            <div class="al-logo">
                <img src="../logo.jpeg" alt="fctCare Logo">
                <span>fct<b>Care</b></span>
            </div>

            <div class="al-hero">

                <span class="al-badge">ADMIN PANEL</span>

                <h2>Manage care, all in one place.</h2>

                <p>
                    Sign in to manage appointments, doctors,
                    hospitals, schedules and patient messages.
                </p>

            </div>

            <ul class="al-points">
                <li><i>📅</i> Appointments &amp; schedules</li>
                <li><i>👨‍⚕️</i> Doctors &amp; hospitals</li>
                <li><i>💬</i> Patient messages</li>
            </ul>

        </aside>


        <!-- ================= RIGHT LOGIN FORM ================= -->

        <section class="al-form">

            <div class="al-lock">🔐</div>

            <h1>Admin Login</h1>

            <p class="al-sub">
                Enter your credentials to access the dashboard.
            </p>

            <?php if (!empty($error)): ?>

                <div class="al-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                action=""
            >

                <div class="al-group">

                    <label>
                        Username
                    </label>

                    <div class="al-field">

                        <span class="ico">👤</span>

                        <input
                            type="text"
                            name="username"
                            placeholder="Enter your username"
                            autocomplete="username"
                            autofocus
                            required
                        >

                    </div>

                </div>


                <div class="al-group">

                    <label>
                        Password
                    </label>

                    <div class="al-field">

                        <span class="ico">🔑</span>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="al-eye"
                            id="eye"
                            aria-label="Show password"
                        >👁</button>

                    </div>

                    <div class="al-caps" id="caps">
                        ⚠ Caps Lock is on
                    </div>

                </div>


                <button
                    type="submit"
                    class="al-btn"
                >
                    Login
                </button>

            </form>

            <div class="al-note">
                🔒 Authorized administrators only
            </div>

        </section>

    </div>


    <script>

        var pw = document.getElementById("password");

        // Show / hide password
        document.getElementById("eye").addEventListener("click", function () {
            pw.type = pw.type === "password" ? "text" : "password";
        });

        // Caps Lock warning
        pw.addEventListener("keyup", function (e) {
            document.getElementById("caps").style.display =
                e.getModifierState && e.getModifierState("CapsLock") ? "block" : "none";
        });

    </script>

</body>

</html>