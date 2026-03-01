<?php
include "backend/config.php";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    company VARCHAR(255) DEFAULT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    title VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function testimonialsEnsureColumn(mysqli $conn, string $table, string $column, string $definition): void {
    $tableEsc = mysqli_real_escape_string($conn, $table);
    $colEsc = mysqli_real_escape_string($conn, $column);
    $existsSql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$tableEsc}' AND COLUMN_NAME = '{$colEsc}' LIMIT 1";
    $existsRes = mysqli_query($conn, $existsSql);
    if ($existsRes && mysqli_fetch_row($existsRes)) {
        return;
    }
    mysqli_query($conn, "ALTER TABLE `{$tableEsc}` ADD COLUMN `{$colEsc}` {$definition}");
}

testimonialsEnsureColumn($conn, 'testimonials', 'is_approved', 'TINYINT(1) NOT NULL DEFAULT 0');
testimonialsEnsureColumn($conn, 'testimonials', 'company', 'VARCHAR(255) DEFAULT NULL');
testimonialsEnsureColumn($conn, 'testimonials', 'rating', 'TINYINT NOT NULL DEFAULT 5');
testimonialsEnsureColumn($conn, 'testimonials', 'title', 'VARCHAR(255) DEFAULT NULL');
testimonialsEnsureColumn($conn, 'testimonials', 'created_at', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');

mysqli_query($conn, "UPDATE testimonials SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'approve') {
        $id = intval($_POST['id'] ?? 0);
        $approved = isset($_POST['is_approved']) ? (int)$_POST['is_approved'] : 1;
        $approved = $approved ? 1 : 0;
        if ($id > 0) {
            $legacyStatusExists2 = mysqli_query($conn, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='testimonials' AND COLUMN_NAME='status' LIMIT 1");
            $hasLegacyStatus = ($legacyStatusExists2 && mysqli_fetch_row($legacyStatusExists2));

            if ($hasLegacyStatus) {
                $stmt = mysqli_prepare($conn, "UPDATE testimonials SET is_approved=?, status=? WHERE id=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'iii', $approved, $approved, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    mysqli_query($conn, "UPDATE testimonials SET is_approved=" . (int)$approved . ", status=" . (int)$approved . " WHERE id=" . (int)$id);
                }
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE testimonials SET is_approved=? WHERE id=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ii', $approved, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    mysqli_query($conn, "UPDATE testimonials SET is_approved=" . (int)$approved . " WHERE id=" . (int)$id);
                }
            }

            if (mysqli_affected_rows($conn) === 0) {
                if ($hasLegacyStatus) {
                    mysqli_query($conn, "UPDATE testimonials SET is_approved=" . (int)$approved . ", status=" . (int)$approved . " WHERE id=" . (int)$id);
                } else {
                    mysqli_query($conn, "UPDATE testimonials SET is_approved=" . (int)$approved . " WHERE id=" . (int)$id);
                }
            }
        }
        header('Location: admin-testimonials.php');
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM testimonials WHERE id=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        header('Location: admin-testimonials.php');
        exit;
    }
}

$rows = [];
$res = mysqli_query($conn, "SELECT * FROM testimonials ORDER BY id DESC");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Testimonials - AKRINO Studio</title>
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
                <li class="active" data-section="testimonials"><a href="admin-testimonials.php"><i class="fas fa-star"></i> <span>Testimonials</span></a></li>
                <li data-section="inquiries"><a href="admin-inquiries.php"><i class="fas fa-inbox"></i> <span>Inquiries</span></a></li>
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
                <h1>Testimonials</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="quick-actions" style="margin-top:0;">
                <div class="section-header">
                    <h2>Client Feedback</h2>
                </div>

                <?php if (count($rows) === 0) { ?>
                    <p style="color: var(--muted);">No feedback submitted yet.</p>
                <?php } else { ?>
                    <div style="overflow:auto;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">ID</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Name</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Rating</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Approved</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Title</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Message</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Created</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) { ?>
                                    <tr>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo (int)$r['id']; ?></td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <?php echo htmlspecialchars($r['name'] ?? ''); ?>
                                            <?php if (!empty($r['company'])) { ?>
                                                <div style="color:var(--muted); font-size:0.85rem; margin-top:2px;">
                                                    <?php echo htmlspecialchars($r['company']); ?>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo (int)($r['rating'] ?? 5); ?>/5</td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <?php echo ((int)($r['is_approved'] ?? 0) === 1) ? 'Yes' : 'No'; ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($r['title'] ?? ''); ?></td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border); max-width:520px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?php echo htmlspecialchars($r['message'] ?? ''); ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <?php echo htmlspecialchars($r['created_at'] ?? ''); ?>
                                        </td>
                                        <td style="padding:10px; border-bottom:1px solid var(--border);">
                                            <form method="POST" style="display:inline; margin-right:6px;">
                                                <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                                                <input type="hidden" name="action" value="approve" />
                                                <?php if ((int)($r['is_approved'] ?? 0) === 1) { ?>
                                                    <input type="hidden" name="is_approved" value="0" />
                                                    <button type="submit" class="btn btn-reject" style="padding:8px 12px;">Reject</button>
                                                <?php } else { ?>
                                                    <input type="hidden" name="is_approved" value="1" />
                                                    <button type="submit" class="btn btn-approve" style="padding:8px 12px;">Approve</button>
                                                <?php } ?>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('Delete this feedback?');" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                                                <input type="hidden" name="action" value="delete" />
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
