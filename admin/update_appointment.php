<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: appointments.php");
    exit;
}

$appointment_id = intval($_POST["appointment_id"] ?? 0);
$status = $_POST["status"] ?? "";

$allowed_statuses = [
    "Pending",
    "Confirmed",
    "Completed",
    "Cancelled"
];

if ($appointment_id <= 0 || !in_array($status, $allowed_statuses)) {
    header("Location: appointments.php");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE appointments
     SET status = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "si",
    $status,
    $appointment_id
);

$stmt->execute();

$stmt->close();
$conn->close();

header("Location: appointments.php");
exit;

?>