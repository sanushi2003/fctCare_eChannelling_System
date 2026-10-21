```php
<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");

require_once __DIR__ . "/backend/config/db.php";

$patient_id = 1;
$new_password = "Patient@123";

$hashed_password = password_hash(
    $new_password,
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare(
    "UPDATE patients
     SET password = ?, status = 'Active'
     WHERE id = ?"
);

$stmt->bind_param("si", $hashed_password, $patient_id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo "Patient password reset successfully!";
    } else {
        echo "No changes made. Check whether patient ID 1 exists and the password is already set.";
    }

} else {
    echo "Error: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();

?>
```
