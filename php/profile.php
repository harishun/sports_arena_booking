<?php
session_start();
include_once 'connect_db.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$sql_user = "SELECT * FROM users WHERE username='$username'";
$result_user = mysqli_query($conn, $sql_user);
$profile = mysqli_fetch_assoc($result_user);
$_SESSION['user_id'] = $profile['id'];

$sql_bookings = "SELECT b.booking_date, b.start_time, b.end_time, s.sport, a.arena_name, b.status 
                 FROM bookings b
                 JOIN arenas a ON b.arena_id = a.id
                 JOIN sports s ON a.sport_id = s.id
                 WHERE b.user_id = " . $profile['id'] . " ORDER BY b.booking_date DESC";
$result_bookings = mysqli_query($conn, $sql_bookings);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <script src="../js/credential.js"></script>
    <script src="../js/overlay.js"></script>
</head>

<body>
    <div class="card">
        <h2>Profile Details</h2>
        <div class="profile-info">
            <p><strong>Full Name :</strong> <?php echo htmlspecialchars($profile['f_name']) . " " . htmlspecialchars($profile['l_name']); ?></p>
            <p><strong>Username :</strong> <?php echo htmlspecialchars($profile['username']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($profile['e_mail']); ?></p>
            <p><strong>Phone Number :</strong> <?php echo htmlspecialchars($profile['tele']); ?></p>
            <p><strong>Role :</strong> <?php echo strtoupper(htmlspecialchars($profile["role"])); ?></p>
        </div>
    </div>

    <div class="bookings">
        <h3>My Bookings</h3>
        <table>
            <thead>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Sport</th>
                <th>Arena</th>
                <th>Status</th>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result_bookings) > 0) {
                    while ($row = mysqli_fetch_assoc($result_bookings)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['booking_date']) . "</td>
                                <td>" . htmlspecialchars($row['start_time']) . "</td>
                                <td>" . htmlspecialchars($row['end_time']) . "</td>
                                <td>" . htmlspecialchars($row['sport']) . "</td>
                                <td>" . htmlspecialchars($row['arena_name']) . "</td>
                                <td>" . htmlspecialchars($row['status']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>You have no bookings.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <form method="POST" action="logout.php">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
</body>

</html>