<?php
include("connect_db.php");

// Check if the user is logged in and has admin role
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="../../css/list.css">
</head>

<body>
    <!-- BOOKINGS -->
    <div class="card" id="bookings">
        <h2>Bookings<a href="admin_home.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg"
                    height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                    <path
                        d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
                </svg></a></h2>
        <div class="table">
            <table>
                <thead>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Sport</th>
                    <th>Arena</th>
                    <th>Full Name</th>
                    <th>Phone Number</th>
                </thead>
                <tbody>
                    <td>12:00</td>
                    <td>13:00</td>
                    <td>Badminton</td>
                    <td>UOC Court 1</td>
                    <td>Harishun Dhanaraj</td>
                    <td>+94 771519681</td>
                </tbody>
            </table>
        </div>
    </div>
</body>