<?php
include("connect_db.php");

// Initialize variables
$f_name = '';
$l_name = '';
$e_mail = '';
$tele = '';
$username = '';
$username_error = '';
$email_error = '';
$tele_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Repopulate form with submitted data
    $f_name = $_POST['f_name'] ?? '';
    $l_name = $_POST['l_name'] ?? '';
    $e_mail = $_POST['e_mail'] ?? '';
    $tele = $_POST['tele'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $can_register = true;

    // 1. Validate International Telephone Number
    $tele_cleaned = preg_replace('/[\s\-()]/', '', $tele); 
    if (!preg_match('/^\+\d{7,15}$/', $tele_cleaned)) {
        $tele_error = "Please enter a valid international phone number (e.g., +94771234567).";
        $can_register = false;
    }

    $username_sanitized = mysqli_real_escape_string($conn, $username);
    $email_sanitized = mysqli_real_escape_string($conn, $e_mail);
    
    // 2. NEW: Check if both username AND email match a single existing user
    $exact_match_sql = "SELECT id FROM users WHERE username = '$username_sanitized' AND e_mail = '$email_sanitized' LIMIT 1";
    $exact_match_result = mysqli_query($conn, $exact_match_sql);
    if (mysqli_num_rows($exact_match_result) > 0) {
        // User with these exact details exists, redirect to login
        header("Location: log_in.php?error=userexists");
        exit;
    }

    // 3. If no exact match, check for individual duplicates (username OR email)
    $check_sql = "SELECT username, e_mail FROM users WHERE username = '$username_sanitized' OR e_mail = '$email_sanitized' LIMIT 1";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['username'] === $username) {
            $username_error = "This username is already taken. Please choose another.";
            $can_register = false;
        }
        if ($row['e_mail'] === $e_mail) {
            $email_error = "This email is already registered. Please choose another.";
            $can_register = false;
        }
    }

    // --- Proceed with registration if all checks pass ---
    if ($can_register) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $f_name_db = mysqli_real_escape_string($conn, $f_name);
        $l_name_db = mysqli_real_escape_string($conn, $l_name);
        $tele_db = mysqli_real_escape_string($conn, $tele);

        $insert_sql = "INSERT INTO users (f_name, l_name, e_mail, tele, username, password_hashed) 
                       VALUES ('$f_name_db', '$l_name_db', '$email_sanitized', '$tele_db', '$username_sanitized', '$password_hashed')";
        
        if (mysqli_query($conn, $insert_sql)) {
            header("Location: log_in.php?registration=success");
            exit;
        } else {
            die("Error creating account: " . mysqli_error($conn));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            <input type="tel" placeholder="+94 00 000 0000" title="Please enter your full phone number with country code" id="tele" name="tele" value="<?php echo htmlspecialchars($tele); ?>" required>
            <?php if (!empty($tele_error)): ?>
                <p class="error-message"><?php echo $tele_error; ?></p>
            <?php endif; ?>
            <br>
            <input type="text" placeholder="Username" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <?php if (!empty($username_error)): ?>
                <p class="error-message"><?php echo $username_error; ?></p>
            <?php endif; ?>
            <br>
            <div class="p_word_toggle">
                <input type="password" placeholder="Password" id="password" name="password" required>
                <span onclick="toggle()" id="hide_show"><svg xmlns="http://www.w.org/2000/svg" height="24" viewBox="0 -960 960 960"><path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/></svg></span>
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