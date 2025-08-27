<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-In</title>
    <link rel="stylesheet" href="../css/credential.css">
    <script src="../js/credential.js"></script>
</head>
<body>
    
    <div class="wrapper">
        <a href="../index.php"><img style="margin-top:30px; width:100px; height:100px;" src="../images/icons/logo.png" alt="logo"></a>
        <h1>Log In</h1>
        <br>
        <form action="#" method="post" autocomplete=off>
            <input type="text" placeholder="Username" id="username" name="username" required>
            <br><br>
            <div class="p_word_toggle">
                <input type="password" placeholder="Password" id="password" name="password" required>
                <span onclick="toggle()" id="hide_show"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/></svg></span>
            </div>
            <br>
            <button type="submit">Log In</button>
        </form>
        <br>
        <p>Don't have an account? <a href="../php/register.php">Register</a></p>
        <br>
    </div>
</body>
</html>
<?php
include("connect_db.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

//get username and password from form
$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);

// Validate inputs
if (empty($username) || empty($password)) {
    exit;
}
// Prepare and execute the SQL statement
$username = mysqli_real_escape_string($conn, $username);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);  
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Verify the password
    if (password_verify($password, $row['password_hashed'])) {
        // Start session and set session variables
        session_start();
        $_SESSION['username'] = $username;
        if($row['role'] == 'admin') {
            $_SESSION['role'] = 'admin';
            header("Location: ../php/admin_home.php");
            exit;
        } else if($row['role'] == 'user') {
            $_SESSION['role'] = 'user';
            header("Location: ../index.php");
            exit;
        }
        
    } else {
        echo "<script>window.alert('Invalid password.');</script>";
    }
} else {
    //redirect to register page if user does not exist
    echo "<script>window.alert('User does not exist. Please register.');</script>";
    header("Location: ../php/register.php");
    exit;
} 

?>