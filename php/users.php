<?php
include("connect_db.php");

// Check if the user is logged in and has admin role
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
$sql = "SELECT `f_name`, `l_name`, `tele`, `e_mail` FROM `users` WHERE `role` = 'user' ORDER BY `f_name` ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="../../css/list.css">
</head>

<body>
    <!-- USERS -->
    <div class="card" id="users">
        <h2>Registered Users<a href="admin_home.php" class="button back"><svg
                    xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e3e3e3">
                    <path
                        d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
                </svg></a></h2>
        <div class="table">
            <table>
                <thead>
                    <th>Full Name</th>
                    <th>Phone Number</th>
                    <th>E-Mail</th>
                </thead>
                <tbody>
                    <?php
                    // Check if there are any users
                    if (mysqli_num_rows($result) > 0) {
                        //for each users generate table
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['f_name']) . " " . htmlspecialchars($row['l_name']) . "</td>
                                    <td>" . htmlspecialchars($row['tele']) . "</td>
                                    <td>" . htmlspecialchars($row['e_mail']) . "</td>
                                  </tr>";
                        }
                        } else {
                            echo "<tr><td colspan='3'>You Have No Registered Users.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>