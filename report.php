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

$result = pg_query($conn,
    "SELECT u.name,
            COUNT(a.id) AS total_days,
            COUNT(a.id) FILTER (WHERE a.status = 'Present') AS present_days
     FROM users u
     LEFT JOIN attendance a ON a.user_id = u.id
     GROUP BY u.id, u.name
     ORDER BY u.name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card" style="width:600px;">
    <h2>Attendance Report</h2>
    <table border="1" cellpadding="6" style="width:100%; border-collapse:collapse;">
        <tr><th>Name</th><th>Days Present</th><th>Total Days</th><th>Percentage</th></tr>
        <?php while ($row = pg_fetch_assoc($result)) {
            $total = (int)$row['total_days'];
            $present = (int)$row['present_days'];
            $percent = $total > 0 ? round($present / $total * 100) : 0;
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $present; ?></td>
                <td><?php echo $total; ?></td>
                <td><?php echo $percent; ?>%</td>
            </tr>
        <?php } ?>
    </table>
    <p><a href="welcome.php">Back to welcome</a></p>
</div>
</body>
</html>
