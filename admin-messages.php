<?php
header('Location: admin-inquiries.php');
exit;

include "backend/config.php";

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
    header('Location: admin-messages.php');
    exit;
}

$rows = [];
$res = mysqli_query($conn, "SELECT id, name, email, message, created_at FROM contact_messages ORDER BY id DESC");
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
    <title>Admin Messages - AKRINO Studio</title>
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
                <h1>Messages</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="quick-actions" style="margin-top:0;">
                <div class="section-header">
                    <h2>Contact Form Messages</h2>
                </div>

                <?php if (count($rows) === 0) { ?>
                    <p style="color: var(--muted);">No messages found.</p>
                <?php } else { ?>
                    <div style="overflow:auto;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">ID</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Name</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Email</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Message</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Created</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo (int)$r['id']; ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($r['name']); ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($r['email']); ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border); white-space:pre-wrap; min-width:280px;"><?php echo nl2br(htmlspecialchars($r['message'])); ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($r['created_at']); ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);">
                                        <form method="POST" onsubmit="return confirm('Delete this message?');" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo (int)$r['id']; ?>" />
                                            <button type="submit" class="btn btn-danger" style="padding:8px 12px;"><i class="fas fa-trash"></i> Delete</button>
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
