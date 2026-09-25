<?php

require_once "config/db.php";

header("Content-Type: application/json");

// ================= CHECK REQUEST =================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


// ================= GET FORM DATA =================

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");


// ================= VALIDATION =================

if ($name === "" || $email === "" || $message === "") {

    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);

    exit;
}


// ================= EMAIL VALIDATION =================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


// ================= INSERT MESSAGE =================

$stmt = $conn->prepare(
    "INSERT INTO messages
        (name, email, message)
     VALUES
        (?, ?, ?)"
);

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $message
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Your message has been sent successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to send your message."
    ]);
}


// ================= CLOSE =================

$stmt->close();
$conn->close();

?>