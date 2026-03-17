<?php
include '../connection/db.php';

// Fetch top 3 users by total score
$query = "
    SELECT u.name, SUM(qh.score) AS total_score
    FROM users u
    JOIN quiz_history qh ON u.id = qh.user_id
    GROUP BY u.id
    ORDER BY total_score DESC
    LIMIT 3
";

$result = mysqli_query($conn, $query);

$topPlayers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $topPlayers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Ensures proper scaling on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Main stylesheet -->
    <link rel="stylesheet" href="../css/styles.css">
    <title>Quiz Master</title>

    <!-- AOS (Animate On Scroll) library for scroll animations -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../images/download.png">

    <!-- Font Awesome CDN for social icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts: Material Symbols for icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_right_alt" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
    <header class="nav">
        <div class="nav-inner">
            <a href="./index.php" class="brand">
                <img src="../images/download.png" alt="Quiz Master Logo" class="logo-img">
                <span>Quiz Master</span>
            </a>
            <ul class="menu">
                <li><a href="./index.php" id="links">Home</a></li>
                <li><a href="./about.html" id="links">About</a></li>
                <li><a href="./contact.html" id="links">Contact</a></li>
                <button class="nav-btn"><a href="./login.php" class="btn">Login</a></button>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="hero_section">
        <div class="image_section">
            <!-- CTA -->
            <div class="cta_section">
                <h1>Challenge Your Mind</h1>
                <p>Compete & Grow Smarter.</p>
                <a href="./login.php" class="cta_btn">
                    Take The Challenge <span class="material-symbols-outlined">arrow_right_alt</span>
                </a>
            </div>

            <!-- Background image -->
            <!-- <img src="../images/home_background.png" alt="image_section"> -->
            <img src="https://i.pinimg.com/1200x/a9/4c/0c/a94c0cc1b0f186f69baaa9fd06b9833a.jpg" alt="">
        </div>

        <!-- Features -->
        <section class="features" data-aos="fade-up">
            <h2 class="features-title">Why Choose Quiz Master?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon"><i class="fa-solid fa-brain"></i></span>
                    <h3>Smart Learning</h3>
                    <p>Track your progress and improve with every quiz.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon"><i class="fa-solid fa-earth-americas"></i></span>
                    <h3>Global Competition</h3>
                    <p>Compete with friends and players worldwide.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon"><i class="fa-solid fa-chart-area"></i></span>
                    <h3>Instant Feedback</h3>
                    <p>Get results and explanations right after each question.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon"><i class="fa-solid fa-palette"></i></span>
                    <h3>Fun & Interactive</h3>
                    <p>Engaging visuals, animations, and responsive layouts.</p>
                </div>
            </div>
        </section>

        <!-- LeaderBoard -->
        <section class="leaderboard" style="display: flex;flex-direction:column;justify-content:center;align-items:center;" data-aos="fade-up">
            <h2 class="leaderboard-title">🏆 Top Players</h2>
            <br>
            <div class="leaderboard-grid">
                <?php if (!empty($topPlayers)): ?>
                    <?php foreach ($topPlayers as $index => $player): ?>
                        <div class="leaderboard-card <?= $index == 0 ? 'first' : ($index == 1 ? 'second' : ($index == 2 ? 'third' : '')) ?>">
                            <h3><?= htmlspecialchars($player['name']) ?></h3>
                            <p>Score: <?= $player['total_score'] ?> pts</p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-leaderboard">No leaderboard yet — be the first to play!</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Quiz Categories -->
        <section class="quiz-categories" data-aos="fade-up">
            <h2 class="categories-title">🎯 Quiz Categories</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <span class="category-icon"><i class="fa-solid fa-flask-vial"></i></span>
                    <h3>Science</h3>
                    <p>Explore physics, chemistry, biology, and more.</p>
                </div>
                <div class="category-card">
                    <span class="category-icon"><i class="fa-solid fa-clipboard"></i></span>
                    <h3>History</h3>
                    <p>Test your knowledge of world events and leaders.</p>
                </div>
                <div class="category-card">
                    <span class="category-icon"><i class="fa-solid fa-clapperboard"></i></span>
                    <h3>Pop Culture</h3>
                    <p>Movies, music, celebrities, and trending topics.</p>
                </div>
                <div class="category-card">
                    <span class="category-icon"><i class="fa-solid fa-brain"></i></span>
                    <h3>Logic & Riddles</h3>
                    <p>Challenge your brain with puzzles and riddles.</p>
                </div>
            </div>
        </section>

        <!-- FAQs -->
        <section class="faq" data-aos="fade-up">
            <h2 class="faq-title">❓ Frequently Asked Questions</h2>
            <div class="faq-item">
                <button class="faq-question">
                    Is Quiz Master free?
                    <span class="material-symbols-outlined arrow">expand_more</span>
                </button>
                <div class="faq-answer">
                    <p>Yes, Quiz Master is completely free to use. You can play quizzes without any cost.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    Do I need to sign up?
                    <span class="material-symbols-outlined arrow">expand_more</span>
                </button>
                <div class="faq-answer">
                    <p><strong>Yes, it is necessary to play</strong> because it allows Quiz Master to track your
                        progress, save your scores, and connect you to the global leaderboard for fair competition.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    How are scores calculated?
                    <span class="material-symbols-outlined arrow">expand_more</span>
                </button>
                <div class="faq-answer">
                    <p>Scores are based on correct answers and speed. The faster and more accurate you are, the higher
                        your score.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    Can I play on mobile?
                    <span class="material-symbols-outlined arrow">expand_more</span>
                </button>
                <div class="faq-answer">
                    <p>Yes! Quiz Master is fully responsive and works smoothly on mobile, tablet, and desktop devices.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <strong>Quiz Master</strong>
                <p class="tagline">Challenge your mind, have fun!</p>
            </div>

            <div class="footer-right">
                <a href="https://facebook.com" target="_blank" id="fb"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com" target="_blank" id="insta"><i class="fab fa-instagram"></i></a>
                <a href="https://twitter.com" target="_blank" id="twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://linkedin.com" target="_blank" id="linkedin"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 QuizMaster. All rights reserved.</p>
        </div>
    </footer>

    <script src="../js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</body>

</html>