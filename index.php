<?php
session_start();
include("php/connect_db.php");
$sql = "SELECT `sport`,`file_name` FROM `sports`;";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/home.css">
    <title>Home</title>
</head>

<body>
    <a class="logo" href="index.php"><img src="images/icons/logo.png" alt="logo" height="auto" width="75px"></a>
    <nav>
        <ul>
            <li onclick="window.location.href='index.php'" class="active"><a href="index.php"><svg
                        xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#e3e3e3">
                        <path
                            d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
                    </svg></a></li>
            <?php
            if (isset($_SESSION['username'])) {
                echo '<li onclick="window.location.href=\'php/profile.php\';">
                        <a href="/php/profile.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z"/>
                            </svg>
                        </a>
                        </li>';
            } else {
                echo '<li onclick="window.locatio.href=\'/php/log_in.php\';">
                        <a href="/php/log_in.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z"/>
                            </svg>
                        </a>
                    </li>';
            }
            ?>
            <li onclick="window.locatio.href='php/contact.php'"><a href="php/contact.php"><svg
                        xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#e3e3e3">
                        <path
                            d="M440-120v-80h320v-284q0-117-81.5-198.5T480-764q-117 0-198.5 81.5T200-484v244h-40q-33 0-56.5-23.5T80-320v-80q0-21 10.5-39.5T120-469l3-53q8-68 39.5-126t79-101q47.5-43 109-67T480-840q68 0 129 24t109 66.5Q766-707 797-649t40 126l3 52q19 9 29.5 27t10.5 38v92q0 20-10.5 38T840-249v49q0 33-23.5 56.5T760-120H440Zm-80-280q-17 0-28.5-11.5T320-440q0-17 11.5-28.5T360-480q17 0 28.5 11.5T400-440q0 17-11.5 28.5T360-400Zm240 0q-17 0-28.5-11.5T560-440q0-17 11.5-28.5T600-480q17 0 28.5 11.5T640-440q0 17-11.5 28.5T600-400Zm-359-62q-7-106 64-182t177-76q89 0 156.5 56.5T720-519q-91-1-167.5-49T435-698q-16 80-67.5 142.5T241-462Z" />
                    </svg></a></li>
        </ul>
    </nav>
    <div class="container">
        <form id="sportForm" action="php/book.php" method="post">
            <?php
            // Check if there are any users
            if (mysqli_num_rows($result) > 0) {
                // for each user generate card
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='card' onclick=\"submitSport('" . $row['sport'] . "')\">";
                    echo "<h2>" . ucfirst(htmlspecialchars($row['sport'])) . "</h2>";
                    echo "<img class='card_img' src='" . htmlspecialchars($row["file_name"]) . "' alt='" . htmlspecialchars($row["sport"]) . "'>";
                    echo "</div>";
                }
            }


            ?>

            <div class="card" onclick="submitSport('badminton')">
                <h2>Badminton</h2>
                <img class="card_img" src="images/sport_cards/badminton.jpg" alt="Badminton Image">
            </div>
            <div class="card" onclick="submitSport('basketball')">
                <h2>Basketball</h2>
                <img class="card_img" src="images/sport_cards/badminton.jpg" alt="Basketball Image">
            </div>
            <div class="card" onclick="submitSport('tennis')">
                <h2>Tennis</h2>
                <img class="card_img" src="images/sport_cards/badminton.jpg" alt="Tennis Image">
            </div>
            <!-- Add more cards -->
            <input type="hidden" id="sportInput" name="sport">
        </form>
    </div>
</body>
<script src="/js/submit.js"></script>

</html>