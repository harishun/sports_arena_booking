<?php
include("connect_db.php");
session_start(); // Start the session at the top

// Initialize variables to store error messages and user input
$username_err = '';
$password_err = '';
$username = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get username and password from form and keep the username for redisplay
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Validate inputs
    if (empty($username)) {
        $username_err = 'Please enter a username.';
    }
    if (empty($password)) {
        $password_err = 'Please enter a password.';
    }

    // If there are no validation errors, proceed to check credentials
    if (empty($username_err) && empty($password_err)) {
        
        // --- Using Prepared Statements (More Secure) ---
        $sql = "SELECT id, username, password_hashed, role FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            
                            // Store data in session variables
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;
                            $_SESSION['role'] = $role;
                            
                            // Redirect user based on role
                            if ($role == 'admin') {
                                header("Location: ../php/admin_home.php");
                                exit;
                            } else {
                                header("Location: ../index.php");
                                exit;
                            }
                        } else {
                            // Password is not valid, display a generic error message
                            $password_err = "The password you entered is incorrect.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $username_err = "This username does not exist. Please register.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-In</title>
    <link rel="stylesheet" href="../css/credential.css">
    <script src="../js/credential.js"></script>
    <style>
        /* Add this style for the error messages */
        .error-message {
            color: #ff4747; /* A reddish color for errors */
            font-size: 0.9em;
            margin-top: 5px;
            display: block; /* Makes it appear on its own line */
        }
    </style>
</head>
<body>
    
    <div class="wrapper">
        <a href="../index.php"><img style="margin-top:30px; width:100px; height:100px;" src="../images/icons/logo.png" alt="logo"></a>
        <h1>Log In</h1>
        <br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            
            <input type="text" placeholder="Username" id="username" name="username" value="<?php echo $username; ?>" required>
            <?php if (!empty($username_err)): ?>
                <p class="error-message"><?php echo $username_err; ?></p>
            <?php endif; ?>
            <div class="p_word_toggle">
                <input type="password" placeholder="Password" id="password" name="password" required>
                <span onclick="toggle()" id="hide_show"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/></svg></span>
            </div>
            <?php if (!empty($password_err)): ?>
                <p class="error-message"><?php echo $password_err; ?></p>
            <?php endif; ?>
            
            <br>
            <button type="submit">Log In</button>
        </form>
        <br>
        <p>Don't have an account? <a href="../php/register.php">Register</a></p>
        <br>
    </div>
</body>
</html>