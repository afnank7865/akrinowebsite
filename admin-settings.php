<?php
include "backend/config.php";

header('Location: admin-dashboard.html');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Settings - AKRINO Studio</title>
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
                <li data-section="inquiries"><a href="admin-inquiries.php"><i class="fas fa-inbox"></i> <span>Inquiries</span></a></li>
                <li data-section="services"><a href="admin-services.php"><i class="fas fa-concierge-bell"></i> <span>Services</span></a></li>
                <li data-section="portfolio"><a href="admin-dashboard.php">Admin</a></li>
                <li data-section="about"><a href="admin-about.php"><i class="fas fa-circle-info"></i> <span>About Page</span></a></li>
                <li class="active" data-section="settings"><a href="admin-settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li id="logoutBtn"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar"><i class="fas fa-bars"></i></button>
                <h1>Settings</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="recent-activity" style="margin-top:0;">
                <div class="section-header">
                    <h2>Website Settings</h2>
                </div>
                <div style="color: var(--muted); line-height: 1.6;">
                    Settings page is working now. If you want, I can add options here like:
                    <br>- Website name
                    <br>- Contact phone/email
                    <br>- Social links
                    <br>- Theme options
                </div>
            </div>
        </div>
    </main>
</div>

<script src="js/admin-auth.js"></script>
<script src="js/admin-dashboard.js"></script>
</body>
</html>
