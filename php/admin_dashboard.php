<?php
// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
include("connect_db.php");

// Check if the user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// --- DATA CALCULATIONS ---

// 1. Get total number of registered users
$sql_users = "SELECT COUNT(id) AS total_users FROM users WHERE role = 'user'";
$result_users = mysqli_query($conn, $sql_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

// 2. Get total number of bookings
$sql_total_bookings = "SELECT COUNT(id) AS total_bookings FROM bookings";
$result_total_bookings = mysqli_query($conn, $sql_total_bookings);
$total_bookings = mysqli_fetch_assoc($result_total_bookings)['total_bookings'];

// 3. Get number of bookings for today
$today = date("Y-m-d");
$sql_today_bookings = "SELECT COUNT(id) AS today_bookings FROM bookings WHERE booking_date = '$today'";
$result_today_bookings = mysqli_query($conn, $sql_today_bookings);
$today_bookings = mysqli_fetch_assoc($result_today_bookings)['today_bookings'];

// 4. Calculate total profit from all bookings
$sql_profit = "SELECT SUM(
                    TIMESTAMPDIFF(HOUR, b.start_time, b.end_time) * a.hourly_price
                ) AS total_profit
                FROM bookings b
                JOIN arenas a ON b.arena_id = a.id";
$result_profit = mysqli_query($conn, $sql_profit);
$total_profit = mysqli_fetch_assoc($result_profit)['total_profit'];
// Format as currency, defaulting to 0 if null
$total_profit = $total_profit ? number_format($total_profit, 2) : '0.00';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>UOC Sports — Dashboard</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>

<body>
    <a class="logo" href="admin_home.php"><img src="../../images/icons/logo.png" alt="logo" height="auto" width="75px"></a>
    <nav>
        <li><a href="admin_home.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg></a></li>
        <li><a href="admin_dashboard.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-120v-80l80-80v160h-80Zm160 0v-240l80-80v320h-80Zm160 0v-320l80 81v239h-80Zm160 0v-239l80-80v319h-80Zm160 0v-400l80-80v480h-80ZM120-327v-113l280-280 160 160 280-280v113L560-447 400-607 120-327Z"/></svg></a></li>
        <li><a href="profile.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z"/></svg></a></li>
    </nav>

    <!-- SUMMARY DASHBOARD -->
    <div class="summary-container">
        <div class="summary-card">
            <h3>Registered Users</h3>
            <p class="summary-number"><?php echo $total_users; ?></p>
        </div>
        <div class="summary-card">
            <h3>Total Bookings</h3>
            <p class="summary-number"><?php echo $total_bookings; ?></p>
        </div>
        <div class="summary-card">
            <h3>Bookings Today</h3>
            <p class="summary-number"><?php echo $today_bookings; ?></p>
        </div>
        <div class="summary-card">
            <h3>Total Profit</h3>
            <p class="summary-number">LKR <?php echo $total_profit; ?></p>
        </div>
    </div>
</body>
</html>