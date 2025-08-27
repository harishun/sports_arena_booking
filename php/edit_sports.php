<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
} else {
    include("connect_db.php");
    if (isset($_POST["sport"])) {
        $mode = "edit";
    } else {
        $mode = "add";
    }
    if (isset($_POST["sport"])) {
        $sport = $_POST["sport"] ?? "No Description Found";
        $sql = "SELECT * FROM `sports` WHERE sport='" . $sport . "'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $sport = $row["sport"];
        $image = $row["file_name"]??"";
        $description = htmlspecialchars($row["description"] ?? "");
    } else {
        $sport = "Sport Name";
        $image = "";
        $description = "Short Decription ( Optional )";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($mode) ?> Sports</title>
    <link rel="stylesheet" href="../../css/edit_sports.css">
    <script src="../js/overlay.js"></script>
</head>

<body>
    <a href="sports.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
            viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path
                d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
        </svg></a>
    <div class="card">
        <div class="container"><!-- Add a new sport (with card image) -->
            <h2><?php echo ucfirst($mode) ?> Sport <div class="save_btn" onclick="document.getElementById('sports').requestSubmit()"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path
                            d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM480-240q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg></div>
            </h2>
            <form id="sports" action="edit_sports.php" method="post" enctype="multipart/form-data">
                <input class="input" type="text" placeholder="<?php echo ucfirst($sport) ?>" id="sports_name" name="sport_name" required>
                <br>
                <div class="col2">
                    <label class="card_preview_container" for="sports_card_image">
                        <div class="card_preview" id="cardpreview"
                            <?php if (!empty($image)): ?>
                            style="background-image: url('<?php echo $image ?>');"
                            <?php endif; ?>>
                            <?php if (empty($image)): ?>
                                <!-- Only show the icon if no saved image -->
                                <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48" fill="#e3e3e3">
                                    <path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                    </label>
                    <input onchange="previewImage(this,getElementById('cardpreview'));" class="input" type="file" id="sports_card_image" name="sports_card_image" accept="image/*"
                        required>
                    <br>
                    <textarea maxlength="170" class="input" name="sport_desc" rows="3"
                        placeholder="<?php echo $description?>"></textarea>
                </div>
                <br>
            </form>
            <div class="sub_card">
                <h3>Add New Arena<div onclick="document.getElementById('arena').requestSubmit()" class="add_btn"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z" />
                        </svg></div>
                </h3>
                <br>
                <form id="arena" class="form" action="#" method="post" enctype="multipart/form-data">
                    <input type="text" name="new_arena_name" placeholder="Arena Name" required>
                    <br>
                    <input type="text" name="new_arena_loc" placeholder="Building / Area" required>
                    <br>
                    <label>Active Hours</label>
                    <br><br>
                    <div class="col2"><input type="time" name="new_open" required> —
                        <input type="time" name="new_close" required>
                    </div>
                    <br>
                    <label>Hourly Price</label>
                    <br><br>
                    <input type="number" name="new_price" min="0" step="500" required>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Guard: ensure the file index exists
    if (!isset($_FILES["sports_card_image"])) {
        return;
    }

    $file = $_FILES["sports_card_image"];

    // Check for PHP upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Upload error (code: " . (int)$file['error'] . ")";
        return;
    }

    $tmpName = $file["tmp_name"];
    $originalName = $file["name"];

    // Get file extension
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Only allow jpg and png
    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        die("Only JPG and PNG allowed");
    }

    // Create uploads folder if needed
    $uploadDir = "../images/sport_cards/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Generate a random filename
    $newName = bin2hex(random_bytes(8)) . "." . $ext;
    $targetFile = $uploadDir . $newName;

    // Move file from temp to uploads with new name
    if (move_uploaded_file($tmpName, $targetFile)) {
        echo "File uploaded successfully as " . htmlspecialchars($newName);
    } else {
        echo "Upload failed";
    }
}
?>