
<?php

require_once "backend/config/db.php";

$full_name = "Test Patient";
$email = "patient@gmail.com";
$password = password_hash("Patient@123", PASSWORD_DEFAULT);
$status = "Active";

$stmt = $conn->prepare(
    "INSERT INTO patients (full_name, email, password, status)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssss",
    $full_name,
    $email,
    $password,
    $status
);

if ($stmt->execute()) {
    echo "Patient account created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>

