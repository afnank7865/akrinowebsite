<?php
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Activity - AKRINO Studio</title>
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
                <li data-section="portfolio"><a href="admin-dashboard.php"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a></li>
                <li class="active" data-section="activity"><a href="admin-activity.php"><i class="fas fa-clock"></i> <span>Activity</span></a></li>
                <li data-section="about"><a href="admin-about.php"><i class="fas fa-circle-info"></i> <span>About Page</span></a></li>
                <li id="logoutBtn"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar"><i class="fas fa-bars"></i></button>
                <h1>Activity</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="recent-activity" style="margin-top:0;">
                <div class="section-header">
                    <h2 id="activityTitle">All Activity</h2>
                </div>
                <div class="activity-list" id="allActivity"></div>
            </div>
        </div>
    </main>
</div>

<script src="js/admin-auth.js"></script>
<script src="js/admin-dashboard.js"></script>
<script>
    (function(){
        const titleEl = document.getElementById('activityTitle');
        const selectedDate = new URLSearchParams(window.location.search).get('date');
        if (titleEl && selectedDate) {
            titleEl.textContent = 'Activity - ' + selectedDate;
        }

        const el = document.getElementById('allActivity');
        if (!el) return;

        const url = new URL('backend/get_activity.php', window.location.href);
        url.searchParams.set('limit', '200');
        if (selectedDate) {
            url.searchParams.set('date', selectedDate);
        }
        url.searchParams.set('_', String(Date.now()));

        fetch(url.toString(), { cache: 'no-store' })
            .then(r => r.json())
            .then(rows => {
                if (!Array.isArray(rows) || rows.length === 0) {
                    el.innerHTML = '<div style="color:#64748b; padding:8px 2px;">No activity yet.</div>';
                    return;
                }

                el.innerHTML = '';
                rows.forEach(row => {
                    const item = document.createElement('div');
                    item.style.display = 'flex';
                    item.style.alignItems = 'center';
                    item.style.justifyContent = 'space-between';
                    item.style.gap = '12px';
                    item.style.padding = '10px 8px';
                    item.style.borderBottom = '1px solid rgba(148, 163, 184, 0.35)';

                    const left = document.createElement('div');
                    left.style.display = 'flex';
                    left.style.flexDirection = 'column';
                    left.style.gap = '3px';

                    const title = document.createElement('div');
                    title.style.fontWeight = '700';
                    title.style.color = '#0f172a';
                    title.textContent = row.title || row.type || 'Activity';

                    const desc = document.createElement('div');
                    desc.style.color = '#64748b';
                    desc.style.fontSize = '0.92rem';
                    desc.textContent = row.description || '';

                    left.appendChild(title);
                    left.appendChild(desc);

                    const right = document.createElement('div');
                    right.style.color = '#64748b';
                    right.style.fontSize = '0.85rem';
                    right.textContent = row.occurred_at ? new Date(row.occurred_at).toLocaleString() : '';

                    item.appendChild(left);
                    item.appendChild(right);
                    el.appendChild(item);
                });
            })
            .catch(() => {
                el.innerHTML = '<div style="color:#ef4444; padding:8px 2px;">Failed to load activity.</div>';
            });
    })();
</script>
</body>
</html>
