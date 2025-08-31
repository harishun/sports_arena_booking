<?php
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
    <div class="card">
        <div class="container">
            <h2>Edit Arena</h2>
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
                <button type="submit" name="update_arena">Update Arena</button>
            </form>
            <?php else: ?>
                <p>Arena not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>