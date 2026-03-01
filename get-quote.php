<?php
require_once __DIR__ . '/backend/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Get A Quote - AKRINO Scedio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body class="light-theme">

<header>
    <nav class="navbar">
        <div class="logo">AKRINO Scedio</div>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.html#home">Home</a></li>
            <li><a href="index.html#services">Services</a></li>
            <li><a href="index.html#testimonials">Testimonials</a></li>
            <li><a href="portfolio.php">Portfolio</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="get-quote.php">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <button class="hamburger nav-icon" id="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="navLinks">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <button class="theme-toggle nav-icon" id="themeToggle" aria-label="Toggle dark mode">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>
</header>

<main>
<section id="contact" class="contact-section" style="
    padding:100px 20px;
    background:var(--bg-color);
">

<div style="max-width:1100px; margin:0 auto;">

    <div style="text-align:center; margin-bottom:60px;">
        <h2 style="
            font-size:3rem;
            color:var(--text-color);
            font-weight:600;
        ">Get A Quote</h2>

        <div style="
            width:90px;
            height:4px;
            background:#b48cff;
            margin:15px auto 0;
            border-radius:5px;
        "></div>
    </div>

    <div class="contact-container" style="
        background:var(--card-bg);
        padding:60px;
        border-radius:15px;
        box-shadow:0 25px 60px rgba(0,0,0,0.06);
    ">

       <form id="contactForm" action="backend/contact_submit.php" method="POST" style="display:flex; flex-direction:column; gap:30px;">

            <div class="contact-grid" style="
                display:grid;
                grid-template-columns:1fr 1fr;
                gap:40px;
            ">

                <div>
                    <label style="font-weight:600; color:#555;">Name</label>
                    <input type="text" id="name" name="name" required
                        style="
                        width:100%;
                        padding:16px;
                        margin-top:10px;
                        border:none;
                        border-bottom:2px solid #ddd;
                        font-size:16px;
                        outline:none;
                        transition:0.3s;
                    ">
                </div>

                <div>
                    <label style="font-weight:600; color:#555;">Email</label>
                    <input type="email" id="email" name="email" required
                        style="
                        width:100%;
                        padding:16px;
                        margin-top:10px;
                        border:none;
                        border-bottom:2px solid #ddd;
                        font-size:16px;
                        outline:none;
                        transition:0.3s;
                    ">
                </div>

            </div>

            <div>
                <label style="font-weight:600; color:#555;">Service</label>
                <select id="service" name="service" required
                    style="
                    width:100%;
                    padding:16px;
                    margin-top:10px;
                    border:none;
                    border-bottom:2px solid #ddd;
                    font-size:16px;
                    outline:none;
                    background:white;
                ">
                    <option value="">Select a service</option>
                    <option value="Product Design">Product Design</option>
                    <option value="Brand Card">Brand Card</option>
                    <option value="Catalog Design">Catalog Design</option>
                    <option value="Branding">Branding</option>
                    <option value="Mock Design">Mock Design</option>
                    <option value="Graphic Design">Graphic Design</option>
                    <option value="Application Design">Application Design</option>
                    <option value="Website Design">Website Design</option>
                </select>
            </div>

            <div>
                <label style="font-weight:600; color:#555;">Message</label>
                <textarea id="message" name="message" required
                    style="
                    width:100%;
                    padding:16px;
                    margin-top:10px;
                    border:none;
                    border-bottom:2px solid #ddd;
                    font-size:16px;
                    min-height:140px;
                    resize:none;
                    outline:none;
                "></textarea>
            </div>

            <button type="submit"
                style="
                    margin-top:20px;
                    padding:18px;
                    font-size:18px;
                    border:none;
                    border-radius:50px;
                    background:linear-gradient(90deg,#b48cff,#7c5cff);
                    color:white;
                    cursor:pointer;
                    transition:0.3s;
                "
                onmouseover="this.style.opacity='0.85'"
                onmouseout="this.style.opacity='1'">

                Send Message

            </button>

        </form>

    </div>

</div>
</section>
</main>

<?php include "footer.php"; ?>

<script src="js/shared.js?v=20260224"></script>
<script src="js/main.js?v=20260221"></script>

</body>
</html>
