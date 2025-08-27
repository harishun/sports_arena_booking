<?php
session_start();
include_once 'connect_db.php';

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['logout'])) {
    $new_username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Update user info in DB
    $sql = "UPDATE users SET username='$new_username', e_mail='$email' WHERE username='$username'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['username'] = $new_username; // update session if username changed
        $message = "Profile updated successfully!";
        $username = $new_username;
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch current profile details
$sql = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);
$profile = mysqli_fetch_assoc($result);
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
    <div class="overlay hidden">
        <h2>Edit Profile<svg onclick="toggle_overlay(document.querySelector('.overlay'))" id="close" class="close" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                    <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
                </svg></h2>
        <form method="POST" action="">
            <div class="name cardlet">
                <label>First Name :</label>
                <input type="text" name="f_name" id="f_name" value="<?php echo htmlspecialchars($profile['f_name']); ?>" required>
                <br>

                <label>Last Name :</label>
                <input type="text" name="l_name" id="l_name" value="<?php echo htmlspecialchars($profile['l_name']); ?>" required>
                <br>
            </div>
            <div class="auth cardlet">
                <label>Username :</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>" required>
                <br>
                <div class="p_word_toggle">
                    <label>New Password :</label>
                    <input type="new_password" placeholder="New Password" id="new_password" name="new_password" required>
                    <span onclick="toggle()" id="hide_show"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                            <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
                        </svg></span>
                </div>
                <br>
            </div>
            <div class="contact cardlet">
                <label>Email :</label>
                <br>
                <input type="email" name="email" value="<?php echo htmlspecialchars($profile['e_mail']); ?>" required>
                <br>

                <label>Phone Number :</label>
                <br>
                <input type="text" name="tele" value="<?php echo htmlspecialchars($profile['tele']); ?>" required>
                <br>
            </div>
            <div class="center"><button type="submit"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path
                            d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM480-240q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg></button>
            </div>
        </form>
    </div>
    <div class="card">
        <h2>Profile Details<svg class="edit" onclick="toggle_overlay(document.querySelector('.overlay'))" id="edit" xmlns="http://www.w3.org/2000/svg"
                                height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path
                                    d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z" />
                            </svg></h2>
        <div class="profile-info">
            <p><strong>Full Name :</strong></p>
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

            </tbody>
        </table>
    </div>

    <form method="POST" action="">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
    <form methode=post action="delete_profile.php">
        <button type="submit" name="delete" class="delete-btn">Delete Account</button>
    </form>
    </div>
</body>
</html>