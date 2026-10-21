<?php

require_once "backend/config/db.php";
require_once "backend/config/session.php";

$error = "";


/*
|--------------------------------------------------------------------------
| REDIRECT IF ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["user_type"])) {

    $redirect = $_GET["redirect"] ?? "";

    if (
        $_SESSION["user_type"] === "patient" &&
        $redirect === "appointment.php"
    ) {
        header("Location: appointment.php");
        exit;
    }

    if ($_SESSION["user_type"] === "patient") {
        header("Location: patient/dashboard.php");
        exit;
    }

    if ($_SESSION["user_type"] === "doctor") {
        header("Location: doctor/dashboard.php");
        exit;
    }

    if ($_SESSION["user_type"] === "admin") {
        header("Location: admin/dashboard.php");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email =
        trim($_POST["email"] ?? "");

    $password =
        $_POST["password"] ?? "";

    $role =
        $_POST["role"] ?? "";


    if (
        empty($email) ||
        empty($password) ||
        empty($role)
    ) {

        $error =
            "Please fill all fields.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | PATIENT LOGIN
        |--------------------------------------------------------------------------
        */

        if ($role === "patient") {

            $stmt = $conn->prepare(
                "SELECT
                    id,
                    full_name,
                    email,
                    password,
                    status
                 FROM patients
                 WHERE email = ?
                 LIMIT 1"
            );

            $stmt->bind_param(
                "s",
                $email
            );

            $stmt->execute();

            $result =
                $stmt->get_result();


            if ($result->num_rows === 1) {

                $user =
                    $result->fetch_assoc();


                if (
                    $user["status"] !== "Active"
                ) {

                    $error =
                        "Your account is inactive.";

                } elseif (
                    password_verify(
                        $password,
                        $user["password"]
                    )
                ) {

                    session_regenerate_id(true);

                    $_SESSION["user_type"] =
                        "patient";

                    $_SESSION["patient_id"] =
                        $user["id"];

                    $_SESSION["patient_name"] =
                        $user["full_name"];

                    $_SESSION["patient_email"] =
                        $user["email"];

                    $_SESSION["last_activity"] =
                        time();


                    $redirect = $_GET["redirect"] ?? "";

                    if ($redirect === "appointment.php") {
                        header("Location: appointment.php");
                    } else {
                        header("Location: patient/dashboard.php");
                    }

                    exit;

                } else {

                    $error =
                        "Invalid email or password.";
                }

            } else {

                $error =
                    "Invalid email or password.";
            }

            $stmt->close();
        }


        /*
        |--------------------------------------------------------------------------
        | DOCTOR LOGIN
        |--------------------------------------------------------------------------
        */

        elseif ($role === "doctor") {

            $stmt = $conn->prepare(
                "SELECT
                    du.id,
                    du.doctor_id,
                    du.email,
                    du.password,
                    du.status,
                    d.name
                 FROM doctor_users du
                 INNER JOIN doctors d
                    ON du.doctor_id = d.id
                 WHERE du.email = ?
                 LIMIT 1"
            );

            $stmt->bind_param(
                "s",
                $email
            );

            $stmt->execute();

            $result =
                $stmt->get_result();


            if ($result->num_rows === 1) {

                $doctor =
                    $result->fetch_assoc();


                if (
                    $doctor["status"] !== "Active"
                ) {

                    $error =
                        "Doctor account is inactive.";

                } elseif (
                    password_verify(
                        $password,
                        $doctor["password"]
                    )
                ) {

                    session_regenerate_id(true);

                    $_SESSION["user_type"] =
                        "doctor";

                    $_SESSION["doctor_user_id"] =
                        $doctor["id"];

                    $_SESSION["doctor_id"] =
                        $doctor["doctor_id"];

                    $_SESSION["doctor_name"] =
                        $doctor["name"];

                    $_SESSION["doctor_email"] =
                        $doctor["email"];

                    $_SESSION["last_activity"] =
                        time();


                    header(
                        "Location: doctor/dashboard.php"
                    );

                    exit;

                } else {

                    $error =
                        "Invalid email or password.";
                }

            } else {

                $error =
                    "Invalid email or password.";
            }

            $stmt->close();
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN LOGIN
        |--------------------------------------------------------------------------
        */

        elseif ($role === "admin") {

            $stmt = $conn->prepare(
                "SELECT
                    id,
                    username,
                    password
                 FROM admins
                 WHERE username = ?
                 LIMIT 1"
            );

            $stmt->bind_param(
                "s",
                $email
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

                    $_SESSION["user_type"] =
                        "admin";

                    $_SESSION["admin_id"] =
                        $admin["id"];

                    $_SESSION["admin_username"] =
                        $admin["username"];

                    $_SESSION["last_activity"] =
                        time();


                    header(
                        "Location: admin/dashboard.php"
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
        }
    }
}

$selectedRole = $_POST["role"] ?? "patient";
if (!in_array($selectedRole, ["patient", "doctor", "admin"], true)) {
    $selectedRole = "patient";
}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>fctCare - Login</title>
<link rel="icon" type="image/jpeg" href="logo.jpeg">

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
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
    color: var(--text);
    background: #eef7f9;
}


/* =====================================================
   PAGE + BACKGROUND DECORATION
===================================================== */

.page {
    position: relative;
    flex: 1 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: clamp(40px, 6vw, 110px);
    padding: 48px 5vw;
    overflow: hidden;
}

/* soft dotted pattern that fades out toward the edges */
.page::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(11,114,133,0.20) 1.2px, transparent 1.5px);
    background-size: 26px 26px;
    -webkit-mask-image: radial-gradient(circle at 50% 50%, #000 25%, transparent 78%);
            mask-image: radial-gradient(circle at 50% 50%, #000 25%, transparent 78%);
    pointer-events: none;
}

/* floating colour orbs */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    pointer-events: none;
    animation: drift 14s ease-in-out infinite alternate;
}

.orb-1 {
    width: 380px;
    height: 380px;
    background: #17a2b8;
    top: -130px;
    left: -110px;
    opacity: .50;
}

.orb-2 {
    width: 330px;
    height: 330px;
    background: #00a897;
    bottom: -120px;
    right: -70px;
    opacity: .38;
    animation-delay: -5s;
}

.orb-3 {
    width: 230px;
    height: 230px;
    background: #0b7285;
    top: 52%;
    left: 44%;
    opacity: .22;
    animation-delay: -9s;
}

@keyframes drift {
    to { transform: translate(46px, -34px) scale(1.14); }
}


/* =====================================================
   RIGHT : BRAND PANEL
===================================================== */

.visual {
    position: relative;
    order: 2;
    flex: 1 1 auto;
    width: min(46%, 560px);
    max-width: 560px;
    min-height: 420px;
    display: flex;
    align-items: center;
    z-index: 1;
}

/* tilted glass sheet behind the panel */
.visual::before {
    content: "";
    position: absolute;
    inset: 10px -12px -12px 10px;
    border: 1px solid rgba(255,255,255,0.40);
    border-radius: 30px;
    background: rgba(255,255,255,0.10);
    backdrop-filter: blur(3px);
    transform: rotate(2.2deg);
}

/* plus badge with pulse ring */
.visual::after {
    content: "✚";
    position: absolute;
    width: 72px;
    height: 72px;
    right: 22px;
    top: -26px;
    display: grid;
    place-items: center;
    border-radius: 22px;
    color: #fff;
    font-size: 34px;
    background: linear-gradient(135deg, var(--primary), #1098ad);
    box-shadow: 0 14px 32px rgba(11,114,133,0.38);
    animation: badgePulse 2.8s ease-out infinite;
    z-index: 3;
}

@keyframes badgePulse {
    0%   { box-shadow: 0 14px 32px rgba(11,114,133,0.38), 0 0 0 0 rgba(23,162,184,0.55); }
    70%  { box-shadow: 0 14px 32px rgba(11,114,133,0.38), 0 0 0 22px rgba(23,162,184,0); }
    100% { box-shadow: 0 14px 32px rgba(11,114,133,0.38), 0 0 0 0 rgba(23,162,184,0); }
}

.visual-content {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 40px 38px 34px;
    color: #19859be3;
    border-radius: 28px;
    border: 1px solid rgba(255,255,255,0.55);
    background: linear-gradient(145deg,
        rgba(255,255,255,0.42),
        rgba(255,255,255,0.16));
    box-shadow: 0 24px 65px rgba(5,40,50,0.22);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    animation: fadeUp .8s .15s ease both;
}

.brand {
    display: flex;
    align-items: center;
    gap: 0;
    font-size: 25px;
    font-weight: 700;
    letter-spacing: .5px;
    color: #0a2d63;
    text-decoration: none;
    margin-bottom: 22px;
}

.brand span {
    color: #00a897d0;
}

.logo {
    width: 50px;
    height: 50px;
    margin-right: 10px;
    border-radius: 14px;
    object-fit: cover;
    box-shadow: 0 8px 22px rgba(5,40,50,0.20);
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    margin-bottom: 16px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .6px;
    text-transform: uppercase;
    color: var(--primary);
    background: rgba(255,255,255,0.55);
    border: 1px solid rgba(11,114,133,0.20);
}

.eyebrow::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00a897;
    animation: blink 1.6s ease-in-out infinite;
}

@keyframes blink {
    50% { opacity: .25; transform: scale(.7); }
}

.visual h1 {
    margin: 0 0 14px;
    font-size: 42px;
    line-height: 1.12;
    background: linear-gradient(90deg, #0a2d63 0%, #0b7285 55%, #00a897 100%);
    -webkit-background-clip: text;
            background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
}

.visual p {
    margin: 0 0 22px;
    font-size: 16px;
    line-height: 1.6;
    color: rgba(5, 5, 5, 0.56);
}

/* animated heartbeat line */
.ecg {
    display: block;
    width: 100%;
    height: 54px;
    margin: 0 0 24px;
    overflow: visible;
}

.ecg path {
    fill: none;
    stroke-width: 2.6;
    stroke-linecap: round;
    stroke-linejoin: round;
    vector-effect: non-scaling-stroke;
}

.ecg .ecg-base {
    stroke: rgba(11,114,133,0.20);
}

.ecg .ecg-pulse {
    stroke: #17a2b8;
    stroke-dasharray: 28 72;
    stroke-dashoffset: 100;
    filter: drop-shadow(0 0 5px rgba(23,162,184,0.8));
    animation: beat 2.8s linear infinite;
}

@keyframes beat {
    to { stroke-dashoffset: 0; }
}

.features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.features span {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 8px 14px;
    border-radius: 18px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    color: var(--primary);
    background: rgba(255,255,255,0.45);
    border: 1px solid rgba(255,255,255,0.65);
    backdrop-filter: blur(6px);
    box-shadow: 0 8px 20px rgba(5,40,50,0.08);
    animation: floaty 5s ease-in-out infinite;
    transition: transform .25s, box-shadow .25s;
}

.features span:nth-child(2) { animation-delay: -1.6s; }
.features span:nth-child(3) { animation-delay: -3.2s; }

.features span:hover {
    box-shadow: 0 14px 28px rgba(11,114,133,0.20);
}

.features i {
    font-style: normal;
    font-size: 24px;
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    background: var(--primary-soft);
}

@keyframes floaty {
    50% { transform: translateY(-6px); }
}


/* =====================================================
   LEFT : LOGIN CARD
===================================================== */

.form-side {
    position: relative;
    order: 1;
    width: 460px;
    max-width: 100%;
    flex: 0 0 460px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 38px 34px 34px;
    background: rgba(255, 255, 255, 0.28);
    border: 1px solid rgba(255, 255, 255, 0.60);
    border-radius: 24px;
    box-shadow: 0 18px 55px rgba(5, 40, 50, 0.20);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    overflow: hidden;
    z-index: 3;
    animation: cardAppear 0.65s ease both;
}

/* colour strip along the top of the card */
.form-side::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #0b7285, #17a2b8, #00a897);
}

.login-box {
    width: 100%;
    max-width: 392px;
    margin: 0 auto;
}

/* staggered entrance */
.login-box > * {
    animation: fadeUp .6s ease both;
}

.login-box > :nth-child(1) { animation-delay: .10s; }
.login-box > :nth-child(2) { animation-delay: .18s; }
.login-box > :nth-child(3) { animation-delay: .26s; }
.login-box > :nth-child(4) { animation-delay: .34s; }
.login-box > :nth-child(5) { animation-delay: .42s; }
.login-box > :nth-child(6) { animation-delay: .50s; }

@keyframes cardAppear {
    from { opacity: 0; transform: translateY(18px) scale(.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* brand shown inside the card on phones only */
.mobile-brand {
    display: none;
    align-items: center;
    margin-bottom: 18px;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: .5px;
    color: #0a2d63;
}

.mobile-brand span {
    color: #00a897d0;
}

.mobile-brand img {
    width: 42px;
    height: 42px;
    margin-right: 10px;
    border-radius: 12px;
    object-fit: cover;
}

.login-box h2 {
    margin: 0 0 6px;
    font-size: 30px;
}

.login-box h2 .wave {
    display: inline-block;
    transform-origin: 70% 80%;
    animation: wave 2.6s ease-in-out infinite;
}

@keyframes wave {
    0%, 60%, 100% { transform: rotate(0); }
    10%, 30% { transform: rotate(16deg); }
    20%, 40% { transform: rotate(-10deg); }
    50% { transform: rotate(8deg); }
}

.subtitle {
    margin: 0 0 26px;
    color: rgba(5, 5, 5, 0.56);
}


/* ---------- ROLE SWITCH (sliding pill) ---------- */

.roles {
    --g: 6px;
    --p: 5px;
    position: relative;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--g);
    padding: var(--p);
    background: var(--primary-soft);
    border-radius: 14px;
    margin-bottom: 24px;
    box-shadow: inset 0 2px 6px rgba(11,114,133,0.10);
}

.roles input {
    display: none;
}

.roles label {
    position: relative;
    z-index: 1;
    margin: 0;
    padding: 10px 0;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: var(--primary);
    border-radius: 10px;
    cursor: pointer;
    transition: color .25s, background .2s;
}

.roles label:hover {
    background: rgba(11,114,133,0.10);
}

.roles input:checked + label {
    background: var(--primary);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(11,114,133,0.35);
}

.roles input:checked + label:hover {
    background: var(--primary-dark);
}

/* modern browsers: one pill slides between the three options */
@supports selector(:has(*)) {

    .roles::before {
        content: "";
        position: absolute;
        top: var(--p);
        bottom: var(--p);
        left: var(--p);
        width: calc((100% - 2 * var(--p) - 2 * var(--g)) / 3);
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), #1098ad);
        box-shadow: 0 6px 16px rgba(11,114,133,0.38);
        transition: transform .4s cubic-bezier(.34, 1.4, .5, 1);
    }

    .roles:has(#r-doctor:checked)::before {
        transform: translateX(calc(100% + var(--g)));
    }

    .roles:has(#r-admin:checked)::before {
        transform: translateX(calc(200% + 2 * var(--g)));
    }

    .roles input:checked + label,
    .roles input:checked + label:hover {
        background: transparent;
        box-shadow: none;
        color: #ffffff;
    }
}


/* ---------- INPUTS ---------- */

.form-group {
    margin-bottom: 18px;
}

.form-group > label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 7px;
}

.input-wrap {
    position: relative;
}

.input-wrap .ico {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    opacity: .55;
    pointer-events: none;
    transition: opacity .2s, transform .25s;
}

.input-wrap:focus-within .ico {
    opacity: 1;
    transform: translateY(-50%) scale(1.18);
}

.input-wrap input {
    width: 100%;
    padding: 13px 14px 13px 42px;
    border: 1.5px solid rgba(217, 226, 236, 0.92);
    border-radius: 12px;
    font-size: 15px;
    outline: none;
    transition: .2s;
    background: rgba(255,255,255,0.92);
    color: var(--text);
}

.input-wrap input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(11,114,133,0.12), 0 8px 18px rgba(11,114,133,0.10);
    transform: translateY(-1px);
}

.toggle-pw {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: auto;
    padding: 6px 10px;
    border: none;
    background: none;
    color: var(--primary);
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
}

.toggle-pw:hover {
    background: var(--primary-soft);
}


/* ---------- BUTTON ---------- */

.submit {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--primary), #1098ad);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    letter-spacing: .2px;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(11,114,133,0.30);
    transition: .2s;
}

/* light sweep on hover */
.submit::after {
    content: "";
    position: absolute;
    top: 0;
    left: -80%;
    width: 50%;
    height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,0.38), transparent);
    transform: skewX(-20deg);
    transition: left .7s;
}

.submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 26px rgba(11,114,133,0.42);
}

.submit:hover::after {
    left: 140%;
}

.submit:active {
    transform: translateY(0);
}

.submit .arrow {
    transition: transform .25s;
}

.submit:hover .arrow {
    transform: translateX(5px);
}


/* ---------- MESSAGES + LINKS ---------- */

.error {
    background: #ffe3e3;
    color: #c92a2a;
    padding: 12px 14px;
    border-radius: 12px;
    border-left: 4px solid #c92a2a;
    margin-bottom: 20px;
    font-size: 14px;
    animation: shake .45s ease both;
}

@keyframes shake {
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}

.register {
    text-align: center;
    margin-top: 26px;
    padding-top: 20px;
    border-top: 1px dashed rgba(11,114,133,0.28);
    color: rgba(5, 5, 5, 0.56);
    font-size: 14px;
}

.register a {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    text-underline-offset: 3px;
}

.register a:hover {
    text-decoration: underline;
}


/* =====================================================
   RESPONSIVE
===================================================== */

/* tablets / small laptops: hide the brand panel, centre the card */
@media (max-width: 1000px) {

    .visual {
        display: none;
    }
}

@media (max-width: 768px) {

    body {
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .page {
        width: 100%;
        padding: 25px 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: url("bb.avif");
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }

    .orb-3 {
        display: none;
    }

    .mobile-brand {
        display: flex;
    }

    /* Login card */
    .form-side {
        position: relative;
        width: 100%;
        max-width: 430px;
        min-width: 0;
        flex: 1 1 auto;
        margin: 0 auto;
        padding: 32px 24px 28px;
        background: rgba(255, 255, 255, 0.28);
        border: 1px solid rgba(255, 255, 255, 0.60);
        border-radius: 20px;
        box-shadow: 0 18px 45px rgba(5, 40, 50, 0.20);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        box-sizing: border-box;
    }

    .login-box {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }

    .login-box h2 {
        font-size: 27px;
        line-height: 1.3;
        margin-bottom: 8px;
    }

    .subtitle {
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 24px;
    }

    .roles {
        --g: 5px;
        --p: 5px;
        margin-bottom: 22px;
    }

    .roles label {
        min-width: 0;
        padding: 11px 2px;
        font-size: 13px;
        white-space: nowrap;
    }

    .form-group {
        margin-bottom: 17px;
    }

    .input-wrap {
        width: 100%;
    }

    .input-wrap input {
        display: block;
        width: 100%;
        min-width: 0;
        height: 46px;
        padding: 12px 58px 12px 40px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .input-wrap .ico {
        left: 12px;
    }

    .toggle-pw {
        right: 5px;
        padding: 6px 8px;
        font-size: 12px;
    }

    .submit {
        width: 100%;
        min-height: 46px;
        padding: 13px;
        font-size: 16px;
        margin-top: 3px;
    }

    .error {
        font-size: 13px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .register {
        margin-top: 22px;
        padding-top: 18px;
        font-size: 13px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }
}


/* small phones */
@media (max-width: 380px) {

    .page {
        padding: 18px 10px;
    }

    .form-side {
        padding: 26px 16px 22px;
        border-radius: 17px;
    }

    .login-box h2 {
        font-size: 24px;
    }

    .subtitle {
        font-size: 13px;
    }

    .roles {
        --g: 3px;
        --p: 4px;
    }

    .roles label {
        padding: 10px 1px;
        font-size: 12px;
    }

    .input-wrap input {
        padding-left: 36px;
        padding-right: 52px;
    }

    .input-wrap .ico {
        left: 10px;
    }

    .register {
        font-size: 12px;
    }
}


/* landscape phones */
@media (max-width: 768px) and (max-height: 500px) {

    .page {
        align-items: flex-start;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .form-side {
        margin-top: 0;
        margin-bottom: 0;
    }
}


/* =====================================================
   FOOTER
===================================================== */

.footer {
    position: relative;
    z-index: 2;
    background-color: #0a2d63;
    color: #ffffff;
    text-align: center;
    padding: 32px;
    margin-top: 0;
    border-top: 4px solid #00a896;
}

.footer_text {
    font-style: italic;
    opacity: 0.8;
    margin-top: 24px;
    font-size: 14px;
}

/* Footer contact details */
.footer-contact {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 75px;
    margin-bottom: 20px;
    text-align: left;
}

.footer-contact .contact-item h4 {
    margin: 0 0 6px 0;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.8;
}

.footer-contact .contact-item span,
.footer-contact .contact-item address {
    font-weight: 600;
    font-style: normal;
}

.footer-contact .contact-item a {
    color: inherit;
    text-decoration: none;
    font-weight: 600;
}

.footer-contact .contact-item a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {

    .footer {
        padding: 26px 18px;
    }

    .footer-contact {
        gap: 22px;
        text-align: center;
        flex-direction: column;
        align-items: center;
    }
}


/* respect "reduce motion" */
@media (prefers-reduced-motion: reduce) {

    *, *::before, *::after {
        animation: none !important;
        transition: none !important;
    }
}


 /* ===== Advanced footer layout (layout only - colors come from style.css) ===== */
                

        .footer-top {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1.2fr 1.4fr;
            gap: 32px;
            text-align: left;
            padding-bottom: 28px;
            margin-bottom: 20px;
            border-bottom: 1px solid color-mix(in srgb, currentColor 25%, transparent);
        }
        .footer-top h4 {
            margin: 0 0 12px 0;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }
        .footer-brand .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            color: inherit;
            margin-bottom: 12px;
        }
        .footer-brand .footer-logo img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
        }
        .footer-brand p {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.6;
            opacity: 0.85;
        }
        .footer-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .footer-links a {
            text-decoration: none;
            color: inherit;
            opacity: 0.85;
            transition: opacity 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }
        .footer-links a:hover {
            opacity: 1;
            transform: translateX(4px);
        }
        .footer-col .contact-item {
            margin-bottom: 14px;
        }
        .footer-col .contact-item h4 {
            margin-bottom: 4px;
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 600;
        }
        .footer-col .contact-item a,
        .footer-col .contact-item span,
        .footer-col .contact-item address {
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.88rem;
        }
        .footer-bottom p {
            margin: 0;
        }
        .footer-bottom .back-to-top {
            text-decoration: none;
            color: inherit;
            opacity: 0.85;
            border: 1px solid color-mix(in srgb, currentColor 35%, transparent);
            padding: 4px 14px;
            border-radius: 20px;
            transition: opacity 0.2s ease;
        }
        .footer-bottom .back-to-top:hover {
            opacity: 1;
        }

        @media (max-width: 992px) {
            .footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 560px) {
            .footer-top { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { justify-content: center; text-align: center; }
        }

</style>

</head>

<body style="
    background:
        linear-gradient(rgba(255,255,255,0.35), rgba(255,255,255,0.35)),
        url('bb.avif') center center / cover no-repeat fixed;
    min-height:100vh;
">

<div class="page">

    <!-- background decoration -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- RIGHT : BRAND PANEL -->
    <div class="visual">
        <div class="visual-content">

            <div class="brand">
                <img src="logo.jpeg" alt="fctCare Logo" class="logo">
                fct<span>Care</span>
            </div>

            <div class="eyebrow">Secure Healthcare Login</div>

            <h1>Your health,<br>our priority.</h1>

            <p>
                Book appointments, connect with doctors and manage
                your healthcare, all in one secure place.
            </p>

            <svg class="ecg" viewBox="0 0 400 54" preserveAspectRatio="none" aria-hidden="true">
                <path class="ecg-base" pathLength="100"
                      d="M0 30 H110 L124 30 L138 8 L156 50 L172 14 L184 30 H400"/>
                <path class="ecg-pulse" pathLength="100"
                      d="M0 30 H110 L124 30 L138 8 L156 50 L172 14 L184 30 H400"/>
            </svg>

            <div class="features">
                <span><i>🩺</i>Expert Doctors</span>
                <span><i>📅</i>Easy Booking</span>
                <span><i>🔒</i>Secure Access</span>
            </div>

        </div>
    </div>


    <!-- LEFT : LOGIN -->
    <div class="form-side">

        <div class="login-box">

            <div class="mobile-brand">
                <img src="logo.jpeg" alt="fctCare Logo">
                fct<span>Care</span>
            </div>

            <h2>Welcome Back <span class="wave">👋</span></h2>
            <p class="subtitle">Sign in to continue to fctCare</p>

            <?php if ($error): ?>
                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="roles">

                    <input type="radio" name="role" id="r-patient" value="patient"
                        <?= $selectedRole === "patient" ? "checked" : "" ?>>
                    <label for="r-patient">Patient</label>

                    <input type="radio" name="role" id="r-doctor" value="doctor"
                        <?= $selectedRole === "doctor" ? "checked" : "" ?>>
                    <label for="r-doctor">Doctor</label>

                    <input type="radio" name="role" id="r-admin" value="admin"
                        <?= $selectedRole === "admin" ? "checked" : "" ?>>
                    <label for="r-admin">Admin</label>

                </div>

                <div class="form-group">
                    <label id="idLabel" for="email">Email</label>
                    <div class="input-wrap">
                        <span class="ico">👤</span>
                        <input
                            type="text"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="ico">🔑</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                        <button type="button" class="toggle-pw" id="togglePw">Show</button>
                    </div>
                </div>

                <button type="submit" class="submit">
                    Login <span class="arrow">→</span>
                </button>

            </form>

            <div class="register">
                Don't have a patient account?
                <a href="patient/register.php">Create Patient Account</a>
            </div>

        </div>

    </div>

</div>

<footer class="footer">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="index.php" class="footer-logo">
                    <img src="logo.jpeg" alt="fctCare Logo">
                    <span>fctCare</span>
                </a>
                <p>Sri Lanka's e-channeling framework. Book appointments with top doctors and leading hospitals from the comfort of your home.</p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="doctors.php">Doctors</a></li>
                    <li><a href="hospitals.php">Hospitals</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
                    <li><a href="contact.php">Contact</a></li>
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

const idLabel = document.getElementById("idLabel");
const idInput = document.getElementById("email");

function updateIdField() {
    const role = document.querySelector('input[name="role"]:checked').value;
    if (role === "admin") {
        idLabel.textContent = "Username";
        idInput.placeholder = "Enter your username";
    } else {
        idLabel.textContent = "Email";
        idInput.placeholder = "Enter your email";
    }
}

document.querySelectorAll('input[name="role"]').forEach(function (r) {
    r.addEventListener("change", updateIdField);
});
updateIdField();

document.getElementById("togglePw").addEventListener("click", function () {
    const pw = document.getElementById("password");
    const show = pw.type === "password";
    pw.type = show ? "text" : "password";
    this.textContent = show ? "Hide" : "Show";
});

</script>

</body>

</html>