<?php
include "backend/config.php";

$id = $_GET['id'];

/* DELETE IMAGES FROM FOLDER */
$images = mysqli_query($conn,
"SELECT * FROM project_images WHERE project_id=$id");

while($img = mysqli_fetch_assoc($images)){
    unlink("uploads/".$img['image_name']);
}

/* DELETE FROM DB */
mysqli_query($conn,"DELETE FROM project_images WHERE project_id=$id");
mysqli_query($conn,"DELETE FROM projects WHERE id=$id");

header("Location: admin-dashboard.php");
?>
