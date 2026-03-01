<?php include "backend/config.php"; ?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Project</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h3 class="mb-4">Add Project</h3>

            <div class="d-flex gap-2 mb-4 flex-wrap">
              <a href="admin-dashboard.html" class="btn btn-outline-secondary">Back to Admin Panel</a>
              <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Go Back</button>
              <a href="portfolio.php" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">View Portfolio</a>
            </div>

            <form action="backend/add_project.php" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Project Title</label>
                <input type="text" class="form-control" name="project_title" placeholder="Project Title" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Project Description</label>
                <textarea class="form-control" name="description" rows="4" placeholder="Project Description"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="project_category" required>
                  <option value="">Select Category</option>

                  <?php
                  $cats = mysqli_query($conn,"SELECT * FROM categories");
                  while($c=mysqli_fetch_assoc($cats)){
                  ?>
                  <option value="<?php echo $c['id']; ?>">
                    <?php echo isset($c['category_name']) ? $c['category_name'] : (isset($c['name']) ? $c['name'] : (isset($c['title']) ? $c['title'] : '')); ?>
                  </option>
                  <?php } ?>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label">Project Images</label>
                <input class="form-control" type="file" name="project_images[]" multiple required>
              </div>

              <button type="submit" class="btn btn-primary">Add Project</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
