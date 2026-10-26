<?php
require_once __DIR__ . "/config.php";
session_start();
$conn = pg_connect(DB_CONN);
if (!$conn) {
    die("Database connection failed");
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = pg_query_params($conn,
        'SELECT id, name, password FROM users WHERE email = $1',
        array($email));
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // Don't log in yet. Send the user to the OTP step first.
        $_SESSION['pending_user_id'] = $user['id'];
        $_SESSION['pending_name'] = $user['name'];
        header("Location: otp.php");
        exit;
    } else {
        $message = "Wrong email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h2>Login</h2>
    <p><?php echo $message; ?></p>
    <form method="POST">
        Email: <input type="email" name="email" required>
        Password: <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Create an account</a></p>
</div>
</body>
</html>
