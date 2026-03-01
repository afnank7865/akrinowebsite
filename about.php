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

function aboutEnsureColumn(mysqli $conn, string $table, string $column, string $definition): void {
    $tableEsc = mysqli_real_escape_string($conn, $table);
    $colEsc = mysqli_real_escape_string($conn, $column);
    $existsSql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$tableEsc}' AND COLUMN_NAME = '{$colEsc}' LIMIT 1";
    $existsRes = mysqli_query($conn, $existsSql);
    if ($existsRes && mysqli_fetch_row($existsRes)) {
        return;
    }

    mysqli_query($conn, "ALTER TABLE `{$tableEsc}` ADD COLUMN `{$colEsc}` {$definition}");
}

aboutEnsureColumn($conn, 'about_content', 'hero_title', 'VARCHAR(255) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'hero_subtitle', 'TEXT NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'story_title', 'VARCHAR(255) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'story_body', 'MEDIUMTEXT NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'mission_title', 'VARCHAR(255) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'mission_body', 'MEDIUMTEXT NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'stat_projects', 'VARCHAR(50) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'stat_satisfaction', 'VARCHAR(50) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'stat_clients', 'VARCHAR(50) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'stat_awards', 'VARCHAR(50) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'cta_title', 'VARCHAR(255) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'cta_text', 'TEXT NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'cta_button_text', 'VARCHAR(100) NOT NULL');
aboutEnsureColumn($conn, 'about_content', 'updated_at', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

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

mysqli_query($conn, "UPDATE about_content SET
    hero_title = IF(hero_title IS NULL OR hero_title='', '" . mysqli_real_escape_string($conn, $defaults['hero_title']) . "', hero_title),
    hero_subtitle = IF(hero_subtitle IS NULL OR hero_subtitle='', '" . mysqli_real_escape_string($conn, $defaults['hero_subtitle']) . "', hero_subtitle),
    story_title = IF(story_title IS NULL OR story_title='', '" . mysqli_real_escape_string($conn, $defaults['story_title']) . "', story_title),
    story_body = IF(story_body IS NULL OR story_body='', '" . mysqli_real_escape_string($conn, $defaults['story_body']) . "', story_body),
    mission_title = IF(mission_title IS NULL OR mission_title='', '" . mysqli_real_escape_string($conn, $defaults['mission_title']) . "', mission_title),
    mission_body = IF(mission_body IS NULL OR mission_body='', '" . mysqli_real_escape_string($conn, $defaults['mission_body']) . "', mission_body),
    stat_projects = IF(stat_projects IS NULL OR stat_projects='', '" . mysqli_real_escape_string($conn, $defaults['stat_projects']) . "', stat_projects),
    stat_satisfaction = IF(stat_satisfaction IS NULL OR stat_satisfaction='', '" . mysqli_real_escape_string($conn, $defaults['stat_satisfaction']) . "', stat_satisfaction),
    stat_clients = IF(stat_clients IS NULL OR stat_clients='', '" . mysqli_real_escape_string($conn, $defaults['stat_clients']) . "', stat_clients),
    stat_awards = IF(stat_awards IS NULL OR stat_awards='', '" . mysqli_real_escape_string($conn, $defaults['stat_awards']) . "', stat_awards),
    cta_title = IF(cta_title IS NULL OR cta_title='', '" . mysqli_real_escape_string($conn, $defaults['cta_title']) . "', cta_title),
    cta_text = IF(cta_text IS NULL OR cta_text='', '" . mysqli_real_escape_string($conn, $defaults['cta_text']) . "', cta_text),
    cta_button_text = IF(cta_button_text IS NULL OR cta_button_text='', '" . mysqli_real_escape_string($conn, $defaults['cta_button_text']) . "', cta_button_text)
    WHERE id=1");

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AKRINO Scedio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        .fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        .about-hero {
            background: url('img/about.jpeg') center/cover no-repeat;
            color: white;
            padding: 100px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.60), rgba(0, 0, 0, 0.72));
            z-index: 1;
        }

        .about-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            position: relative;
            z-index: 2;
            color: #ffffff !important;
            text-shadow: 0 6px 18px rgba(0,0,0,0.55);
        }

        .about-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
            opacity: 0.9;
            line-height: 1.6;
            position: relative;
            z-index: 2;
            color: rgba(255,255,255,0.95) !important;
            text-shadow: 0 4px 14px rgba(0,0,0,0.45);
        }

        .about-section {
            padding: 80px 0;
            background-color: var(--bg-color);
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--text-color);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .section-title .title-divider {
            width: 80px;
            height: 4px;
            background: #b48cff;
            margin: 0 auto;
            border: none;
        }

        .about-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 60px;
        }

        .about-text {
            flex: 1;
            min-width: 300px;
            padding: 0 30px;
        }

        .about-text h3 {
            font-size: 2rem;
            color: var(--text-color);
            margin-bottom: 20px;
        }

        .about-text p {
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 1.1rem;
            text-align: justify;
            text-justify: inter-word;
        }

        .about-text h3 {
            text-align: center;
        }

        .about-text {
            flex: 1;
            min-width: 100%;
            padding: 0 30px;
            text-align: left;
            max-width: 980px;
            margin: 0 auto;
        }

        .about-text a {
            color: var(--primary-color);
        }

        .notification-bell,
        .notification-count,
        #notificationBell {
            display: none !important;
        }

        .stats-section {
            background: linear-gradient(135deg, #b48cff, #6a5acd);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .stat-item {
            flex: 1;
            min-width: 200px;
            margin: 20px;
            padding: 30px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-10px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: block;
            transition: transform 0.3s ease;
        }
        
        .stat-item:hover .stat-number {
            transform: scale(1.1);
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }


        @media (max-width: 768px) {
            .about-content {
                flex-direction: column;
            }
            
            .about-text, .about-image {
                padding: 0 15px 30px;
            }
            
            .about-hero h1 {
                font-size: 2.5rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include "navbar.php"; ?>
    </header>

    <section class="about-hero">
        <div class="about-container">
            <h1 data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($row['hero_title']); ?></h1>
            <p data-aos="fade-up" data-aos-delay="200"><?php echo htmlspecialchars($row['hero_subtitle']); ?></p>
        </div>
    </section>

    <section class="about-section">
        <div class="about-container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our Story</h2>
                <div class="title-divider"></div>
            </div>
            <div class="about-content">
                <div class="about-text" style="flex: 1 100%;" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fade-in-up delay-100"><?php echo htmlspecialchars($row['story_title']); ?></h3>
                    <div class="fade-in-up delay-200"><?php echo $row['story_body']; ?></div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="about-container">
            <div class="section-title" data-aos="fade-up">
                <h2><?php echo htmlspecialchars($row['mission_title']); ?></h2>
                <div class="title-divider"></div>
            </div>
            <div class="about-content">
                <div class="about-text" style="flex: 1 100%;" data-aos="fade-up" data-aos-delay="100">
                    <div class="fade-in-up delay-100">
                        <?php echo $row['mission_body']; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                <span class="stat-number animate-pulse"><?php echo htmlspecialchars($row['stat_projects']); ?></span>
                <span class="stat-label">Projects Completed</span>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                <span class="stat-number animate-pulse"><?php echo htmlspecialchars($row['stat_satisfaction']); ?></span>
                <span class="stat-label">Client Satisfaction</span>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                <span class="stat-number animate-pulse"><?php echo htmlspecialchars($row['stat_clients']); ?></span>
                <span class="stat-label">Happy Clients</span>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="400">
                <span class="stat-number animate-pulse"><?php echo htmlspecialchars($row['stat_awards']); ?></span>
                <span class="stat-label">Awards Won</span>
            </div>
        </div>
    </section>

    <section class="about-section" style="background-color: #f9fbfe; text-align: center; padding: 80px 0;">
        <div class="about-container">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px; color: #2c3e50;"><?php echo htmlspecialchars($row['cta_title']); ?></h2>
            <p style="font-size: 1.2rem; color: #666; max-width: 700px; margin: 0 auto 30px;"><?php echo htmlspecialchars($row['cta_text']); ?></p>
            <a href="get-quote.php" class="btn" style="display: inline-block; background: #4a90e2; color: white; padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease;"><?php echo htmlspecialchars($row['cta_button_text']); ?></a>
        </div>
    </section>

    <?php include "footer.php"; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
        });
    </script>
    <script src="js/shared.js?v=20260224b"></script>
    <script src="js/main.js"></script>
</body>
</html>
