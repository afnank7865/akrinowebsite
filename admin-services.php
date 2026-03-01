<?php
include "backend/config.php";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon_class VARCHAR(100) DEFAULT NULL,
    gradient_css VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function servicesEnsureColumn(mysqli $conn, string $table, string $column, string $definition): void {
    $tableEsc = mysqli_real_escape_string($conn, $table);
    $colEsc = mysqli_real_escape_string($conn, $column);
    $existsSql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$tableEsc}' AND COLUMN_NAME = '{$colEsc}' LIMIT 1";
    $existsRes = mysqli_query($conn, $existsSql);
    if ($existsRes && mysqli_fetch_row($existsRes)) {
        return;
    }
    mysqli_query($conn, "ALTER TABLE `{$tableEsc}` ADD COLUMN `{$colEsc}` {$definition}");
}

servicesEnsureColumn($conn, 'services', 'icon_class', 'VARCHAR(100) DEFAULT NULL');
servicesEnsureColumn($conn, 'services', 'gradient_css', 'VARCHAR(255) DEFAULT NULL');
servicesEnsureColumn($conn, 'services', 'is_active', 'TINYINT(1) NOT NULL DEFAULT 1');

$legacyIconExists = mysqli_query($conn, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='services' AND COLUMN_NAME='icon' LIMIT 1");
if ($legacyIconExists && mysqli_fetch_row($legacyIconExists)) {
    mysqli_query($conn, "UPDATE services SET icon_class = icon WHERE (icon_class IS NULL OR icon_class='') AND icon IS NOT NULL AND icon<>''");
}

$legacyStatusExists = mysqli_query($conn, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='services' AND COLUMN_NAME='status' LIMIT 1");
if ($legacyStatusExists && mysqli_fetch_row($legacyStatusExists)) {
    mysqli_query($conn, "UPDATE services SET is_active = CASE WHEN status IN (1,'1','active','Active','ACTIVE') THEN 1 ELSE 0 END WHERE is_active IS NULL");
}

$countRes = mysqli_query($conn, "SELECT COUNT(*) AS c FROM services");
$countRow = $countRes ? mysqli_fetch_assoc($countRes) : null;
$serviceCount = $countRow ? (int)$countRow['c'] : 0;

if ($serviceCount === 0) {
    $defaults = [
        [
            'Graphic Design',
            "From eye-catching logos to stunning brochures and business cards, we create visual content that captures attention and communicates your brand's message effectively.",
            'fas fa-paint-brush',
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        ],
        [
            'Branding',
            'Build a powerful brand identity that resonates with your audience. We create complete brand identity packages including logos, color schemes, and brand guidelines.',
            'fas fa-palette',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        ],
        [
            'Web Design',
            'Create stunning, responsive websites that engage visitors and drive conversions. Our designs combine beautiful aesthetics with seamless functionality.',
            'fas fa-laptop-code',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        ],
        [
            'Application Design',
            'We design modern, user-friendly mobile and web applications with clean layouts, clear flows, and engaging interfaces that improve usability and retention.',
            'fas fa-mobile-alt',
            'linear-gradient(135deg, #34d399 0%, #10b981 100%)',
        ],
        [
            'UI/UX Design',
            'From wireframes to polished UI systems, we craft intuitive user experiences and visually consistent interfaces that align with your brand and business goals.',
            'fas fa-bezier-curve',
            'linear-gradient(135deg, #b48cff 0%, #4a90e2 100%)',
        ],
        [
            'Custom Solutions',
            'Every business is unique. We offer tailored design services for UI/UX, social media graphics, packaging design, and any creative challenge you face.',
            'fas fa-lightbulb',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        ],
    ];

    $stmt = mysqli_prepare($conn, "INSERT INTO services (title, description, icon_class, gradient_css, is_active) VALUES (?, ?, ?, ?, 1)");
    if ($stmt) {
        foreach ($defaults as $d) {
            mysqli_stmt_bind_param($stmt, 'ssss', $d[0], $d[1], $d[2], $d[3]);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

$editId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$editRow = null;
if ($editId > 0) {
    $res = mysqli_query($conn, "SELECT * FROM services WHERE id=$editId");
    if ($res && mysqli_num_rows($res) > 0) {
        $editRow = mysqli_fetch_assoc($res);
    }
}

$rows = [];
$res = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Services - AKRINO Studio</title>
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
                <li class="active" data-section="services"><a href="admin-services.php"><i class="fas fa-concierge-bell"></i> <span>Services</span></a></li>
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
                <h1>Services</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="recent-activity" style="margin-top:0;">
                <div class="section-header">
                    <h2><?php echo $editRow ? 'Edit Service' : 'Add Service'; ?></h2>
                    <a href="admin-services.php" class="view-all" style="text-decoration:none;">Reset</a>
                </div>

                <form class="notification-form" method="POST" action="backend/services_crud.php">
                    <input type="hidden" name="action" value="<?php echo $editRow ? 'update' : 'create'; ?>">
                    <?php if ($editRow) { ?>
                        <input type="hidden" name="id" value="<?php echo (int)$editRow['id']; ?>">
                    <?php } ?>

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($editRow['title'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required><?php echo htmlspecialchars($editRow['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Icon Class (FontAwesome)</label>
                        <input type="text" id="icon_class" name="icon_class" value="<?php echo htmlspecialchars($editRow['icon_class'] ?? ''); ?>" placeholder="e.g. fas fa-paint-brush">
                        <div style="margin-top:10px; display:grid; grid-template-columns: 1fr 1fr; gap:12px; align-items:end;">
                            <div>
                                <label style="display:block; margin-bottom:6px;">Icon Preset</label>
                                <select id="iconPreset" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:10px; background:var(--card); color:var(--text);">
                                    <option value="">Select icon</option>
                                    <option value="fas fa-paint-brush">Paint Brush</option>
                                    <option value="fas fa-palette">Palette</option>
                                    <option value="fas fa-laptop-code">Laptop Code</option>
                                    <option value="fas fa-mobile-alt">Mobile</option>
                                    <option value="fas fa-bezier-curve">Bezier</option>
                                    <option value="fas fa-lightbulb">Lightbulb</option>
                                    <option value="fas fa-camera">Camera</option>
                                    <option value="fas fa-video">Video</option>
                                    <option value="fas fa-bullhorn">Bullhorn</option>
                                    <option value="fas fa-chart-line">Chart</option>
                                    <option value="fas fa-code">Code</option>
                                    <option value="fas fa-pen-nib">Pen</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:6px;">Icon Preview</label>
                                <div style="height:48px; border-radius:12px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; background:var(--card);">
                                    <i id="iconPreview" class="fas fa-star" style="font-size:20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Gradient CSS</label>
                        <input type="text" id="gradient_css" name="gradient_css" value="<?php echo htmlspecialchars($editRow['gradient_css'] ?? ''); ?>" placeholder="e.g. linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                        <div style="margin-top:10px; display:grid; grid-template-columns: 1fr 1fr; gap:12px; align-items:end;">
                            <div>
                                <label style="display:block; margin-bottom:6px;">Gradient Preset</label>
                                <select id="gradientPreset" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:10px; background:var(--card); color:var(--text);">
                                    <option value="">Select preset</option>
                                    <option value="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">Purple Blue</option>
                                    <option value="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">Pink Red</option>
                                    <option value="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">Sky Cyan</option>
                                    <option value="linear-gradient(135deg, #34d399 0%, #10b981 100%)">Green Mint</option>
                                    <option value="linear-gradient(135deg, #fa709a 0%, #fee140 100%)">Pink Yellow</option>
                                    <option value="linear-gradient(135deg, #111827 0%, #334155 100%)">Dark Slate</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:6px;">Live Preview</label>
                                <div id="gradientPreview" style="height:48px; border-radius:12px; border:1px solid var(--border);"></div>
                            </div>
                            <div style="display:flex; gap:12px; align-items:end;">
                                <div style="flex:1;">
                                    <label style="display:block; margin-bottom:6px;">Color 1</label>
                                    <input id="gradientColor1" type="color" value="#667eea" style="width:100%; height:44px; padding:0; border:1px solid var(--border); border-radius:10px; background:var(--card);">
                                </div>
                                <div style="flex:1;">
                                    <label style="display:block; margin-bottom:6px;">Color 2</label>
                                    <input id="gradientColor2" type="color" value="#764ba2" style="width:100%; height:44px; padding:0; border:1px solid var(--border); border-radius:10px; background:var(--card);">
                                </div>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:6px;">Angle</label>
                                <input id="gradientAngle" type="number" value="135" min="0" max="360" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:10px; background:var(--card); color:var(--text);" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo (($editRow['is_active'] ?? 1) ? 'checked' : ''); ?>>
                        <label for="is_active" style="margin:0;">Active on website</label>
                    </div>

                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editRow ? 'Update' : 'Create'; ?></button>
                        <a href="admin-dashboard.html" class="btn btn-secondary" style="text-decoration:none;"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>

            <div class="quick-actions">
                <div class="section-header">
                    <h2>Existing Services</h2>
                </div>

                <?php if (count($rows) === 0) { ?>
                    <p style="color: var(--muted);">No services found.</p>
                <?php } else { ?>
                    <div style="overflow:auto;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">ID</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Title</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Active</th>
                                    <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo (int)$r['id']; ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($r['title']); ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border);"><?php echo ((int)$r['is_active'] === 1) ? 'Yes' : 'No'; ?></td>
                                    <td style="padding:10px; border-bottom:1px solid var(--border); display:flex; gap:10px; flex-wrap:wrap;">
                                        <a class="btn btn-primary" style="text-decoration:none; padding:8px 12px;" href="admin-services.php?edit=<?php echo (int)$r['id']; ?>"><i class="fas fa-pen"></i> Edit</a>
                                        <form method="POST" action="backend/services_crud.php" style="display:inline;">
                                            <input type="hidden" name="action" value="toggle_active">
                                            <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                                            <input type="hidden" name="to" value="<?php echo ((int)$r['is_active'] === 1) ? 0 : 1; ?>">
                                            <button type="submit" class="btn btn-secondary" style="padding:8px 12px;">
                                                <?php if ((int)$r['is_active'] === 1) { ?>
                                                    <i class="fas fa-eye-slash"></i> Deactivate
                                                <?php } else { ?>
                                                    <i class="fas fa-eye"></i> Activate
                                                <?php } ?>
                                            </button>
                                        </form>
                                        <form method="POST" action="backend/services_crud.php" onsubmit="return confirm('Delete this service?');" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
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
<script>
    (function(){
        const iconInput = document.getElementById('icon_class');
        const iconPreset = document.getElementById('iconPreset');
        const iconPreview = document.getElementById('iconPreview');

        const input = document.getElementById('gradient_css');
        const preset = document.getElementById('gradientPreset');
        const preview = document.getElementById('gradientPreview');
        const c1 = document.getElementById('gradientColor1');
        const c2 = document.getElementById('gradientColor2');
        const angle = document.getElementById('gradientAngle');

        function setIconPreview(value) {
            if (!iconPreview) return;
            const cls = (value && String(value).trim()) ? String(value).trim() : 'fas fa-star';
            iconPreview.className = cls;
        }

        if (iconPreset && iconInput) {
            iconPreset.addEventListener('change', function(){
                if (!this.value) return;
                iconInput.value = this.value;
                setIconPreview(this.value);
            });
        }

        if (iconInput) {
            iconInput.addEventListener('input', function(){
                setIconPreview(this.value);
            });
            setIconPreview(iconInput.value);
        }

        if (!input || !preset || !preview || !c1 || !c2 || !angle) return;

        function setPreview(value){
            preview.style.background = value && value.trim() ? value : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }

        function buildGradient(){
            const a = Number(angle.value || 135);
            return `linear-gradient(${a}deg, ${c1.value} 0%, ${c2.value} 100%)`;
        }

        function tryParseExisting(){
            const v = (input.value || '').trim();
            setPreview(v);
            const m = v.match(/linear-gradient\((\s*[-\d.]+)deg\s*,\s*(#[0-9a-fA-F]{3,8})\s*0%\s*,\s*(#[0-9a-fA-F]{3,8})\s*100%\s*\)/);
            if (m) {
                angle.value = String(parseInt(m[1], 10));
                c1.value = m[2];
                c2.value = m[3];
            }
        }

        preset.addEventListener('change', function(){
            if (!this.value) return;
            input.value = this.value;
            tryParseExisting();
        });

        function updateFromPickers(){
            const g = buildGradient();
            input.value = g;
            setPreview(g);
        }

        c1.addEventListener('input', updateFromPickers);
        c2.addEventListener('input', updateFromPickers);
        angle.addEventListener('input', updateFromPickers);

        input.addEventListener('input', function(){
            setPreview(this.value);
        });

        tryParseExisting();
    })();
</script>
</body>
</html>
