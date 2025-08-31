<?php
// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

include("connect_db.php");
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Auto-update ALL booking statuses to 'completed'
$update_status_sql = "UPDATE bookings SET status = 'completed' WHERE CONCAT(booking_date, ' ', end_time) < NOW() AND status = 'confirmed'";
mysqli_query($conn, $update_status_sql);

// Fetch all bookings with user/guest details
$sql_all_bookings = "SELECT 
                        b.booking_date, 
                        b.start_time, 
                        b.end_time, 
                        b.status,
                        COALESCE(CONCAT(u.f_name, ' ', u.l_name), b.guest_name) AS full_name,
                        COALESCE(u.tele, b.guest_phone) AS telephone
                     FROM bookings b
                     LEFT JOIN users u ON b.user_id = u.id
                     ORDER BY b.booking_date DESC, b.start_time ASC";
$result_all_bookings = mysqli_query($conn, $sql_all_bookings);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="../../css/list.css">
</head>

<body>
    <!-- BOOKINGS -->
    <div class="card" id="bookings">
        <h2>Bookings<a href="admin_home.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg"
                    height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                    <path
                        d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
                </svg></a></h2>
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Full Name</th>
                        <th>Telephone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_all_bookings) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_all_bookings)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                <td><?php echo htmlspecialchars(date("g:i a", strtotime($row['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date("g:i a", strtotime($row['end_time']))); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>