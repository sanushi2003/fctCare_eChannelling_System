<?php

/*
|--------------------------------------------------------------------------
| SECURE SESSION CONFIGURATION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    ini_set(
        "session.cookie_httponly",
        "1"
    );

    ini_set(
        "session.cookie_samesite",
        "Lax"
    );

    session_start();
}


/*
|--------------------------------------------------------------------------
| SESSION TIMEOUT
|--------------------------------------------------------------------------
*/

$session_timeout = 1800; // 30 minutes


if (
    isset($_SESSION["last_activity"]) &&
    (time() - $_SESSION["last_activity"]) > $session_timeout
) {

    $_SESSION = [];

    session_destroy();

    header("Location: ../login.php?timeout=1");

    exit;
}


$_SESSION["last_activity"] = time();


/*
|--------------------------------------------------------------------------
| SECURITY FUNCTIONS
|--------------------------------------------------------------------------
*/

function requirePatient()
{
    if (
        !isset($_SESSION["user_type"]) ||
        $_SESSION["user_type"] !== "patient"
    ) {

        header("Location: ../login.php");

        exit;
    }
}


function requireDoctor()
{
    if (
        !isset($_SESSION["user_type"]) ||
        $_SESSION["user_type"] !== "doctor"
    ) {

        header("Location: ../login.php");

        exit;
    }
}


function requireAdmin()
{
    if (
        !isset($_SESSION["user_type"]) ||
        $_SESSION["user_type"] !== "admin"
    ) {

        header("Location: login.php");

        exit;
    }
}

?>