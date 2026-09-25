<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


$id = intval($_GET["id"] ?? 0);


if ($id <= 0) {

    header("Location: schedules.php");
    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM schedules
     WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    header("Location: schedules.php");
    exit;

} else {

    echo "
        <script>
            alert('Failed to delete schedule.');
            window.location.href = 'schedules.php';
        </script>
    ";

}


$stmt->close();

$conn->close();

?>