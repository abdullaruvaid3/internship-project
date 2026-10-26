<?php
require_once __DIR__ . "/config.php"; $conn = new mysqli("localhost", MYSQL_USER, MYSQL_PASS, MYSQL_DB);if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO students (name, email, phone) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $_POST['name'], $_POST['email'], $_POST['phone']);

if ($stmt->execute()) {
    echo "Data saved successfully! <a href='index.html'>Go back</a>";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
