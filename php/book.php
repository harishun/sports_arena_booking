<?php
// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

include("connect_db.php");
session_start();

$is_logged_in = isset($_SESSION['username']);
$user_details = null;

if ($is_logged_in) {
    $username = $_SESSION['username'];
    $sql_user = "SELECT * FROM users WHERE username='$username'";
    $result_user = mysqli_query($conn, $sql_user);
    $user_details = mysqli_fetch_assoc($result_user);
    $_SESSION['user_id'] = $user_details['id'];
}

$sport_name = htmlspecialchars($_POST["sport"]);
$sql_sport = "SELECT * FROM `sports` WHERE `sport`='$sport_name'";
$result_sport = mysqli_query($conn, $sql_sport);
$sport = mysqli_fetch_assoc($result_sport);
$sport_id = $sport['id'];

$sql_arena = "SELECT * FROM `arenas` WHERE `sport_id`=$sport_id";
$result_arena = mysqli_query($conn, $sql_arena);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book'])) {
    $arena_id = $_POST['arena'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // --- BOOKING VALIDATION ---

    // 1. Check if booking is at least one hour in the future
    $booking_start_datetime_str = $booking_date . ' ' . $start_time;
    $booking_start_datetime = new DateTime($booking_start_datetime_str, new DateTimeZone('Asia/Colombo'));
    $current_datetime = new DateTime('now', new DateTimeZone('Asia/Colombo'));
    
    if ($booking_start_datetime < $current_datetime) {
        echo "<script>alert('Error: You cannot book a time in the past.'); window.history.back();</script>";
        exit;
    }

    $time_difference_seconds = $booking_start_datetime->getTimestamp() - $current_datetime->getTimestamp();
    if ($time_difference_seconds < 3600) { // 3600 seconds = 1 hour
        echo "<script>alert('Error: You must book at least one hour in advance.'); window.history.back();</script>";
        exit;
    }

    // 2. Check for booking conflicts (double booking)
    $check_conflict_sql = "SELECT * FROM bookings 
                           WHERE arena_id = '$arena_id' 
                           AND booking_date = '$booking_date' 
                           AND status = 'confirmed' 
                           AND (('$start_time' < end_time) AND ('$end_time' > start_time))";
    
    $conflict_result = mysqli_query($conn, $check_conflict_sql);
    
    if (mysqli_num_rows($conflict_result) > 0) {
        echo "<script>alert('Error: This time slot is already booked. Please choose a different time.'); window.history.back();</script>";
        exit;
    }

    // --- END OF VALIDATION ---

    if ($is_logged_in) {
        $user_id = $_SESSION['user_id'];
        $insert_booking_sql = "INSERT INTO bookings (user_id, arena_id, booking_date, start_time, end_time, status) 
                               VALUES ('$user_id', '$arena_id', '$booking_date', '$start_time', '$end_time', 'confirmed')";
    } else {
        $guest_name = mysqli_real_escape_string($conn, $_POST['guest_name']);
        $guest_email = mysqli_real_escape_string($conn, $_POST['guest_email']);
        $guest_phone = mysqli_real_escape_string($conn, $_POST['guest_phone']);
        $insert_booking_sql = "INSERT INTO bookings (arena_id, booking_date, start_time, end_time, guest_name, guest_email, guest_phone, status) 
                               VALUES ('$arena_id', '$booking_date', '$start_time', '$end_time', '$guest_name', '$guest_email', '$guest_phone', 'confirmed')";
    }

    if (mysqli_query($conn, $insert_booking_sql)) {
        echo "<script>alert('Booking successful!'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo $sport['sport']; ?></title>
    <link rel="stylesheet" href="../css/book.css">
</head>

<body>
    <a href="../index.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
        </svg></a>
    <div class="card">
        <h1>Book <?php echo $sport['sport']; ?></h1>
        <img class="card_img" src="../<?php echo $sport['file_name']; ?>" alt="<?php echo $sport['sport']; ?>">
        <p><?php echo $sport['description']; ?></p>
        <br>
        <form action="book.php" method="post">
            <input type="hidden" name="sport" value="<?php echo $sport_name; ?>">

            <?php if ($is_logged_in) : ?>
                <h3>Booking As : <?php echo htmlspecialchars($user_details['f_name']) . " " . htmlspecialchars($user_details['l_name']); ?></h3>
            <?php else : ?>
                <label>Full Name: <input type="text" name="guest_name" required></label>
                <label>Email: <input type="email" name="guest_email" required></label>
                <label>Phone Number: <input type="tel" name="guest_phone" required></label>
            <?php endif; ?>

            <label>Select Arena:
                <select name="arena" required>
                    <?php mysqli_data_seek($result_arena, 0); // Reset pointer
                    while ($row = mysqli_fetch_assoc($result_arena)) : ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['arena_name']; ?> (LKR <?php echo $row['hourly_price']; ?>/hr)</option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label>Date: <input type="date" name="booking_date" format="dd-mm-yyyy" required></label>
            <label>Start Time: <input type="time" name="start_time" required></label>
            <label>End Time: <input type="time" name="end_time" required></label>
            <button type="submit" name="book">Book Now</button>
        </form>
    </div>
</body>

</html>