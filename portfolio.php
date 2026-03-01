<?php include "backend/config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Portfolio - AKRINO Scedio</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/portfolio-styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

<?php include "navbar.php"; ?>

<section id="portfolio" class="portfolio" style="padding: 100px 20px;">

<div class="container" style="max-width: 1200px; margin: 0 auto;">

<div class="section-header" style="text-align: center; margin-bottom: 60px;">
<h2 style="font-size: 2.5rem;">Our Portfolio</h2>
<p>Explore our latest projects and creative work</p>
</div>

<!-- 🔥 DYNAMIC FILTER BUTTONS -->
<div class="portfolio-filters">

<button class="filter-btn active" onclick="filterProjects('all', this)">All</button>

<?php
$catQuery = mysqli_query($conn,"SELECT id, name FROM categories");
if ($catQuery) {
while($cat=mysqli_fetch_assoc($catQuery)){
    if (!isset($cat['id']) || !isset($cat['name'])) { continue; }
?>

<button class="filter-btn"
onclick="filterProjects('<?php echo $cat['id']; ?>', this)">
<?php echo $cat['name']; ?>
</button>

<?php } ?>

<?php } ?>

</div>

<!-- 🔥 JOIN QUERY -->
<div id="publicPortfolioGrid" class="portfolio-grid">

<?php
$query = "
SELECT p.*, c.name AS category_name
FROM projects p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
";

$result = mysqli_query($conn,$query);
if (!$result) {
    $fallback = "SELECT projects.* FROM projects ORDER BY projects.id DESC";
    $result = mysqli_query($conn, $fallback);
}

if ($result) {
while($row=mysqli_fetch_assoc($result)){

$img = null;
$imgQuery = mysqli_query($conn,
"SELECT image_name FROM project_images 
WHERE project_id = {$row['id']} LIMIT 1");
if ($imgQuery) {
    $img = mysqli_fetch_assoc($imgQuery);
}
?>

<a href="project-details.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;">

<div class="portfolio-item" data-category="<?php echo isset($row['category_id']) ? $row['category_id'] : ''; ?>">

<div class="portfolio-image">

<?php if($img && isset($img['image_name']) && $img['image_name']){ ?>
<img src="uploads/<?php echo $img['image_name']; ?>">
<?php } else if (isset($row['image']) && $row['image']) { ?>
<img src="uploads/<?php echo $row['image']; ?>">
<?php } else { ?>
<img src="uploads/default.png">
<?php } ?>

<div class="portfolio-overlay">
<h3 style="color:white;">
<?php echo isset($row['title']) ? $row['title'] : ''; ?>
</h3>
</div>

</div>

<div class="portfolio-content">
<h3><?php echo isset($row['title']) ? $row['title'] : ''; ?></h3>
<p style="font-size:13px; color:#8a6df1;">
<?php echo isset($row['category_name']) ? $row['category_name'] : ''; ?>
</p>
</div>

</div>

</a>

<?php } ?>

<?php } ?>

</div>
</div>
</section>

<script src="js/shared.js"></script>

<script>
function filterProjects(category, btn){

let grid = document.getElementById("publicPortfolioGrid");
if (!grid) return;

let links = grid.querySelectorAll("a");

links.forEach(link => {
    let item = link.querySelector(".portfolio-item");
    if (!item) return;

    if(category === "all" || item.dataset.category == category){
        link.style.display = "block";
    }else{
        link.style.display = "none";
    }
});

document.querySelectorAll(".filter-btn").forEach(b=>b.classList.remove("active"));
btn.classList.add("active");
}
</script>

<?php include "footer.php"; ?>

</body>
</html>
