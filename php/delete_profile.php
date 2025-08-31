<?php
session_start();
include_once 'connect_db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Check if the form was submitted with the 'delete' action
if(isset($_POST['delete'])){
    $username = $_SESSION['username'];
    
    // First, get all of the user's details before we delete them
    $sql_get_user = "SELECT id, f_name, l_name, e_mail, tele FROM users WHERE username='$username'";
    $result_user = mysqli_query($conn, $sql_get_user);

    if($user_row = mysqli_fetch_assoc($result_user)){
        $user_id = $user_row['id'];
        $guest_name = mysqli_real_escape_string($conn, $user_row['f_name'] . ' ' . $user_row['l_name']);
        $guest_email = mysqli_real_escape_string($conn, $user_row['e_mail']);
        $guest_phone = mysqli_real_escape_string($conn, $user_row['tele']);
        
        // Step 1: Anonymize the user's bookings by converting them to guest bookings
        // We copy their details into the guest columns and set the user_id to NULL
        $sql_update_bookings = "UPDATE bookings 
                                SET user_id = NULL, 
                                    guest_name = '$guest_name', 
                                    guest_email = '$guest_email', 
                                    guest_phone = '$guest_phone' 
                                WHERE user_id = $user_id";
        
        // This query needs to run successfully before we can delete the user
        if (mysqli_query($conn, $sql_update_bookings)) {
            // Step 2: Now that bookings are safe, delete the user account
            $sql_delete_user = "DELETE FROM users WHERE id = $user_id";
            if(mysqli_query($conn, $sql_delete_user)){
                // Step 3: Log out, destroy the session, and redirect to the home page
                session_unset();
                session_destroy();
                header("Location: ../index.php?account_deleted=true");
                exit();
            } else {
                // Handle a potential error during user deletion
                header("Location: profile.php?error=deletefailed");
                exit();
            }
        } else {
            // Handle an error during the booking update
            header("Location: profile.php?error=booking_conversion_failed");
            exit();
        }
    }
} else {
    // Redirect back to profile if the form wasn't submitted correctly
    header("Location: profile.php");
    exit();
}
?>