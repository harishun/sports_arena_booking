<?php
include("connect_db.php");
// Fetch an admin's email to display
$admin_email = "admin@uocsports.com"; // Default email
$sql_admin = "SELECT e_mail FROM users WHERE role='admin' LIMIT 1";
$result_admin = mysqli_query($conn, $sql_admin);
if ($row = mysqli_fetch_assoc($result_admin)) {
    $admin_email = $row['e_mail'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/list.css">
    <title>Contact Us</title>
</head>

<body>
    <a class="logo" href="../index.php"><img src="../images/icons/logo.png" alt="logo" height="auto" width="75px"></a>
    <nav>
        <ul>
            <li><a href="../index.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                        <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
                    </svg></a></li>
            <li><a href="log_in.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                        <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
                    </svg></a></li>
            <li class="active"><a href="contact.php"><svg
                        xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#e3e3e3">
                        <path
                            d="M440-120v-80h320v-284q0-117-81.5-198.5T480-764q-117 0-198.5 81.5T200-484v244h-40q-33 0-56.5-23.5T80-320v-80q0-21 10.5-39.5T120-469l3-53q8-68 39.5-126t79-101q47.5-43 109-67T480-840q68 0 129 24t109 66.5Q766-707 797-649t40 126l3 52q19 9 29.5 27t10.5 38v92q0 20-10.5 38T840-249v49q0 33-23.5 56.5T760-120H440Zm-80-280q-17 0-28.5-11.5T320-440q0-17 11.5-28.5T360-480q17 0 28.5 11.5T400-440q0 17-11.5 28.5T360-400Zm240 0q-17 0-28.5-11.5T560-440q0-17 11.5-28.5T600-480q17 0 28.5 11.5T640-440q0 17-11.5 28.5T600-400Zm-359-62q-7-106 64-182t177-76q89 0 156.5 56.5T720-519q-91-1-167.5-49T435-698q-16 80-67.5 142.5T241-462Z" />
                    </svg></a></li>
        </ul>
    </nav>
    <div class="container-wrapper">
        <div class="card" style="width: 90%; max-width: 800px; text-align: left; padding: 40px;">
            <h2 style="text-align: center;">Contact Us</h2>
            <p style="font-size: 1.2rem; color: var(--secondary-text-color); text-align: center; margin-bottom: 30px;">
                Have questions or need assistance? Reach out to our admin team.
            </p>
            <p style="font-size: 1.3rem;">
                <strong>Admin Email:</strong>
                <a href="mailto:<?php echo htmlspecialchars($admin_email); ?>" style="color: var(--primary-accent-color); text-decoration: none;">
                    <?php echo htmlspecialchars($admin_email); ?>
                </a>
            </p>
            <p style="font-size: 1.3rem; margin-top: 15px;">
                <strong>Phone Support:</strong> +94 11 258 1245
            </p>
            <p style="font-size: 1.3rem; margin-top: 15px;">
                <strong>Location:</strong> UOC Sports Complex, Colombo 07, Sri Lanka
            </p>
        </div>
    </div>
</body>

</html>