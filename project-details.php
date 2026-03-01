<?php
include "backend/config.php";

/* SECURITY */
$id = intval($_GET['id']);

/* FETCH PROJECT + CATEGORY */
$projectCategoryCol = 'project_category';
$projectCatExistsRes = mysqli_query(
    $conn,
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='projects' AND COLUMN_NAME='project_category' LIMIT 1"
);
if (!($projectCatExistsRes && mysqli_fetch_row($projectCatExistsRes))) {
    $projectCategoryCol = 'category_id';
}

$categoryLabelCol = 'name';
$catNameExistsRes = mysqli_query(
    $conn,
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='categories' AND COLUMN_NAME='name' LIMIT 1"
);
if (!($catNameExistsRes && mysqli_fetch_row($catNameExistsRes))) {
    $catTitleExistsRes = mysqli_query(
        $conn,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='categories' AND COLUMN_NAME='title' LIMIT 1"
    );
    if ($catTitleExistsRes && mysqli_fetch_row($catTitleExistsRes)) {
        $categoryLabelCol = 'title';
    } else {
        $catLegacyExistsRes = mysqli_query(
            $conn,
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='categories' AND COLUMN_NAME='category_name' LIMIT 1"
        );
        if ($catLegacyExistsRes && mysqli_fetch_row($catLegacyExistsRes)) {
            $categoryLabelCol = 'category_name';
        }
    }
}

$projectQuery = mysqli_query($conn,"
SELECT projects.*, categories.{$categoryLabelCol} AS category_name
FROM projects
LEFT JOIN categories ON projects.{$projectCategoryCol} = categories.id
WHERE projects.id = $id
");

if (!$projectQuery) {
    $projectQuery = mysqli_query($conn, "SELECT projects.*, '' AS category_name FROM projects WHERE projects.id = $id");
}

/* If project not found */
if(mysqli_num_rows($projectQuery) == 0){
    die("Project not found");
}

$project = mysqli_fetch_assoc($projectQuery);

$projectTitle = '';
if (isset($project['project_title']) && $project['project_title'] !== '') {
    $projectTitle = $project['project_title'];
} elseif (isset($project['title']) && $project['title'] !== '') {
    $projectTitle = $project['title'];
}

$projectDescription = '';
if (isset($project['project_description']) && $project['project_description'] !== '') {
    $projectDescription = $project['project_description'];
} elseif (isset($project['description']) && $project['description'] !== '') {
    $projectDescription = $project['description'];
}

$projectCategoryName = '';
if (isset($project['category_name']) && $project['category_name'] !== '') {
    $projectCategoryName = $project['category_name'];
}

/* FETCH IMAGES */
$images = mysqli_query($conn,
"SELECT * FROM project_images WHERE project_id = $id");
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo htmlspecialchars($project['project_title']); ?> - AKRINO Scedio</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/portfolio-styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

/* page spacing under fixed navbar */
body{
    background:#f5f6fa;
    font-family:Arial;
    padding-top:24px;
}

/* MAIN LAYOUT */
.project-container{
    max-width:1320px;
    margin:auto;
    padding:36px 18px 80px;
}

/* HEADER */
.project-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-bottom:24px;
}

.project-header h1{
    margin:0;
    font-size:42px;
    letter-spacing:-0.5px;
    line-height:1.15;
    color:#1f2937;
    animation:fadeUp 650ms ease both;
}

.project-header .btn{
    border-radius:999px;
    padding:10px 14px;
    font-weight:600;
    background:#8a6df1;
    color:white;
}

/* CATEGORY TAG */
.project-tag{
    display:inline-block;
    padding:0;
    background:transparent;
    color:#6b7280;
    border-radius:0;
    font-size:13px;
    margin-top:6px;
}

/* GRID */
.project-grid{
    display:grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap:28px;
    align-items:start;
}

/* DESCRIPTION BOX */
.project-description{
    background:white;
    padding:28px;
    border-radius:14px;
    box-shadow:0 10px 28px rgba(0,0,0,0.08);
    animation:fadeUp 800ms ease both;
    animation-delay:120ms;
}

.project-description h3{
    margin:0 0 12px;
    font-size:18px;
    font-weight:700;
    letter-spacing:0.2px;
    color:#111827;
}

.project-description p{
    line-height:1.8;
    color:#4b5563;
    font-size:16.5px;
}

@keyframes fadeUp{
    from{ opacity:0; transform:translateY(14px); }
    to{ opacity:1; transform:translateY(0); }
}

/* IMAGES */
.project-images{
    background:white;
    border-radius:14px;
    box-shadow:0 10px 28px rgba(0,0,0,0.08);
    padding:22px;
}

.project-images.reveal-ready{
    opacity: 0;
    transform: translateY(22px);
    filter: blur(6px);
    transition: opacity 900ms cubic-bezier(0.22, 1, 0.36, 1), transform 900ms cubic-bezier(0.22, 1, 0.36, 1), filter 900ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: opacity, transform, filter;
}

.project-images.reveal-ready.in-view{
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
}

.image-scroll{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
    gap:16px;
}

.project-images img{
    width:100%;
    border-radius:12px;
    background:#f3f4f6;
    border:1px solid rgba(17,24,39,0.08);
    padding: 10px;
    box-sizing: border-box;
    aspect-ratio: 16 / 10;
    object-fit: contain;
    object-position: center;
    transform: translateY(0);
}

.project-images.reveal-ready img{
    opacity: 0;
    transform: translateY(18px);
    filter: blur(4px);
    transition: opacity 900ms cubic-bezier(0.22, 1, 0.36, 1), transform 900ms cubic-bezier(0.22, 1, 0.36, 1), filter 900ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: opacity, transform, filter;
}

.project-images.reveal-ready.in-view img{
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
}

.project-images.reveal-ready.in-view img:nth-child(2){
    transition-delay: 120ms;
}

.project-images.reveal-ready.in-view img:nth-child(3){
    transition-delay: 240ms;
}

@media (prefers-reduced-motion: reduce){
    .project-images.reveal-ready,
    .project-images.reveal-ready img{
        transition: none !important;
        transform: none !important;
        filter: none !important;
        opacity: 1 !important;
    }
}

.project-images img:hover{
    transform: translateY(-2px) scale(1.01);
    transition: transform 250ms ease;
}

/* DARK MODE SUPPORT */
.dark-theme{
    background:#121212;
    color:white;
}

.dark-theme .project-description{
    background:#1f1f1f;
}

.dark-theme .project-images{
    background:#1f1f1f;
}

.dark-theme .project-images img{
    background:#111827;
    border-color: rgba(255,255,255,0.10);
}

.dark-theme .project-header h1{
    color:#f2f2f2;
}

.dark-theme .project-description h3{
    color:#f2f2f2;
}

.dark-theme .project-description p{
    color:#cfcfcf;
}

/* RESPONSIVE */
@media(max-width:900px){

body{
    padding-top:12px;
}

.project-container{
    padding:12px 14px 54px;
}

.project-header{
    margin-bottom:12px;
    flex-direction:row;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
}

.project-header > div{
    min-width:0;
}

.project-header h1{
    font-size:24px;
    margin-bottom:6px;
}

.project-header .btn{
    width:auto;
    padding:7px 10px;
    font-size:14px;
    white-space:nowrap;
    align-self:flex-start;
    margin-bottom:10px;
}

.project-grid{
    grid-template-columns:1fr;
}

.project-images{
    order: 1;
}

.project-description{
    order: 2;
}

.image-scroll{
    grid-template-columns:1fr;
}

.project-description{
    position:static;
}

}

</style>
</head>

<body>

<!-- LOAD SAVED THEME -->
<script>
if(localStorage.getItem("theme") === "dark"){
document.body.classList.add("dark-theme");
}
</script>

<div class="project-container">

<div class="project-header">
<div>
<a href="portfolio.php" class="btn"><i class="fas fa-arrow-left" style="margin-right:8px;"></i>Back to Portfolio</a>
<h1><?php echo htmlspecialchars($projectTitle); ?></h1>

<!-- CATEGORY TAG (NEW) -->
<span class="project-tag">
<?php echo htmlspecialchars($projectCategoryName); ?>
</span>
</div>
</div>

<div class="project-grid">

<!-- DESCRIPTION -->
<div class="project-description">

<h3>Project Overview</h3>

<p>
<?php echo nl2br(htmlspecialchars($projectDescription)); ?>
</p>

</div>

<!-- IMAGES -->
<div class="project-images">
<div class="image-scroll">

<?php
if(mysqli_num_rows($images) > 0){
while($img=mysqli_fetch_assoc($images)){
?>

<img src="uploads/<?php echo $img['image_name']; ?>">

<?php }} else { ?>

<p>No images uploaded</p>

<?php } ?>

</div>
</div>

<script>
    (function(){
        const box = document.querySelector('.project-images');
        if (!box) return;

        // Enable reveal styles only when JS is running.
        box.classList.add('reveal-ready');

        if (!('IntersectionObserver' in window)) {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    box.classList.add('in-view');
                });
            });
            return;
        }

        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            box.classList.add('in-view');
                            io.disconnect();
                        });
                    });
                }
            });
        }, { threshold: 0.18, rootMargin: '0px 0px -10% 0px' });

        io.observe(box);
    })();
</script>

</div>

</div>

<script src="js/shared.js"></script>

</body>
</html>
