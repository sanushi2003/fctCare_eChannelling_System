
<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");

require_once __DIR__ . "/backend/config/db.php";

$doctor_id = 1;
$email = "aruni@example.com";
$new_password = "Doctor@123";

$hashed_password = password_hash(
    $new_password,
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare(
    "UPDATE doctor_users
     SET password = ?, status = 'Active'
     WHERE doctor_id = ? AND email = ?"
);

$stmt->bind_param(
    "sis",
    $hashed_password,
    $doctor_id,
    $email
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo "Doctor password reset successfully!";
    } else {
        echo "No account was updated. Check the doctor ID and email.";
    }

} else {

    echo "Error: " . htmlspecialchars($stmt->error);

}

$stmt->close();
$conn->close();

?>
