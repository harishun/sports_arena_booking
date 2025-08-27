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
        <form action="#" method="post" autocomplete="off">
            <input type="text" placeholder="First Name" id="f_name" name="f_name" required>
            <br><br>
            <input type="text" placeholder="Last Name" id="l_name" name="l_name" required>
            <br><br>
            <input type="email" placeholder="E-Mail" id="e_mail" name="e_mail" required>
            <br>
            <div class="tele">
                <select name="code" id="code">
                    <option value="+94">LK +94</option>
                    <option value="+1">US +1</option>
                    <option value="+44">GB +44</option>
                    <option value="+91">IN +91</option>
                    <option value="+61">AU +66</option>
                </select>
                <input type="tel" placeholder="xx xxx xxxx" pattern="[0-9]{2} [0-9]{3} [0-9]{4}" id="tele" name="tele" required>
            </div>
            <br>
            <input type="text" placeholder="Username" id="username" name="username" required>
            <br><br>
            <div class="p_word_toggle">
                <input type="password" placeholder="Password" id="password" name="password" required>
                <span onclick="toggle()" id="hide_show"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960"><path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/></svg></span>
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

<?php
include("connect_db.php");

$f_name = $_POST['f_name']  ?? '';
$l_name = $_POST['l_name']  ?? '';
$e_mail = $_POST['e_mail']  ?? '';
$code = $_POST['code']  ?? '';
$tele_num = $_POST['tele']  ?? '';
$username = $_POST['username']  ?? '';
$password = $_POST['password']  ?? '';

$tele = $code ." ". $tele_num;

// Validate inputs
if (empty($f_name) || empty($l_name) || empty($e_mail) || empty($tele) || empty($username) || empty($password)) {
    exit;
}
// Hashing the password for security
$password_hashed = password_hash($password, PASSWORD_DEFAULT);


$check_sql = "SELECT username, e_mail FROM users 
              WHERE username = '$username' OR e_mail = '$e_mail' LIMIT 1";
$result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if ($row['username'] === $username) {
        echo "Username already exists!";
    } elseif ($row['e_mail'] === $e_mail) {
        echo "Email already exists!";
    }
} else {
    // Insert if no existing user found
    $f_name = mysqli_real_escape_string($conn, $f_name);
    $l_name = mysqli_real_escape_string($conn, $l_name);
    $e_mail = mysqli_real_escape_string($conn, $e_mail);
    $tele = mysqli_real_escape_string($conn, $tele);
    $email = filter_var($e_mail, FILTER_SANITIZE_EMAIL);    
    $username = mysqli_real_escape_string($conn, $username);
    $password_hash = mysqli_real_escape_string($conn, $password_hashed);
    $insert_sql = "INSERT INTO users (f_name, l_name, e_mail, tele, username, password_hashed) 
                   VALUES ('$f_name', '$l_name', '$e_mail', '$tele', '$username', '$password_hash')";
    if (mysqli_query($conn, $insert_sql)) {
        header("Location: ../php/log_in.php");
        exit;
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

// Close the database connection
mysqli_close($conn);

?>