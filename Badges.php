<?php
session_start();

require_once __DIR__ . '/Classes/User.php';
require_once __DIR__ . '/Classes/Database.php';

$user = new User();

if (!isset($_SESSION['UserID'])) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION['UserID'];
$userData = $user->retrieveUserDataWithId($userId);

if ($userData) {
    $FirstName = $userData['FirstName'];
    $LastName = $userData['LastName'];
    $TotalPoints = $userData['TotalPoints'];
} else {
    echo "User data not found.";
    exit();
}

// Fetch user predictions count
$db = Database::getInstance();
$pdo = $db->getConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM userprediction WHERE UserID = ?");
$stmt->execute([$userId]);
$predictionCount = intval($stmt->fetchColumn());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - PredictKing</title>
    <link rel="icon" type="image/png" href="src/Screenshot 2025-02-10 153127.png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e1e2f;
            --secondary: #8d99ae;
            --accent: #ef233c;
            --accent-glow: rgba(239, 35, 60, 0.4);
            --background: #0f0f1a;
            --card-bg: #1a1a2e;
            --text: #ffffff;
            --text-secondary: #a0aec0;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            
            /* Badge Theme Colors */
            --gold: #ffd700;
            --gold-glow: rgba(255, 215, 0, 0.3);
            --silver: #c0c0c0;
            --silver-glow: rgba(192, 192, 192, 0.3);
            --bronze: #cd7f32;
            --bronze-glow: rgba(205, 127, 50, 0.3);
            --platinum: #e5e4e2;
            --platinum-glow: rgba(229, 228, 226, 0.3);
            --fire: #ff4500;
            --fire-glow: rgba(255, 69, 0, 0.3);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', -apple-system, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Header Navigation */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(26, 26, 46, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
        }

        .nav {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: none;
        }

        .nav-logo span {
            color: var(--accent);
        }

        .nav-world-cup-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.5));
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.25rem 0;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
        }

        .logout-btn {
            color: var(--accent) !important;
        }

        .menu-toggle {
            display: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            background: none;
            border: none;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 3rem;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .badges-header {
            text-align: center;
        }

        .badges-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .badges-title span {
            color: var(--accent);
        }

        .badges-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Achievements Grid */
        .badges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .badge-card {
            background-color: var(--card-bg);
            border: 1.5px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .badge-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        /* Badge Card Status Modifiers */
        .badge-card.unlocked.bronze { border-color: var(--bronze); box-shadow: 0 0 15px var(--bronze-glow); }
        .badge-card.unlocked.silver { border-color: var(--silver); box-shadow: 0 0 15px var(--silver-glow); }
        .badge-card.unlocked.gold { border-color: var(--gold); box-shadow: 0 0 15px var(--gold-glow); }
        .badge-card.unlocked.platinum { border-color: var(--platinum); box-shadow: 0 0 15px var(--platinum-glow); }
        .badge-card.unlocked.fire { border-color: var(--fire); box-shadow: 0 0 15px var(--fire-glow); }

        .badge-card.locked {
            opacity: 0.5;
        }

        /* Lock Screen Overlay */
        .lock-overlay {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--glass-border);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: var(--text-secondary);
        }

        .unlocked .lock-overlay {
            background: rgba(46, 164, 79, 0.2);
            border-color: var(--success);
            color: #58a6ff;
        }

        /* Graphical Badge Icon */
        .badge-graphic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1.5px solid var(--glass-border);
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .unlocked .badge-graphic {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.05);
        }

        .unlocked.bronze .badge-graphic { border-color: var(--bronze); text-shadow: 0 0 10px var(--bronze); }
        .unlocked.silver .badge-graphic { border-color: var(--silver); text-shadow: 0 0 10px var(--silver); }
        .unlocked.gold .badge-graphic { border-color: var(--gold); text-shadow: 0 0 10px var(--gold); }
        .unlocked.platinum .badge-graphic { border-color: var(--platinum); text-shadow: 0 0 10px var(--platinum); }
        .unlocked.fire .badge-graphic { border-color: var(--fire); text-shadow: 0 0 15px var(--fire); }

        .badge-info h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .badge-info p {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        /* Progress Bar */
        .progress-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .progress-bar-bg {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--text-secondary);
            border-radius: 4px;
            width: 0%;
            transition: width 1s ease-out;
        }

        .unlocked .progress-bar-fill {
            background: var(--success);
        }

        .locked.bronze .progress-bar-fill { background: var(--bronze); }
        .locked.silver .progress-bar-fill { background: var(--silver); }
        .locked.gold .progress-bar-fill { background: var(--gold); }
        .locked.platinum .progress-bar-fill { background: var(--platinum); }
        .locked.fire .progress-bar-fill { background: var(--fire); }

        .progress-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-align: right;
        }

        .unlocked .progress-text {
            color: #58a6ff;
        }

        /* Footer */
        .footer {
            border-top: 1px solid var(--glass-border);
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 4rem;
        }

        .footer span {
            color: var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: rgba(26, 26, 46, 0.95);
                backdrop-filter: blur(15px);
                border-bottom: 1px solid var(--glass-border);
                flex-direction: column;
                gap: 1.25rem;
                padding: 2rem;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
            }

            .nav-menu.active {
                display: flex;
            }

            .nav-link {
                width: 100%;
                text-align: center;
                font-size: 1.05rem;
            }
        }
    </style>
</head>
<body>
    <!-- Premium Header -->
    <header class="header">
        <nav class="nav">
            <a href="Home.php" class="nav-logo">
                <img src="src/world_cup_logo.png" alt="World Cup Trophy Logo" class="nav-world-cup-logo">
                Predict<span>King</span>
            </a>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            <div class="nav-menu">
                <a href="Home.php" class="nav-link">Home</a>
                <a href="Leaderboard.php" class="nav-link">Leaderboard</a>
                <a href="Badges.php" class="nav-link active">Badges</a>
                <a href="Rules.php" class="nav-link">Rules</a>
                <a href="News.php" class="nav-link">News</a>
                <a href="Classes/Logout.php" class="nav-link logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Title -->
        <div class="badges-header">
            <h1 class="badges-title">Milestones & <span>Achievements 🎖️</span></h1>
            <p class="badges-subtitle">Accumulate points, lock predictions, and unlock legendary badges</p>
        </div>

        <!-- Badges Grid Showcase -->
        <section class="badges-grid">
            <?php
            // Badge 1: Bronze Shield
            $bronzeUnlocked = ($TotalPoints >= 50);
            $bronzeProgress = min(100, round(($TotalPoints / 50) * 100));
            $bronzeCardClass = $bronzeUnlocked ? 'unlocked bronze' : 'locked bronze';
            ?>
            <div class="badge-card <?php echo $bronzeCardClass; ?>">
                <div class="lock-overlay"><?php echo $bronzeUnlocked ? '✓' : '🔒'; ?></div>
                <div class="badge-graphic">🥉</div>
                <div class="badge-info">
                    <h3>Bronze Shield</h3>
                    <p>Earned by accumulating 50 total points in the platform</p>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $bronzeProgress; ?>%;"></div>
                    </div>
                    <div class="progress-text"><?php echo $TotalPoints; ?> / 50 pts (<?php echo $bronzeProgress; ?>%)</div>
                </div>
            </div>

            <?php
            // Badge 2: Silver Star
            $silverUnlocked = ($TotalPoints >= 100);
            $silverProgress = min(100, round(($TotalPoints / 100) * 100));
            $silverCardClass = $silverUnlocked ? 'unlocked silver' : 'locked silver';
            ?>
            <div class="badge-card <?php echo $silverCardClass; ?>">
                <div class="lock-overlay"><?php echo $silverUnlocked ? '✓' : '🔒'; ?></div>
                <div class="badge-graphic">🥈</div>
                <div class="badge-info">
                    <h3>Silver Shield</h3>
                    <p>Earned by accumulating 100 total points in the platform</p>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $silverProgress; ?>%;"></div>
                    </div>
                    <div class="progress-text"><?php echo $TotalPoints; ?> / 100 pts (<?php echo $silverProgress; ?>%)</div>
                </div>
            </div>

            <?php
            // Badge 3: Gold Crown
            $goldUnlocked = ($TotalPoints >= 200);
            $goldProgress = min(100, round(($TotalPoints / 200) * 100));
            $goldCardClass = $goldUnlocked ? 'unlocked gold' : 'locked gold';
            ?>
            <div class="badge-card <?php echo $goldCardClass; ?>">
                <div class="lock-overlay"><?php echo $goldUnlocked ? '✓' : '🔒'; ?></div>
                <div class="badge-graphic">👑</div>
                <div class="badge-info">
                    <h3>Gold Crown</h3>
                    <p>Earned by accumulating 200 total points in the platform</p>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $goldProgress; ?>%;"></div>
                    </div>
                    <div class="progress-text"><?php echo $TotalPoints; ?> / 200 pts (<?php echo $goldProgress; ?>%)</div>
                </div>
            </div>

            <?php
            // Badge 4: Platinum Boot
            $platUnlocked = ($TotalPoints >= 500);
            $platProgress = min(100, round(($TotalPoints / 500) * 100));
            $platCardClass = $platUnlocked ? 'unlocked platinum' : 'locked platinum';
            ?>
            <div class="badge-card <?php echo $platCardClass; ?>">
                <div class="lock-overlay"><?php echo $platUnlocked ? '✓' : '🔒'; ?></div>
                <div class="badge-graphic">⚽</div>
                <div class="badge-info">
                    <h3>Platinum Boot</h3>
                    <p>Unlocks at 500 total points. The ultimate predictor crown</p>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $platProgress; ?>%;"></div>
                    </div>
                    <div class="progress-text"><?php echo $TotalPoints; ?> / 500 pts (<?php echo $platProgress; ?>%)</div>
                </div>
            </div>

            <?php
            // Badge 5: Active Streak Master
            $streakUnlocked = ($predictionCount >= 1);
            $streakProgress = min(100, round(($predictionCount / 1) * 100));
            $streakCardClass = $streakUnlocked ? 'unlocked fire' : 'locked fire';
            ?>
            <div class="badge-card <?php echo $streakCardClass; ?>">
                <div class="lock-overlay"><?php echo $streakUnlocked ? '✓' : '🔒'; ?></div>
                <div class="badge-graphic">🔥</div>
                <div class="badge-info">
                    <h3>Predictor Streak</h3>
                    <p>Earned by submitting your very first prediction match score</p>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $streakProgress; ?>%;"></div>
                    </div>
                    <div class="progress-text"><?php echo $predictionCount; ?> / 1 prediction (<?php echo $streakProgress; ?>%)</div>
                </div>
            </div>
        </section>
    </div>

    <!-- Premium Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> PredictKing - World Cup Edition. Powered by <span>⚽</span></p>
    </footer>

    <script>
        function toggleMenu() {
            const menu = document.querySelector('.nav-menu');
            menu.classList.toggle('active');
        }
    </script>
</body>
</html>
