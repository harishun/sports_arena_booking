<?php
include("connect_db.php");

// Initialize variables to hold user input and error messages
$f_name = '';
$l_name = '';
$e_mail = '';
$tele_num = '';
$username = '';
$username_error = '';
$email_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Repopulate form with submitted data to preserve user input on error
    $f_name = $_POST['f_name'] ?? '';
    $l_name = $_POST['l_name'] ?? '';
    $e_mail = $_POST['e_mail'] ?? '';
    $code = $_POST['code'] ?? '';
    $tele_num = $_POST['tele'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $tele = $code . " " . $tele_num;

    // Server-side validation
    if (empty($f_name) || empty($l_name) || empty($e_mail) || empty($tele_num) || empty($username) || empty($password)) {
        // This case is mainly handled by the 'required' attribute in HTML,
        // but server-side validation is a good fallback.
    } else {
        $username_sanitized = mysqli_real_escape_string($conn, $username);
        $email_sanitized = mysqli_real_escape_string($conn, $e_mail);

        // Check if username or email already exists
        $check_sql = "SELECT username, e_mail FROM users WHERE username = '$username_sanitized' OR e_mail = '$email_sanitized' LIMIT 1";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['username'] === $username) {
                // Set the error message for the username field
                $username_error = "Username already exists. Please <a href='log_in.php'>log in</a>.";
            }
            if ($row['e_mail'] === $e_mail) {
                // Set the error message for the email field
                $email_error = "Email already exists. Please <a href='log_in.php'>log in</a>.";
            }
        } else {
            // If no duplicates, proceed with creating the new user
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $f_name_db = mysqli_real_escape_string($conn, $f_name);
            $l_name_db = mysqli_real_escape_string($conn, $l_name);
            $tele_db = mysqli_real_escape_string($conn, $tele);

            $insert_sql = "INSERT INTO users (f_name, l_name, e_mail, tele, username, password_hashed) 
                           VALUES ('$f_name_db', '$l_name_db', '$email_sanitized', '$tele_db', '$username_sanitized', '$password_hashed')";

            if (mysqli_query($conn, $insert_sql)) {
                // Redirect to login page upon successful registration
                header("Location: log_in.php?registration=success");
                exit;
            } else {
                die("Error creating account: " . mysqli_error($conn));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up</title>
    <link rel="stylesheet" href="../css/credential.css">
    <script src="../js/credential.js"></script>
</head>

<body>

    <div class="wrapper">
        <a href="../index.php"><img style="margin-top:10px; width:100px; height:100px;" src="../images/icons/logo.png" alt="logo"></a>
        <h1>Register</h1>
        <br>
        <form action="register.php" method="post" autocomplete="off">
            <input type="text" placeholder="First Name" id="f_name" name="f_name" value="<?php echo htmlspecialchars($f_name); ?>" required>
            <br>
            <input type="text" placeholder="Last Name" id="l_name" name="l_name" value="<?php echo htmlspecialchars($l_name); ?>" required>
            <br>
            <input type="email" placeholder="E-Mail" id="e_mail" name="e_mail" value="<?php echo htmlspecialchars($e_mail); ?>" required>
            <?php if (!empty($email_error)): ?>
                <p class="error-message"><?php echo $email_error; ?></p>
            <?php endif; ?>
            <br>

            <input type="tel" placeholder="xx xxx xxxx" pattern="[0-9]{2} [0-9]{3} [0-9]{4}" id="tele" name="tele" value="<?php echo htmlspecialchars($tele_num); ?>" required>

            <br>
            <input type="text" placeholder="Username" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <?php if (!empty($username_error)): ?>
                <p class="error-message"><?php echo $username_error; ?></p>
            <?php endif; ?>
            <br>
            <div class="p_word_toggle">
                <input type="password" placeholder="Password" id="password" name="password" required>
                <span onclick="toggle()" id="hide_show"><svg xmlns="http://www.w.org/2000/svg" height="24" viewBox="0 -960 960 960">
                        <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
                    </svg></span>
            </div>
            <br>
            <button type="submit">Register</button>
        </form>
        <br>
        <p>Already have an account? <a href="log_in.php">Log In</a></p>
        <br>
    </div>
</body>

</html>