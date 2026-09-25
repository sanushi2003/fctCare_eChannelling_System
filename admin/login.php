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

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <h1>fctCare</h1>

            <h2>Admin Login</h2>

            <?php if (!empty($error)): ?>

                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                action=""
            >

                <div class="form-group">

                    <label>
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="login-btn"
                >
                    Login
                </button>

            </form>

        </div>

    </div>

</body>

</html>