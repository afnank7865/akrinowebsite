<?php
include "backend/config.php";

// Ensure table exists (created by contact_submit.php, but keep safe)
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    service VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Older installs may not have the 'service' column.
$colRes = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS c FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name='contact_messages' AND column_name='service'"
);
$hasServiceCol = false;
if ($colRes) {
    $colRow = mysqli_fetch_assoc($colRes);
    $hasServiceCol = (int)($colRow['c'] ?? 0) > 0;
}

if (!$hasServiceCol) {
    mysqli_query($conn, "ALTER TABLE contact_messages ADD COLUMN service VARCHAR(255) DEFAULT NULL");
    $hasServiceCol = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    if ($deleteId > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM contact_messages WHERE id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $deleteId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    header('Location: admin-inquiries.php');
    exit;
}

$rows = [];
$select = $hasServiceCol
    ? "SELECT id, name, email, service, message, created_at FROM contact_messages ORDER BY id DESC"
    : "SELECT id, name, email, message, created_at FROM contact_messages ORDER BY id DESC";

$res = mysqli_query($conn, $select);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        if (!$hasServiceCol) {
            $r['service'] = '';
        }
        $rows[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Client Inquiries - AKRINO Studio</title>
    <link rel="stylesheet" href="css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="admin-dashboard">
<div class="admin-container">
    <aside class="sidebar">
        <div class="logo">
            <h2>AKRINO Admin</h2>
        </div>
        <nav class="admin-nav">
            <ul>
                <li data-section="dashboard"><a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li data-section="testimonials"><a href="admin-testimonials.php"><i class="fas fa-star"></i> <span>Testimonials</span></a></li>
                <li data-section="inquiries" class="active"><a href="admin-inquiries.php"><i class="fas fa-inbox"></i> <span>Inquiries</span></a></li>
                <li data-section="services"><a href="admin-services.php"><i class="fas fa-concierge-bell"></i> <span>Services</span></a></li>
                <li data-section="portfolio"><a href="admin-dashboard.php"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a></li>
                <li data-section="activity"><a href="admin-activity.php"><i class="fas fa-clock"></i> <span>Activity</span></a></li>
                <li data-section="about"><a href="admin-about.php"><i class="fas fa-circle-info"></i> <span>About Page</span></a></li>
                <li id="logoutBtn"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar"><i class="fas fa-bars"></i></button>
                <h1>Client Inquiries</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="quick-actions" style="margin-top:0;">
                <div class="section-header">
                    <h2>Contact Form Submissions</h2>
                </div>

                <?php if (count($rows) === 0) { ?>
                    <p style="color: var(--muted);">No inquiries found.</p>
                <?php } else { ?>
                    <div style="overflow:auto;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">ID</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Name</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Email</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Service</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Message</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Created</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) { ?>
                                    <tr>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">#<?php echo (int)$r['id']; ?></td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <?php echo htmlspecialchars($r['name'] ?? ''); ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <a href="mailto:<?php echo htmlspecialchars($r['email'] ?? ''); ?>" style="color:inherit; text-decoration:underline;">
                                                <?php echo htmlspecialchars($r['email'] ?? ''); ?>
                                            </a>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <?php echo htmlspecialchars($r['service'] ?? ''); ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border); max-width:520px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?php echo htmlspecialchars($r['message'] ?? ''); ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <?php echo htmlspecialchars($r['created_at'] ?? ''); ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <form method="POST" onsubmit="return confirm('Delete this inquiry?');" style="display:inline;">
                                                <input type="hidden" name="delete_id" value="<?php echo (int)$r['id']; ?>" />
                                                <button type="submit" class="btn btn-delete" style="padding:8px 12px;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
</div>

<script src="js/admin-auth.js"></script>
<script src="js/admin-dashboard.js"></script>
</body>
</html>
