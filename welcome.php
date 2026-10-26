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

$total_students = pg_fetch_row(pg_query($conn, "SELECT COUNT(*) FROM users"))[0];

$today_present = pg_fetch_row(pg_query_params($conn,
    "SELECT COUNT(*) FROM attendance WHERE attend_date = $1 AND status = 'Present'",
    array($today)))[0];

$avg = pg_fetch_row(pg_query($conn,
    "SELECT ROUND(AVG(pct)) FROM (
        SELECT COUNT(*) FILTER (WHERE status = 'Present') * 100.0 / COUNT(*) AS pct
        FROM attendance GROUP BY user_id) t"))[0];
$avg_text = ($avg === null) ? "No data yet" : $avg . "%";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="navbar">
    <span class="brand"><i class="fa-solid fa-graduation-cap"></i> My Portal</span>
    <div>
        <a href="welcome.php"><i class="fa-solid fa-house"></i> Home</a>
        <a href="attendance.php"><i class="fa-solid fa-calendar-check"></i> Attendance</a>
        <a href="report.php"><i class="fa-solid fa-chart-bar"></i> Report</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p>Dashboard for <?php echo $today; ?></p>
    <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
        <tr><th><i class="fa-solid fa-users"></i> Total students</th><td><?php echo $total_students; ?></td></tr>
        <tr><th><i class="fa-solid fa-user-check"></i> Present today</th><td><?php echo $today_present; ?></td></tr>
        <tr><th><i class="fa-solid fa-percent"></i> Average attendance</th><td><?php echo $avg_text; ?></td></tr>
    </table>
</div>
</body>
</html>
