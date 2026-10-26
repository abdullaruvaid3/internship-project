<?php
require_once __DIR__ . "/config.php";
$conn = pg_connect(DB_CONN);
if (!$conn) {
    die("Database connection failed");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $result = pg_query_params($conn,
        'INSERT INTO users (name, email, password) VALUES ($1, $2, $3)',
        array($name, $email, $password));

    if ($result) {
        echo "Registered successfully! <a href='login.php'>Go to login</a>";
    } else {
        echo "Error: this email may already be registered.";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h2>Register</h2>
    <form method="POST">
        Name: <input type="text" name="name" required>
        Email: <input type="email" name="email" required>
        Password: <input type="password" name="password" required>
        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
