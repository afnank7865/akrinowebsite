<?php
include "backend/config.php";

/* 1️⃣ Check id in URL */
if(!isset($_GET['id'])){
    die("No Project Selected");
}

$id = intval($_GET['id']);

/* 2️⃣ Fetch project */
$query = mysqli_query($conn,"SELECT * FROM projects WHERE id='$id'");

if(mysqli_num_rows($query)==0){
    die("Project Not Found");
}

$row = mysqli_fetch_assoc($query);

$projectTitle = '';
if (isset($row['project_title']) && $row['project_title'] !== '') {
    $projectTitle = $row['project_title'];
} elseif (isset($row['title']) && $row['title'] !== '') {
    $projectTitle = $row['title'];
}

$projectDescription = '';
if (isset($row['project_description']) && $row['project_description'] !== '') {
    $projectDescription = $row['project_description'];
} elseif (isset($row['description']) && $row['description'] !== '') {
    $projectDescription = $row['description'];
}

$projectCategoryId = 0;
if (isset($row['project_category'])) {
    $projectCategoryId = (int)$row['project_category'];
} elseif (isset($row['category_id'])) {
    $projectCategoryId = (int)$row['category_id'];
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Project</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-8">
        <div class="card shadow-sm mb-4">
          <div class="card-body p-4">
            <h3 class="mb-4">Edit Project</h3>

            <form action="backend/update_project.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

              <div class="mb-3">
                <label class="form-label">Project Title</label>
                <input type="text" class="form-control" name="project_title" value="<?php echo htmlspecialchars($projectTitle); ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($projectDescription); ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="project_category" required>
                  <?php
                  $cats = mysqli_query($conn,"SELECT * FROM categories");
                  while($c=mysqli_fetch_assoc($cats)){
                  ?>
                  <option value="<?php echo $c['id']; ?>" <?php if((int)$c['id']===$projectCategoryId) echo "selected"; ?>>
                    <?php echo isset($c['category_name']) ? $c['category_name'] : (isset($c['name']) ? $c['name'] : (isset($c['title']) ? $c['title'] : '')); ?>
                  </option>
                  <?php } ?>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label">Add New Images</label>
                <input class="form-control" type="file" name="new_images[]" multiple>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Project</button>
                <a href="admin-dashboard.html" class="btn btn-outline-secondary">Back to Admin Panel</a>
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Go Back</button>
                <a href="portfolio.php" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">View Portfolio</a>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h5 class="mb-3">Existing Images</h5>

            <div class="row g-3">
              <?php
              $images = mysqli_query($conn,
              "SELECT * FROM project_images WHERE project_id=".$row['id']);

              while($img = mysqli_fetch_assoc($images)){
              ?>

              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                  <img src="uploads/<?php echo $img['image_name']; ?>" class="card-img-top" style="height:140px; object-fit:cover;" alt="">
                  <div class="card-body p-2">
                    <a class="btn btn-danger btn-sm w-100" href="backend/delete_image.php?id=<?php echo $img['id']; ?>&project=<?php echo $row['id']; ?>" onclick="return confirm('Delete this image?')">Delete</a>
                  </div>
                </div>
              </div>

              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>