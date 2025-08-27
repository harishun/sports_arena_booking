<?php
    include("connect_db.php");
    $sport = htmlspecialchars($_POST["sport"]);
    $sql = "SELECT * `sports` WHERE `sport`=$sport";
    $result = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sports</title>
    <link rel="stylesheet" href="../../css/edit_sports.css">
</head>

<body>
    <a href="../index.php" class="button back"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
            viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path
                d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z" />
        </svg></a>
    <div class="card">
        <div class="container"><!-- Add/Edit a sport (with card image) -->
            <h2><?php  echo $result["sport"]; ?><div class="save_btn"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path
                            d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM480-240q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg></div>
            </h2>
                <div class="col2">
                        <div class="card_preview"></div>
                    <div class="description"><?php echo $description ?></div>
                </div>
                <br>
            <div class="sub_card">
                <h3>Add New Arena<div class="add_btn"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z" />
                        </svg></div>
                    </h4>
                    <br>
                    <form class="form" action="#" method="post" enctype="multipart/form-data">
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


