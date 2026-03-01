<?php
include "backend/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portfolio</title>

<style>
body{
    font-family: Arial;
    background:#f4f4f4;
    padding:20px;
}

h1{
    text-align:center;
}

table{
    width:100%;
    border-collapse: collapse;
    background:white;
}

.table-wrap{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling: touch;
    border-radius:8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

td:nth-child(2){
    word-break: break-word;
}

th,td{
    padding:12px;
    border:1px solid #ddd;
    text-align:center;
}

th{
    background:#333;
    color:white;
}

img{
    width:80px;
    height:60px;
    object-fit:cover;
}

.btn{
    padding:6px 12px;
    text-decoration:none;
    border-radius:5px;
    color:white;
}

.edit{ background:orange; }
.delete{ background:red; }
.add-btn{ background:green; }

@media (max-width: 768px){
    body{ padding:12px; }
    h1{ font-size:20px; }
    th,td{ padding:8px; font-size:12px; }
    img{ width:64px; height:48px; }

    table{
        table-layout: fixed;
    }

    td:nth-child(2){
        min-width: 0;
    }

    td:nth-child(5) {
        min-width: 108px;
        overflow: hidden;
    }

    td a.btn {
        display: block;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        text-align: center;
        margin: 4px 0;
        padding: 8px 6px;
        font-size: 12px;
        line-height: 1.1;
    }
}

@media (max-width: 420px){
    .table-wrap{
        overflow-x: visible;
    }

    th:nth-child(3), td:nth-child(3),
    th:nth-child(4), td:nth-child(4){
        display:none;
    }
}

</style>
</head>

<body>

<h1>Portfolio</h1>

<div style="margin: 12px 0 18px; display:flex; gap:10px; flex-wrap:wrap;">
<a class="btn add-btn" href="admin-add-project.php">Add New Project</a>
<a class="btn edit" href="portfolio.php" target="_blank" rel="noopener noreferrer">View Portfolio</a>
<a class="btn" href="admin-dashboard.html" style="background:#2563eb;">Back to Admin Panel</a>
</div>

<div class="table-wrap">

<table>

<tr>
<th>ID</th>
<th>Title</th>
<th>Image</th>
<th>Created</th>
<th>Actions</th>
<th>Category</th>
</tr>

<?php
$query = "
SELECT p.*, c.name AS category_name
FROM projects p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
";

$result = mysqli_query($conn,$query);

if (!$result) {
    $result = mysqli_query($conn, "SELECT * FROM projects ORDER BY id DESC");
}

if ($result) {
while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<!-- ID -->
<td><?php echo $row['id']; ?></td>

<!-- TITLE -->
<td><?php echo isset($row['title']) ? $row['title'] : (isset($row['project_title']) ? $row['project_title'] : ''); ?></td>

<!-- IMAGE -->
<td>
<?php
$imgQuery = mysqli_query($conn,
"SELECT image_name FROM project_images 
 WHERE project_id = {$row['id']} 
 LIMIT 1");

$img = mysqli_fetch_assoc($imgQuery);

?>

<?php if($img){ ?>
<img src="uploads/<?php echo $img['image_name']; ?>">
<?php } else { ?>
No Image
<?php } ?>

</td>

<!-- DATE -->
<td><?php echo isset($row['created_at']) ? $row['created_at'] : ''; ?></td>

<!-- ACTION BUTTONS -->
<td>

<a class="btn edit"
href="edit-project.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="btn delete"
href="delete-project.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this project?')">
Delete
</a>

</td>
<!-- CATEGORY -->
<td><?php echo isset($row['category_name']) ? $row['category_name'] : ''; ?></td>

</tr>

<?php } ?>

<?php } ?>

</table>

</div>

</body>
</html>
