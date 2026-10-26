<?php
require_once __DIR__ . "/config.php";
session_start();
$conn = pg_connect(DB_CONN);
if (!$conn) {
    die("Database connection failed");
}

if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['pending_user_id'];

// Create a new 6-digit code, valid for 5 minutes
$code = str_pad(random_int(0, 999999), 6, "0", STR_PAD_LEFT);
pg_query_params($conn,
    "INSERT INTO otps (user_id, code, expires_at) VALUES ($1, $2, NOW() + INTERVAL '5 minutes')",
    array($user_id, $code));

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = $_POST['code'];
    $result = pg_query_params($conn,
        "SELECT id FROM otps WHERE user_id = $1 AND code = $2 AND used = FALSE AND expires_at > NOW() ORDER BY id DESC LIMIT 1",
        array($user_id, $entered));
    $row = pg_fetch_assoc($result);

    if ($row) {
        pg_query_params($conn, "UPDATE otps SET used = TRUE WHERE id = $1", array($row['id']));
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $_SESSION['pending_name'];
        unset($_SESSION['pending_user_id'], $_SESSION['pending_name']);
        header("Location: welcome.php");
        exit;
    } else {
        $message = "Wrong or expired code.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OTP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h2>Enter OTP</h2>
    <p>Your code (demo): <strong><?php echo $code; ?></strong></p>
    <p><?php echo $message; ?></p>
    <form method="POST">
        Code: <input type="text" name="code" maxlength="6" required>
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>

