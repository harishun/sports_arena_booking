<?php
// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

include("connect_db.php");
$arena_id = null;
$arena = null;
$sport_name = '';

if (isset($_GET['id'])) {
    $arena_id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT a.*, s.sport FROM arenas a JOIN sports s ON a.sport_id = s.id WHERE a.id = $arena_id";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $arena = $row;
        $sport_name = $row['sport'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_arena'])) {
    $arena_id = mysqli_real_escape_string($conn, $_POST['arena_id']);
    $arena_name = mysqli_real_escape_string($conn, $_POST['arena_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $open_time = $_POST['open_time'];
    $close_time = $_POST['close_time'];
    $price = $_POST['hourly_price'];
    $sport_name = $_POST['sport_name'];
    echo "<script>window.alert(".$sport_name.");</script>";
    $sql_update = "UPDATE arenas SET arena_name='$arena_name', location='$location', open_time='$open_time', close_time='$close_time', hourly_price='$price' WHERE id=$arena_id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: edit_sports.php?sport=" . urlencode($sport_name));
        exit;
    } else {
        echo "Error updating arena: " . mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Arena</title>
    <link rel="stylesheet" href="../../css/edit_sports.css">
</head>
<body>
     <a href="edit_sports.php?sport=<?php echo $sport_name?>" class="button back"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
        </svg></a>
    <div class="card">
        <div class="container">
            <h2>Edit Arena<button class="save_btn" form="edit_arena_form" type="submit" name="update_arena"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM480-240q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg></button></h2>
            <?php if ($arena): ?>
            <form id="edit_arena_form" action="edit_arena.php" method="post">
                <input type="hidden" name="arena_id" value="<?php echo $arena['id']; ?>">
                <input type="hidden" name="sport_name" value="<?php echo $sport_name; ?>">
                
                <label>Arena Name: <input type="text" name="arena_name" value="<?php echo htmlspecialchars($arena['arena_name']); ?>" required></label>
                <br>
                <label>Location: <input type="text" name="location" value="<?php echo htmlspecialchars($arena['location']); ?>" required></label>
                <br>
                <label>Active Hours:</label>
                <div class="col2">
                    <input type="time" name="open_time" value="<?php echo $arena['open_time']; ?>" required> —
                    <input type="time" name="close_time" value="<?php echo $arena['close_time']; ?>" required>
                </div>
                <br>
                <label>Hourly Price: <input type="number" name="hourly_price" min="0" step="100" value="<?php echo $arena['hourly_price']; ?>" required></label>
                <br>
            </form>
            <form method="POST" action="delete_arena.php?id=<?php echo $arena["id"]?>" onsubmit="return confirm('Are you absolutely sure you want to delete this arena? This action cannot be undone.');">
                <button type="submit" name="delete" class="delete-btn"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg></button>
            </form>
            <?php else: ?>
                <p>Arena not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>