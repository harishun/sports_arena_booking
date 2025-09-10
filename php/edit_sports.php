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

// Default mode is 'add'
$mode = "add";
$sport = "Sport Name";
$image = "";
$description = "Short Description (Optional)";
$sport_id = null;
$arenas = [];

// Check if we are in 'edit' mode from URL (initial page load) or from a submitted form
if (isset($_REQUEST["sport"]) || (isset($_POST['mode']) && $_POST['mode'] == 'edit')) {
    $mode = "edit";
    // Use the sport name from the request or the form's original_sport_name
    $sport_name = mysqli_real_escape_string($conn, $_REQUEST["sport"] ?? $_POST["original_sport_name"]);

    $sql = "SELECT * FROM `sports` WHERE sport='" . $sport_name . "'";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $sport_id = $row['id'];
        $sport = $row["sport"];
        $image = $row["file_name"] ?? "";
        $description = htmlspecialchars($row["description"] ?? "");

        // Fetch existing arenas for this sport
        $sql_arenas = "SELECT * FROM `arenas` WHERE sport_id = $sport_id ORDER BY arena_name ASC";
        $result_arenas = mysqli_query($conn, $sql_arenas);
        while ($arena_row = mysqli_fetch_assoc($result_arenas)) {
            $arenas[] = $arena_row;
        }
    }
}


// Handle adding a new arena
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_arena'])) {
    $current_sport_name = mysqli_real_escape_string($conn, $_POST['sport_name']);
    $sport_id_query = "SELECT id FROM sports WHERE sport='$current_sport_name' LIMIT 1";
    $sport_id_result = mysqli_query($conn, $sport_id_query);
    if ($sport_id_row = mysqli_fetch_assoc($sport_id_result)) {
        $sport_id = $sport_id_row['id'];
        $arena_name = mysqli_real_escape_string($conn, $_POST['new_arena_name']);
        $location = mysqli_real_escape_string($conn, $_POST['new_arena_loc']);
        $open_time = $_POST['new_open'];
        $close_time = $_POST['new_close'];
        $price = $_POST['new_price'];

        $sql_insert_arena = "INSERT INTO arenas (sport_id, arena_name, location, open_time, close_time, hourly_price) VALUES ('$sport_id', '$arena_name', '$location', '$open_time', '$close_time', '$price')";

        if (mysqli_query($conn, $sql_insert_arena)) {
            // Redirect to refresh the page and show the new arena
            header("Location: edit_sports.php?sport=" . urlencode($current_sport_name));
            exit;
        } else {
            echo "Error adding arena: " . mysqli_error($conn);
        }
    }
}


// Handle saving sport details (Add or Edit)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_sport'])) {
    // Determine the mode from the hidden form field
    $current_mode = $_POST['mode'];
    $sport_name = mysqli_real_escape_string($conn, $_POST['sport_name']);
    $sport_desc = mysqli_real_escape_string($conn, $_POST['sport_desc']);
    $targetFile = $_POST['original_image']; // Keep original image by default

    if (isset($_FILES["sports_card_image"]) && $_FILES["sports_card_image"]["error"] == UPLOAD_ERR_OK) {
        $uploadDir = "../images/sport_cards/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES["sports_card_image"]["name"], PATHINFO_EXTENSION));
        $newName = bin2hex(random_bytes(8)) . "." . $ext;
        $targetFile = $uploadDir . $newName;
        move_uploaded_file($_FILES["sports_card_image"]["tmp_name"], $targetFile);
    }

    if ($current_mode == 'add') {
        $sql = "INSERT INTO sports (sport, description, file_name) VALUES ('$sport_name', '$sport_desc', '$targetFile')";
    } else { // It's 'edit' mode
        $original_sport_name = mysqli_real_escape_string($conn, $_POST["original_sport_name"]);
        $sql = "UPDATE sports SET sport='$sport_name', description='$sport_desc', file_name='$targetFile' WHERE sport='$original_sport_name'";
    }

    if (mysqli_query($conn, $sql)) {
        if ($current_mode == 'add') {
            header("Location: edit_sports.php?sport=" . urlencode($sport_name));
        } else {
            header("Location: sports.php");
        }
        exit;
    } else {
        // This will now only throw a duplicate error if you try to rename a sport to another existing sport's name.
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
    <a href="sports.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
        </svg></a>
    <div class="card">
        <div class="container">
            <h2><?php echo ucfirst($mode) ?> Sport <button class="save_btn" form="sports" type="submit" name="save_sport"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM480-240q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg></button>
            </h2>
            <form id="sports" action="edit_sports.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                <input type="hidden" name="original_sport_name" value="<?php echo htmlspecialchars($sport); ?>">
                <input type="hidden" name="original_image" value="<?php echo htmlspecialchars($image); ?>">

                <input class="input" type="text" value="<?php echo htmlspecialchars($sport); ?>" id="sports_name" name="sport_name" required>
                <br>
                <div class="col2">
                    <label class="card_preview_container" for="sports_card_image">
                        <div class="card_preview" id="cardpreview" <?php if (!empty($image)) : ?> style="background-image: url('<?php echo $image; ?>');" <?php endif; ?>>
                            <?php if (empty($image)) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48" fill="#e3e3e3">
                                    <path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                    </label>
                    <input onchange="previewImage(this, document.getElementById('cardpreview'));" class="input" type="file" id="sports_card_image" name="sports_card_image" accept="image/*">
                    <br>
                    <textarea maxlength="170" class="input" name="sport_desc" rows="3" placeholder="Short description..."><?php echo $description; ?></textarea>
                </div>
                <br>
            </form>

            <?php if ($mode == 'edit') : ?>
                <div class="sub_card">
                    <h3>Arenas</h3>
                    <table>
                        <thead>
                            <th>Arena Name</th>
                            <th>Location</th>
                            <th>Price (LKR/hr)</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <?php if (empty($arenas)) : ?>
                                <tr>
                                    <td colspan="4">No arenas found for this sport.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($arenas as $arena) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($arena['arena_name']); ?></td>
                                        <td><?php echo htmlspecialchars($arena['location']); ?></td>
                                        <td><?php echo htmlspecialchars($arena['hourly_price']); ?></td>
                                        <td>
                                            <a class="btn" href="edit_arena.php?id=<?php echo $arena["id"]?>"><svg xmlns='http://www.w3.org/2000/svg'
                                                    height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'>
                                                    <path
                                                        d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z\z" />
                                                </svg></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="sub_card">
                    <h3>Add New Arena
                        <button form="arena" type="submit" name="add_arena" class="add_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z" />
                            </svg>
                        </button>
                    </h3>
                    <br>
                    <form id="arena" class="form" action="edit_sports.php" method="post">
                        <input type="hidden" name="sport_name" value="<?php echo htmlspecialchars($sport); ?>">
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
                        <input type="number" name="new_price" min="0" step="100" placeholder="e.g. 1500" required>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>