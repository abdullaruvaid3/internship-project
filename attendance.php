<?php
require_once __DIR__ . "/config.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$conn = pg_connect(DB_CONN);
if (!$conn) {
    die("Database connection failed");
}

$today = date('Y-m-d');
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    pg_query_params($conn,
        "INSERT INTO attendance (user_id, attend_date, status) VALUES ($1, $2, $3)
         ON CONFLICT (user_id, attend_date) DO UPDATE SET status = EXCLUDED.status",
        array($_SESSION['user_id'], $today, $status));
    $message = "Attendance saved as " . htmlspecialchars($status) . ".";
}

$records = pg_query_params($conn,
    "SELECT attend_date, status FROM attendance WHERE user_id = $1 ORDER BY attend_date DESC",
    array($_SESSION['user_id']));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <span class="brand">My Portal</span>
    <div>
        <a href="welcome.php">Home</a>
        <a href="attendance.php">Attendance</a>
        <a href="report.php">Report</a>
        <a href="logout.php">Logout</a>
    </div>
</div>
<div class="card">
    <h2>Attendance</h2>
    <?php if ($message) { ?>
        <p class="msg-success"><?php echo $message; ?></p>
    <?php } ?>
    <p>Today (<?php echo $today; ?>):</p>
    <form method="POST">
        <select name="status">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>
        <button type="submit">Save</button>
    </form>
    <h3>Records</h3>
    <table border="1" cellpadding="6" style="width:100%; border-collapse:collapse;">
        <tr><th>Date</th><th>Status</th></tr>
        <?php while ($row = pg_fetch_assoc($records)) { ?>
            <tr><td><?php echo $row['attend_date']; ?></td><td><?php echo htmlspecialchars($row['status']); ?></td></tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
