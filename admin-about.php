<?php
include "backend/config.php";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS about_content (
    id INT PRIMARY KEY,
    hero_title VARCHAR(255) NOT NULL,
    hero_subtitle TEXT NOT NULL,
    story_title VARCHAR(255) NOT NULL,
    story_body MEDIUMTEXT NOT NULL,
    mission_title VARCHAR(255) NOT NULL,
    mission_body MEDIUMTEXT NOT NULL,
    stat_projects VARCHAR(50) NOT NULL,
    stat_satisfaction VARCHAR(50) NOT NULL,
    stat_clients VARCHAR(50) NOT NULL,
    stat_awards VARCHAR(50) NOT NULL,
    cta_title VARCHAR(255) NOT NULL,
    cta_text TEXT NOT NULL,
    cta_button_text VARCHAR(100) NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function sanitize_about_html($html) {
    $html = (string)$html;
    $html = str_replace("\0", "", $html);

    $allowed = '<b><strong><i><em><u><br><p><ul><ol><li><a>';
    $html = strip_tags($html, $allowed);

    $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html);
    $html = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $html);
    $html = preg_replace('/\sstyle\s*=\s*"[^"]*"/i', '', $html);
    $html = preg_replace("/\sstyle\s*=\s*'[^']*'/i", '', $html);

    $html = preg_replace_callback('/<a\s+[^>]*href\s*=\s*(["\'])(.*?)\1[^>]*>/i', function ($m) {
        $quote = $m[1];
        $href = trim($m[2]);
        if (preg_match('/^\s*javascript:/i', $href)) {
            $href = '#';
        }
        $href = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
        return '<a href=' . $quote . $href . $quote . '>';
    }, $html);

    return $html;
}

$defaults = [
    'hero_title' => 'About AKRINO Scedio',
    'hero_subtitle' => 'Transforming ideas into stunning visual experiences through innovative design and creative solutions.',
    'story_title' => 'From Humble Beginnings to Creative Excellence',
    'story_body' => '<p>Founded in 2020, AKRINO Scedio began as a small team of passionate designers with a shared vision to create meaningful and impactful designs. What started as a modest design studio has now grown into a full-service creative agency serving clients worldwide.</p>'
        . '<p>Our journey has been marked by dedication, innovation, and an unwavering commitment to excellence. We\'ve had the privilege of working with startups, established businesses, and everything in between, helping them bring their visions to life through exceptional design.</p>'
        . '<p>Today, we continue to push the boundaries of creativity, constantly evolving our skills and embracing new technologies to deliver cutting-edge design solutions that make a real difference.</p>',
    'mission_title' => 'Our Mission & Vision',
    'mission_body' => '<p>At AKRINO Scedio, our mission is to empower businesses through innovative design solutions that drive growth and create lasting impressions. We believe that great design is not just about aesthetics, but about solving problems and creating meaningful connections.</p>'
        . '<p>Our vision is to be recognized as a global leader in design and creativity, known for our commitment to excellence, innovation, and client satisfaction. We strive to push the boundaries of what\'s possible, constantly exploring new ideas and techniques to deliver exceptional results.</p>'
        . '<p>We\'re not just designers; we\'re storytellers, problem-solvers, and visionaries who are passionate about making a difference through our work.</p>',
    'stat_projects' => '150+',
    'stat_satisfaction' => '98%',
    'stat_clients' => '50+',
    'stat_awards' => '10+',
    'cta_title' => 'Ready to Start Your Project?',
    'cta_text' => "Let's work together to bring your ideas to life with our expert design services.",
    'cta_button_text' => 'Get in Touch'
];

mysqli_query($conn, "INSERT IGNORE INTO about_content (
    id, hero_title, hero_subtitle, story_title, story_body, mission_title, mission_body,
    stat_projects, stat_satisfaction, stat_clients, stat_awards,
    cta_title, cta_text, cta_button_text
) VALUES (
    1,
    '" . mysqli_real_escape_string($conn, $defaults['hero_title']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['hero_subtitle']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['story_title']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['story_body']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['mission_title']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['mission_body']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['stat_projects']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['stat_satisfaction']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['stat_clients']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['stat_awards']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['cta_title']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['cta_text']) . "',
    '" . mysqli_real_escape_string($conn, $defaults['cta_button_text']) . "'
)");

$save_ok = null;
$save_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_title = trim((string)($_POST['hero_title'] ?? ''));
    $hero_subtitle = trim((string)($_POST['hero_subtitle'] ?? ''));
    $story_title = trim((string)($_POST['story_title'] ?? ''));
    $story_body = sanitize_about_html($_POST['story_body'] ?? '');
    $mission_title = trim((string)($_POST['mission_title'] ?? ''));
    $mission_body = sanitize_about_html($_POST['mission_body'] ?? '');

    $stat_projects = trim((string)($_POST['stat_projects'] ?? ''));
    $stat_satisfaction = trim((string)($_POST['stat_satisfaction'] ?? ''));
    $stat_clients = trim((string)($_POST['stat_clients'] ?? ''));
    $stat_awards = trim((string)($_POST['stat_awards'] ?? ''));

    $cta_title = trim((string)($_POST['cta_title'] ?? ''));
    $cta_text = trim((string)($_POST['cta_text'] ?? ''));
    $cta_button_text = trim((string)($_POST['cta_button_text'] ?? ''));

    if ($hero_title === '' || $hero_subtitle === '' || $story_title === '' || $mission_title === '' || $cta_title === '' || $cta_text === '' || $cta_button_text === '') {
        $save_ok = false;
        $save_error = 'Please fill all required fields.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE about_content SET
            hero_title=?,
            hero_subtitle=?,
            story_title=?,
            story_body=?,
            mission_title=?,
            mission_body=?,
            stat_projects=?,
            stat_satisfaction=?,
            stat_clients=?,
            stat_awards=?,
            cta_title=?,
            cta_text=?,
            cta_button_text=?
            WHERE id=1
        ");

        if (!$stmt) {
            $save_ok = false;
            $save_error = 'Prepare failed: ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                'sssssssssssss',
                $hero_title,
                $hero_subtitle,
                $story_title,
                $story_body,
                $mission_title,
                $mission_body,
                $stat_projects,
                $stat_satisfaction,
                $stat_clients,
                $stat_awards,
                $cta_title,
                $cta_text,
                $cta_button_text
            );
            $ok = mysqli_stmt_execute($stmt);
            $err = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);

            if (!$ok) {
                $save_ok = false;
                $save_error = 'Save failed: ' . $err;
            } else {
                $save_ok = true;
            }
        }
    }
}

$row = $defaults;
$res = mysqli_query($conn, "SELECT * FROM about_content WHERE id=1 LIMIT 1");
if ($res) {
    $db = mysqli_fetch_assoc($res);
    if (is_array($db)) {
        foreach ($row as $k => $v) {
            if (array_key_exists($k, $db) && $db[$k] !== null) {
                $row[$k] = $db[$k];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin About - AKRINO Studio</title>
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
                <li class="active" data-section="about"><a href="admin-about.php"><i class="fas fa-circle-info"></i> <span>About Page</span></a></li>
                <li id="logoutBtn"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar"><i class="fas fa-bars"></i></button>
                <h1>About Page</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="recent-activity" style="margin-top:0;">
                <div class="section-header">
                    <h2>Edit About Content</h2>
                    <a href="about.php" target="_blank" rel="noopener noreferrer" class="view-all">View</a>
                </div>

                <?php if ($save_ok === true) { ?>
                    <div style="margin: 12px 0; padding: 12px 14px; border-radius: 12px; background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.25); color: #166534;">
                        Saved successfully.
                    </div>
                <?php } elseif ($save_ok === false) { ?>
                    <div style="margin: 12px 0; padding: 12px 14px; border-radius: 12px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); color: #7f1d1d;">
                        <?php echo htmlspecialchars($save_error); ?>
                    </div>
                <?php } ?>

                <form method="POST" style="display:grid; gap: 14px;">
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">Hero Title</label>
                        <input type="text" name="hero_title" value="<?php echo htmlspecialchars($row['hero_title']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required />
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">Hero Subtitle</label>
                        <textarea name="hero_subtitle" rows="3" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required><?php echo htmlspecialchars($row['hero_subtitle']); ?></textarea>
                    </div>

                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">Story Title</label>
                        <input type="text" name="story_title" value="<?php echo htmlspecialchars($row['story_title']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required />
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">Story Body (HTML allowed)</label>
                        <textarea name="story_body" rows="8" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required><?php echo htmlspecialchars($row['story_body']); ?></textarea>
                        <div style="margin-top:6px; color: var(--muted); font-size: 0.9rem; line-height: 1.4;">
                            Allowed: &lt;b&gt; &lt;strong&gt; &lt;i&gt; &lt;em&gt; &lt;u&gt; &lt;br&gt; &lt;p&gt; &lt;ul&gt; &lt;ol&gt; &lt;li&gt; &lt;a href=&quot;...&quot;&gt;
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">Mission Title</label>
                        <input type="text" name="mission_title" value="<?php echo htmlspecialchars($row['mission_title']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required />
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">Mission Body (HTML allowed)</label>
                        <textarea name="mission_body" rows="8" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required><?php echo htmlspecialchars($row['mission_body']); ?></textarea>
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:6px;">Projects</label>
                            <input type="text" name="stat_projects" value="<?php echo htmlspecialchars($row['stat_projects']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" />
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:6px;">Satisfaction</label>
                            <input type="text" name="stat_satisfaction" value="<?php echo htmlspecialchars($row['stat_satisfaction']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" />
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:6px;">Clients</label>
                            <input type="text" name="stat_clients" value="<?php echo htmlspecialchars($row['stat_clients']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" />
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:6px;">Awards</label>
                            <input type="text" name="stat_awards" value="<?php echo htmlspecialchars($row['stat_awards']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" />
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">CTA Title</label>
                        <input type="text" name="cta_title" value="<?php echo htmlspecialchars($row['cta_title']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required />
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">CTA Text</label>
                        <textarea name="cta_text" rows="3" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required><?php echo htmlspecialchars($row['cta_text']); ?></textarea>
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px;">CTA Button Text</label>
                        <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($row['cta_button_text']); ?>" style="width:100%; padding:12px 12px; border:1px solid var(--border); border-radius:12px;" required />
                    </div>

                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top: 6px;">
                        <button type="submit" class="btn" style="padding: 10px 14px;">Save</button>
                        <a href="admin-dashboard.html" class="btn btn-secondary" style="padding: 10px 14px; text-decoration:none; display:inline-flex; align-items:center;">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script src="js/admin-auth.js"></script>
<script src="js/admin-dashboard.js"></script>
</body>
</html>
