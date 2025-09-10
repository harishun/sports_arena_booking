<?php
// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

include("connect_db.php");
session_start();

// Set timezone for all date/time operations
date_default_timezone_set('Asia/Colombo');

$is_logged_in = isset($_SESSION['username']);
$user_details = null;
$sport_found = false;
$error_message = '';

if ($is_logged_in) {
    $username = $_SESSION['username'];
    $sql_user = "SELECT * FROM users WHERE username='$username'";
    $result_user = mysqli_query($conn, $sql_user);
    $user_details = mysqli_fetch_assoc($result_user);
    $_SESSION['user_id'] = $user_details['id'];
}

// --- GRACEFUL SPORT HANDLING ---
// Determine sport name from POST (form submit) or GET (direct link)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sport"])) {
    $sport_name = htmlspecialchars($_POST["sport"]);
} else if (isset($_GET['sport'])) {
    $sport_name = htmlspecialchars($_GET['sport']);
} else {
    $sport_name = null;
    $error_message = "No sport was selected. Please choose a sport from the homepage to begin booking.";
}

// Only proceed if a sport name exists
if ($sport_name) {
    $sql_sport = "SELECT * FROM `sports` WHERE `sport`='$sport_name'";
    $result_sport = mysqli_query($conn, $sql_sport);

    if ($result_sport && mysqli_num_rows($result_sport) > 0) {
        $sport_found = true;
        $sport = mysqli_fetch_assoc($result_sport);
        $sport_id = $sport['id'];
        $sql_arena = "SELECT * FROM `arenas` WHERE `sport_id`=$sport_id";
        $result_arena = mysqli_query($conn, $sql_arena);
    } else {
        $error_message = "The selected sport ('" . htmlspecialchars($sport_name) . "') was not found.";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book'])) {
    $arena_id = $_POST['arena'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // --- BOOKING VALIDATION ---

    // 1. **NEW**: Fetch arena details to check active hours
    $sql_arena_details = "SELECT open_time, close_time FROM arenas WHERE id = '$arena_id'";
    $result_arena_details = mysqli_query($conn, $sql_arena_details);
    $arena_details = mysqli_fetch_assoc($result_arena_details);
    $opening_time = $arena_details['open_time'];
    $closing_time = $arena_details['close_time'];

    // 2. **NEW**: Check if booking is within active hours
    if ($start_time < $opening_time || $end_time > $closing_time) {
        $opening_formatted = date("g:i A", strtotime($opening_time));
        $closing_formatted = date("g:i A", strtotime($closing_time));
        echo "<script>alert('Error: Booking is outside of active hours. Please book between $opening_formatted and $closing_formatted.'); window.location.href='book.php?sport=" . urlencode($sport_name) . "';</script>";
        exit;
    }

    // 3. Check if booking is at least one hour in the future
    $booking_start_datetime_str = $booking_date . ' ' . $start_time;
    $booking_start_datetime = new DateTime($booking_start_datetime_str);
    $current_datetime = new DateTime('now');
    
    if ($booking_start_datetime < $current_datetime) {
        echo "<script>alert('Error: You cannot book a time in the past.'); window.location.href='book.php?sport=" . urlencode($sport_name) . "';</script>";
        exit;
    }

    $time_difference_seconds = $booking_start_datetime->getTimestamp() - $current_datetime->getTimestamp();
    if ($time_difference_seconds < 3600) { // 3600 seconds = 1 hour
        echo "<script>alert('Error: You must book at least one hour in advance.'); window.location.href='book.php?sport=" . urlencode($sport_name) . "';</script>";
        exit;
    }

    // 4. Check for booking conflicts (double booking)
    $check_conflict_sql = "SELECT * FROM bookings 
                           WHERE arena_id = '$arena_id' 
                           AND booking_date = '$booking_date' 
                           AND status = 'confirmed' 
                           AND (('$start_time' < end_time) AND ('$end_time' > start_time))";
    
    $conflict_result = mysqli_query($conn, $check_conflict_sql);
    
    if (mysqli_num_rows($conflict_result) > 0) {
        echo "<script>alert('Error: This time slot is already booked. Please choose a different time.'); window.location.href='book.php?sport=" . urlencode($sport_name) . "';</script>";
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
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Sport</title>
    <link rel="stylesheet" href="../css/book.css">
</head>
<body>
    <a href="../index.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
        </svg></a>
    <div class="card">
        <?php if ($sport_found): ?>
            <h1>Book <?php echo htmlspecialchars($sport['sport']); ?></h1>
            <img class="card_img" src="../<?php echo htmlspecialchars($sport['file_name']); ?>" alt="<?php echo htmlspecialchars($sport['sport']); ?>">
            <p><?php echo htmlspecialchars($sport['description']); ?></p>
            <br>
            <form action="book.php" method="post">
                <input type="hidden" name="sport" value="<?php echo htmlspecialchars($sport_name); ?>">

                <?php if ($is_logged_in) : ?>
                    <h3>Booking As : <?php echo htmlspecialchars($user_details['f_name']) . " " . htmlspecialchars($user_details['l_name']); ?></h3>
                <?php else : ?>
                    <label>Full Name: <input type="text" name="guest_name" required></label>
                    <label>Email: <input type="email" name="guest_email" required></label>
                    <label>Phone Number: <input type="tel" name="guest_phone" required></label>
                <?php endif; ?>

                <label>Select Arena:
                    <select name="arena" required>
                        <?php 
                        if ($result_arena) {
                            mysqli_data_seek($result_arena, 0); // Reset pointer
                            while ($row = mysqli_fetch_assoc($result_arena)) : ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['arena_name']); ?> (LKR <?php echo htmlspecialchars($row['hourly_price']); ?>/hr)</option>
                        <?php 
                            endwhile;
                        } 
                        ?>
                    </select>
                </label>
                <label>Date: <input type="date" name="booking_date" min="<?php echo date('Y-m-d'); ?>" required></label>
                <label>Start Time: <input type="time" name="start_time" required></label>
                <label>End Time: <input type="time" name="end_time" required></label>
                <button type="submit" name="book">Book Now</button>
            </form>
        <?php else: ?>
            <h1>Error</h1>
            <p><?php echo $error_message; ?></p>
            <a href="../index.php" style="display: inline-block; margin-top: 1rem; text-decoration: none; background: #333; color: white; padding: 10px 15px; border-radius: 5px;">Return to Homepage</a>
        <?php endif; ?>
    </div>
</body>
</html>