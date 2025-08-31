<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

include("connect_db.php");

if (isset($_GET['id'])) {
    $arena_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // First, get the sport name for redirection
    $sql_sport = "SELECT s.sport FROM sports s JOIN arenas a ON s.id = a.sport_id WHERE a.id = $arena_id";
    $result_sport = mysqli_query($conn, $sql_sport);
    $sport_name = '';
    if ($row = mysqli_fetch_assoc($result_sport)) {
        $sport_name = $row['sport'];
    }

    $sql_delete = "DELETE FROM arenas WHERE id = $arena_id";
    if (mysqli_query($conn, $sql_delete)) {
        header("Location: edit_sports.php?sport=" . urlencode($sport_name));
        exit;
    } else {
        echo "Error deleting arena: " . mysqli_error($conn);
    }
} else {
    header('Location: sports.php');
    exit;
}
?>