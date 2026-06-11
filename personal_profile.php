<?php
// Start the session
session_start();

// Include the necessary classes
require_once __DIR__ . '/Classes/User.php';
require_once __DIR__ . '/Classes/Matches.php';
require_once __DIR__ . '/Classes/UserPrediction.php';

// Create instances of the classes
$user = new User();
$matchHandler = new Matches();
$predictionManager = new UserPrediction();

// Check if the user_id is provided in the query string
if (!isset($_GET['user_id'])) {
    header("Location: Home.php");
    exit();
}

if (!isset($_SESSION['UserID'])) {
    header("Location: Login.php");
    exit();
}

// Retrieve the user_id from the query parameter
$profileUserId = $_GET['user_id'];

// Fetch user data for the passed user_id
$profileUserData = $user->retrieveUserDataWithId($profileUserId);

if ($profileUserData) {
    $FirstName = $profileUserData['FirstName'];
    $LastName = $profileUserData['LastName'];
    $TotalPoints = $profileUserData['TotalPoints'];
} else {
    echo "User data not found.";
    exit();
}

// Fetch all matches
$matches = $matchHandler->GetMatches();

// Fetch predictions for the user for each match
$userPredictions = [];
foreach ($matches as $match) {
    $matchId = $match['MatchID'];
    $prediction = $predictionManager->getUserPrediction($profileUserId, $matchId);
    if ($prediction) {
        $userPredictions[$matchId] = $prediction;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - PredictKing</title>
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
            --success: #2ea44f;
            --success-glow: rgba(46, 164, 79, 0.3);
            --info: #0077ff;
            --info-glow: rgba(0, 119, 255, 0.3);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
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

        /* Sticky Glass Header */
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

        /* Container Layout */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Profile Banner Card */
        .profile-banner-card {
            background: linear-gradient(135deg, var(--card-bg) 0%, #20203a 100%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .profile-banner-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, #ff5c75 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 25px var(--accent-glow);
            border: 4px solid rgba(255, 255, 255, 0.1);
        }

        .profile-details h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .profile-details p {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-score-stat {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem 2.5rem;
            text-align: center;
            min-width: 180px;
        }

        .score-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.25rem;
        }

        .score-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-secondary);
            font-weight: 700;
        }

        /* History Section */
        .section-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title span {
            color: var(--accent);
        }

        .matches-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        /* Match Cards */
        .match-card {
            background-color: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .match-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        /* Points Highlight Borders */
        .match-card.exact-match { border: 1.5px solid var(--success) !important; box-shadow: 0 0 15px var(--success-glow); }
        .match-card.outcome-match { border: 1.5px solid var(--info) !important; box-shadow: 0 0 15px var(--info-glow); }
        .match-card.incorrect-match { border: 1.5px solid rgba(239, 35, 60, 0.25) !important; }
        .match-card.no-pred-match { opacity: 0.75; }

        .match-header {
            padding: 1rem 2rem;
            background-color: rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .match-league {
            font-weight: 700;
            font-size: 0.9rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .match-league::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: var(--accent);
            border-radius: 50%;
        }

        .match-card.exact-match .match-league::before { background-color: var(--success); }
        .match-card.outcome-match .match-league::before { background-color: var(--info); }

        .match-header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .history-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .history-badge.badge-exact { background-color: var(--success); color: white; box-shadow: 0 0 10px var(--success-glow); }
        .history-badge.badge-outcome { background-color: var(--info); color: white; box-shadow: 0 0 10px var(--info-glow); }
        .history-badge.badge-incorrect { background-color: rgba(239, 35, 60, 0.15); border: 1px solid var(--accent); color: #ff4d6d; }
        .history-badge.badge-none { background-color: rgba(255, 255, 255, 0.05); color: var(--text-secondary); }

        .match-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .match-content {
            padding: 2.5rem 2rem;
        }

        .match-teams {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 3rem;
            align-items: center;
        }

        .team {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .team-logo-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .team-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .team-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
        }

        .score-actual-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .score-num {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
        }

        .vs {
            font-weight: 900;
            color: var(--accent);
            font-size: 1.3rem;
            font-style: italic;
        }

        .history-pred-text {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .history-pred-text span {
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
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

        /* Responsive Layouts */
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

            .profile-banner-card {
                flex-direction: column;
                padding: 2rem;
                text-align: center;
            }

            .profile-banner-left {
                flex-direction: column;
                gap: 1rem;
            }

            .profile-avatar {
                width: 80px;
                height: 80px;
                font-size: 2.2rem;
            }

            .profile-details h2 {
                font-size: 1.75rem;
            }

            .match-teams {
                gap: 1rem;
            }

            .team-logo-wrapper {
                width: 65px;
                height: 65px;
            }

            .team-logo {
                width: 38px;
                height: 38px;
            }

            .team-name {
                font-size: 0.9rem;
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
                <a href="Badges.php" class="nav-link">Badges</a>
                <a href="Rules.php" class="nav-link">Rules</a>
                <a href="News.php" class="nav-link">News</a>
                <a href="Classes/Logout.php" class="nav-link logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Profile Banner -->
        <section class="profile-banner-card">
            <div class="profile-banner-left">
                <div class="profile-avatar"><?php echo strtoupper(substr($FirstName, 0, 1)); ?></div>
                <div class="profile-details">
                    <h2><?php echo htmlspecialchars($FirstName . ' ' . $LastName); ?></h2>
                    <p>⚽ Predictor Profile</p>
                </div>
            </div>
            <div class="profile-score-stat">
                <div class="score-value"><?php echo htmlspecialchars($TotalPoints); ?></div>
                <div class="score-label">Points Scored</div>
            </div>
        </section>

        <!-- User Predictions History Section -->
        <section>
            <h2 class="section-title">Predictions by <span><?php echo htmlspecialchars($FirstName); ?> 🕓</span></h2>
            <div class="matches-grid">
                <?php 
                $hasPredictions = false;
                foreach ($matches as $match):
                    if ($match['ongoing'] == 1) {
                        $hasPredictions = true;
                        $matchId = $match['MatchID'];
                        $userPrediction = $userPredictions[$matchId] ?? null;
                        $hasPredicted = !empty($userPrediction);
                        
                        $multiplier = isset($match['PointsMultiplier']) ? intval($match['PointsMultiplier']) : 1;

                        $isExact = false;
                        $isCorrectOutcome = false;
                        $pointsEarned = 0;
                        $cardHighlightClass = 'no-pred-match';
                        $badgeHTML = '<span class="history-badge badge-none">No prediction</span>';

                        if ($hasPredicted) {
                            $t1P = intval($userPrediction['Team1Score']);
                            $t2P = intval($userPrediction['Team2Score']);
                            $t1A = intval($match['Team1FinalScore']);
                            $t2A = intval($match['Team2FinalScore']);

                            if ($t1P === $t1A && $t2P === $t2A) {
                                $isExact = true;
                                $pointsEarned = 30 * $multiplier;
                                $cardHighlightClass = 'exact-match';
                                $badgeHTML = '<span class="history-badge badge-exact">Exact Score (+'.$pointsEarned.' pts)</span>';
                            } else {
                                $predictedOutcome = $t1P <=> $t2P;
                                $actualOutcome = $t1A <=> $t2A;
                                if ($predictedOutcome === $actualOutcome) {
                                    $isCorrectOutcome = true;
                                    $pointsEarned = 15 * $multiplier;
                                    $cardHighlightClass = 'outcome-match';
                                    $badgeHTML = '<span class="history-badge badge-outcome">Correct Outcome (+'.$pointsEarned.' pts)</span>';
                                } else {
                                    $pointsEarned = 0;
                                    $cardHighlightClass = 'incorrect-match';
                                    $badgeHTML = '<span class="history-badge badge-incorrect">Incorrect (0 pts)</span>';
                                }
                            }
                        }
                ?>
                    <div class="match-card <?php echo $cardHighlightClass; ?>">
                        <div class="match-header">
                            <div class="match-league"><?php echo htmlspecialchars($match['Tournament']); ?></div>
                            <div class="match-header-right">
                                <?php echo $badgeHTML; ?>
                                <div class="match-time">Ended</div>
                            </div>
                        </div>
                        <div class="match-content">
                            <div class="match-teams">
                                <div class="team">
                                    <div class="team-logo-wrapper">
                                        <img src="<?php echo htmlspecialchars($match['Team1Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team1Name']); ?>" class="team-logo">
                                    </div>
                                    <div class="team-name"><?php echo htmlspecialchars($match['Team1Name']); ?></div>
                                </div>

                                <div class="score-actual-display">
                                    <span class="score-num"><?php echo htmlspecialchars($match['Team1FinalScore'] ?? '0'); ?></span>
                                    <div class="vs">-</div>
                                    <span class="score-num"><?php echo htmlspecialchars($match['Team2FinalScore'] ?? '0'); ?></span>
                                </div>

                                <div class="team">
                                    <div class="team-logo-wrapper">
                                        <img src="<?php echo htmlspecialchars($match['Team2Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team2Name']); ?>" class="team-logo">
                                    </div>
                                    <div class="team-name"><?php echo htmlspecialchars($match['Team2Name']); ?></div>
                                </div>
                            </div>

                            <div class="history-pred-text">
                                <?php if ($hasPredicted): ?>
                                    Prediction was: <span><?php echo htmlspecialchars($userPrediction['Team1Score'] . ' - ' . $userPrediction['Team2Score']); ?></span>
                                <?php else: ?>
                                    Did not predict this match.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php 
                    }
                endforeach;
                if (!$hasPredictions):
                ?>
                    <div class="match-card" style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                        This user hasn't made any predictions for ended matches yet.
                    </div>
                <?php endif; ?>
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