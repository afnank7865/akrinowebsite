<?php
include "backend/config.php";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_important TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$editId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$editRow = null;
if ($editId > 0) {
    $res = mysqli_query($conn, "SELECT * FROM notifications WHERE id=$editId");
    if ($res && mysqli_num_rows($res) > 0) {
        $editRow = mysqli_fetch_assoc($res);
    }
}

$rows = [];
$res = mysqli_query($conn, "SELECT * FROM notifications ORDER BY id DESC");
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Notifications - AKRINO Studio</title>
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
                <li class="active" data-section="notifications"><a href="admin-notifications.php"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                <li data-section="services"><a href="admin-services.php"><i class="fas fa-concierge-bell"></i> <span>Services</span></a></li>
                <li data-section="portfolio"><a href="admin-dashboard.php"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a></li>
                <li id="logoutBtn"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar"><i class="fas fa-bars"></i></button>
                <h1>Notifications</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="recent-activity" style="margin-top:0;">
                <div class="section-header">
                    <h2><?php echo $editRow ? 'Edit Notification' : 'Add Notification'; ?></h2>
                    <a href="admin-notifications.php" class="view-all" style="text-decoration:none;">Reset</a>
                </div>

                <form class="notification-form" method="POST" action="backend/notifications_crud.php">
                    <input type="hidden" name="action" value="<?php echo $editRow ? 'update' : 'create'; ?>">
                    <?php if ($editRow) { ?>
                        <input type="hidden" name="id" value="<?php echo (int)$editRow['id']; ?>">
                    <?php } ?>

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($editRow['title'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" required><?php echo htmlspecialchars($editRow['message'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                        <input type="checkbox" id="is_important" name="is_important" <?php echo (($editRow['is_important'] ?? 0) ? 'checked' : ''); ?>>
                        <label for="is_important" style="margin:0;">Important</label>
                    </div>

                    <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo (($editRow['is_active'] ?? 1) ? 'checked' : ''); ?>>
                        <label for="is_active" style="margin:0;">Active</label>
                    </div>

                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editRow ? 'Update' : 'Create'; ?></button>
                        <a href="admin-dashboard.html" class="btn btn-secondary" style="text-decoration:none;"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>

            <div class="quick-actions">
                <div class="section-header">
                    <h2>Existing Notifications</h2>
                </div>

                <?php if (count($rows) === 0) { ?>
                    <p style="color: var(--muted);">No notifications found.</p>
                <?php } else { ?>
                    <div style="display:flex; flex-direction:column; gap:12px; margin-top:12px;">
                        <?php foreach ($rows as $r) { ?>
                            <div class="notification-card <?php echo ((int)$r['is_important'] === 1) ? 'important' : ''; ?>">
                                <div class="notification-header">
                                    <div>
                                        <span class="notification-title"><?php echo htmlspecialchars($r['title']); ?></span>
                                        <span class="notification-date"><?php echo htmlspecialchars($r['created_at']); ?></span>
                                    </div>
                                    <?php if ((int)$r['is_active'] === 0) { ?>
                                        <span class="badge" style="background:#64748b; color:white; padding:2px 8px; border-radius:10px; font-size:0.8em;">Inactive</span>
                                    <?php } ?>
                                </div>
                                <div class="notification-message"><?php echo nl2br(htmlspecialchars($r['message'])); ?></div>
                                <div class="notification-actions">
                                    <a class="btn btn-primary" style="text-decoration:none;" href="admin-notifications.php?edit=<?php echo (int)$r['id']; ?>"><i class="fas fa-pen"></i> Edit</a>
                                    <form method="POST" action="backend/notifications_crud.php" onsubmit="return confirm('Delete this notification?');" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
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
