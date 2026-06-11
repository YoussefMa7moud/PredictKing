<?php
session_start();

require_once __DIR__ . '/Classes/User.php';
require_once __DIR__ . '/Classes/Matches.php';
require_once __DIR__ . '/Classes/UserPrediction.php';

$user = new User();
$matchHandler = new Matches();
$predictionManager = new UserPrediction();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchId = $_POST['match_id'];
    $team1Score = $_POST['team1_score'];
    $team2Score = $_POST['team2_score'];

    if ($predictionManager->save($userId, $matchId, $team1Score, $team2Score)) {
        $_SESSION['success_message'] = 'Prediction saved successfully!';
    } else {
        $_SESSION['error_message'] = 'You have already predicted this match or an error occurred.';
    }

    header("Location: Home.php");
    exit();
}

$matches = $matchHandler->GetMatches();

$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PredictKing - Football Prediction League</title>
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
            --gold: #ffd700;
            --gold-glow: rgba(255, 215, 0, 0.3);
            --purple: #8a2be2;
            --purple-glow: rgba(138, 43, 226, 0.3);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseGold {
            0%, 100% { border-color: rgba(255, 215, 0, 0.4); box-shadow: 0 0 10px rgba(255, 215, 0, 0.1); }
            50% { border-color: rgba(255, 215, 0, 1); box-shadow: 0 0 20px rgba(255, 215, 0, 0.3); }
        }

        @keyframes pulsePurple {
            0%, 100% { border-color: rgba(138, 43, 226, 0.4); box-shadow: 0 0 10px rgba(138, 43, 226, 0.1); }
            50% { border-color: rgba(138, 43, 226, 1); box-shadow: 0 0 20px rgba(138, 43, 226, 0.3); }
        }

        @keyframes pulseGreen {
            0%, 100% { box-shadow: 0 0 5px rgba(46, 164, 79, 0.4); }
            50% { box-shadow: 0 0 15px rgba(46, 164, 79, 0.8); }
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
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            transition: all 0.3s ease;
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

        .logout-btn::after {
            background-color: var(--accent);
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
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2.5rem;
            flex: 1;
        }

        /* Left Sidebar Styling */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .profile-card {
            background: linear-gradient(135deg, var(--card-bg) 0%, #252542 100%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(239, 35, 60, 0.05);
            filter: blur(50px);
            border-radius: 50%;
        }

        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, #ff5c75 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 800;
            box-shadow: 0 8px 20px var(--accent-glow);
            border: 3px solid rgba(255, 255, 255, 0.1);
        }

        .profile-info h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .stat {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            padding: 1.25rem;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .stat:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--accent);
            transform: translateY(-3px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Motivational Card with Salah */
        .promo-card {
            background: linear-gradient(135deg, #10101f 0%, #1c1c35 100%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .promo-player-img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            filter: drop-shadow(0 8px 12px rgba(0, 0, 0, 0.4));
        }

        .promo-text h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .promo-text p {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Main Area Layout */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .section-header-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-header-title span {
            color: var(--accent);
        }

        /* Prediction Card Styling */
        .matches-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .match-card {
            background-color: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease both;
        }

        .match-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        /* Multiplier Badges and Custom Borders */
        .match-card.double-points {
            animation: fadeInUp 0.6s ease both, pulseGold 4s infinite ease-in-out;
            border-width: 1.5px;
        }

        .match-card.triple-points {
            animation: fadeInUp 0.6s ease both, pulsePurple 4s infinite ease-in-out;
            border-width: 1.5px;
        }

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

        .match-card.double-points .match-league::before {
            background-color: var(--gold);
            box-shadow: 0 0 8px var(--gold);
        }

        .match-card.triple-points .match-league::before {
            background-color: var(--purple);
            box-shadow: 0 0 8px var(--purple);
        }

        .match-header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .match-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .match-badge.badge-double {
            background-color: var(--gold);
            color: #000;
            box-shadow: 0 0 10px var(--gold-glow);
        }

        .match-badge.badge-triple {
            background-color: var(--purple);
            color: #fff;
            box-shadow: 0 0 10px var(--purple-glow);
        }

        .match-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .match-content {
            padding: 2.5rem 2rem;
        }

        .match-teams {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 3rem;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .team {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .team-logo-wrapper {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .match-card:hover .team-logo-wrapper {
            transform: scale(1.08);
            border-color: rgba(255, 255, 255, 0.25);
        }

        .team-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .team-name {
            font-weight: 700;
            font-size: 1.15rem;
            color: white;
            letter-spacing: 0.25px;
        }

        .vs {
            font-weight: 900;
            color: var(--accent);
            font-size: 1.5rem;
            font-style: italic;
            letter-spacing: 1px;
            text-shadow: 0 0 10px var(--accent-glow);
        }

        /* Prediction Form */
        .prediction-form {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 1.5rem;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.15);
            padding: 1.5rem 2rem;
            border-radius: 18px;
            border: 1px solid var(--glass-border);
        }

        .prediction-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1.5px solid var(--glass-border);
            border-radius: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-align: center;
            transition: all 0.3s ease;
        }

        .prediction-input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 12px var(--accent-glow);
        }

        .prediction-input:disabled {
            background-color: transparent;
            border-color: transparent;
            color: #ffffff;
            font-size: 1.8rem;
            cursor: default;
        }

        .submit-btn {
            grid-column: 1 / -1;
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 1.1rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px var(--accent-glow);
        }

        .submit-btn:hover {
            background-color: #ff3355;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--accent-glow);
        }

        /* ALREADY PREDICTED STATE */
        .predicted-overlay {
            grid-column: 1 / -1;
            background: rgba(46, 164, 79, 0.08);
            border: 1.5px dashed var(--success);
            padding: 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            color: #58a6ff;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: inset 0 0 10px rgba(46, 164, 79, 0.05);
        }

        .predicted-overlay.locked-in {
            animation: pulseGreen 3s infinite ease-in-out;
        }

        .predicted-score-display {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            margin-left: 0.25rem;
        }

        .prediction-divider {
            color: var(--text-secondary);
            font-weight: 800;
            font-size: 1.2rem;
        }

        /* HISTORY MATCH CARDS - POINT ACCORDING HIGHLIGHT BORDERS */
        .history-card {
            background-color: var(--card-bg);
            border: 1px solid var(--glass-border);
        }

        .history-card.exact-match {
            border: 1.5px solid var(--success) !important;
            box-shadow: 0 0 15px var(--success-glow);
        }

        .history-card.outcome-match {
            border: 1.5px solid var(--info) !important;
            box-shadow: 0 0 15px var(--info-glow);
        }

        .history-card.incorrect-match {
            border: 1px solid rgba(239, 35, 60, 0.25) !important;
        }

        .history-card.no-pred-match {
            border: 1px solid var(--glass-border);
            opacity: 0.75;
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

        .history-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .history-badge.badge-exact {
            background-color: var(--success);
            color: white;
            box-shadow: 0 0 10px var(--success-glow);
        }

        .history-badge.badge-outcome {
            background-color: var(--info);
            color: white;
            box-shadow: 0 0 10px var(--info-glow);
        }

        .history-badge.badge-incorrect {
            background-color: rgba(239, 35, 60, 0.15);
            border: 1px solid var(--accent);
            color: #ff4d6d;
        }

        .history-badge.badge-none {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
        }

        .history-pred-text {
            margin-top: 1.25rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .history-pred-text span {
            color: white;
            font-weight: 800;
            font-size: 1.05rem;
        }

        /* Message Alerts */
        .message-alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-weight: 600;
            animation: fadeInUp 0.4s ease both;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message-alert.success {
            background-color: rgba(46, 164, 79, 0.1);
            border: 1.5px solid var(--success);
            color: #58a6ff;
        }

        .message-alert.error {
            background-color: rgba(239, 35, 60, 0.1);
            border: 1.5px solid var(--accent);
            color: #ff4d6d;
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

        /* Mobile Styles */
        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
                padding: 1rem;
                gap: 2rem;
            }

            .sidebar {
                order: 2;
            }

            .main-content {
                order: 1;
            }

            .promo-card {
                display: none;
            }
        }

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

            .match-teams {
                gap: 1rem;
            }

            .team-logo-wrapper {
                width: 70px;
                height: 70px;
            }

            .team-logo {
                width: 44px;
                height: 44px;
            }

            .team-name {
                font-size: 0.95rem;
            }

            .vs {
                font-size: 1.2rem;
            }

            .prediction-form {
                padding: 1rem;
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
                <a href="Home.php" class="nav-link active">Home</a>
                <a href="Leaderboard.php" class="nav-link">Leaderboard</a>
                <a href="Badges.php" class="nav-link">Badges</a>
                <a href="Rules.php" class="nav-link">Rules</a>
                <a href="News.php" class="nav-link">News</a>
                <a href="Classes/Logout.php" class="nav-link logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Container Layout -->
    <div class="container">
        <!-- Left Sidebar: Profile Details -->
        <aside class="sidebar">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar"><?php echo strtoupper(substr($FirstName, 0, 1)); ?></div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($FirstName . ' ' . $LastName); ?></h2>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo htmlspecialchars($TotalPoints); ?></div>
                        <div class="stat-label">Total Points</div>
                    </div>
                </div>
            </div>

            <!-- Ronaldo Motivational Prompt -->
            <div class="promo-card">
                <img src="src/ronaldo.png" alt="Cristiano Ronaldo" class="promo-player-img">
                <div class="promo-text">
                    <h3>Ronaldo says: "SIUUU!"</h3>
                    <p>"Make your predictions count! Analyze team stats, predict the exact score, and climb up the Leaderboard."</p>
                </div>
            </div>
        </aside>

        <!-- Right Side: Main Match Feed -->
        <main class="main-content">
            <?php if (!empty($successMessage)): ?>
                <div class="message-alert success">
                    ✅ <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="message-alert error">
                    ⚠️ <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <!-- Upcoming Predictions Section -->
            <section>
                <h2 class="section-header-title">Upcoming <span>Predictions 🎯</span></h2>
                <div class="matches-grid">
                    <?php 
                    $hasUpcoming = false;
                    foreach ($matches as $match): 
                        if ($match['ongoing'] == 0) {
                            $hasUpcoming = true;
                            date_default_timezone_set('Africa/Cairo');
                            $currentDate = new DateTime('now', new DateTimeZone('Africa/Cairo'));
                            $matchDate = new DateTime($match['date'], new DateTimeZone('Africa/Cairo'));

                            $predictionCutoffTime = clone $matchDate;
                            $predictionCutoffTime->modify('-1 hour');
                            $isDisabled = ($currentDate >= $predictionCutoffTime);

                            $userPrediction = UserPrediction::getUserPrediction($userId, $match['MatchID']);
                            $hasPredicted = !empty($userPrediction);
                            
                            $multiplier = isset($match['PointsMultiplier']) ? intval($match['PointsMultiplier']) : 1;
                            $cardClass = '';
                            if ($multiplier == 2) $cardClass = 'double-points';
                            elseif ($multiplier >= 3) $cardClass = 'triple-points';
                    ?>
                        <div class="match-card <?php echo $cardClass; ?>">
                            <div class="match-header">
                                <div class="match-league"><?php echo htmlspecialchars($match['Tournament']); ?></div>
                                <div class="match-header-right">
                                    <?php if ($multiplier == 2): ?>
                                        <span class="match-badge badge-double">2x Points</span>
                                    <?php elseif ($multiplier >= 3): ?>
                                        <span class="match-badge badge-triple"><?php echo $multiplier; ?>x Points</span>
                                    <?php endif; ?>
                                    <div class="match-time">🕒 <?php echo date('M j, Y - H:i', strtotime($match['date'])); ?> (EET)</div>
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
                                    <div class="vs">VS</div>
                                    <div class="team">
                                        <div class="team-logo-wrapper">
                                            <img src="<?php echo htmlspecialchars($match['Team2Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team2Name']); ?>" class="team-logo">
                                        </div>
                                        <div class="team-name"><?php echo htmlspecialchars($match['Team2Name']); ?></div>
                                    </div>
                                </div>

                                <form class="prediction-form" method="POST" <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?>>
                                    <input type="hidden" name="match_id" value="<?php echo $match['MatchID']; ?>">
                                    
                                    <input type="number" name="team1_score" class="prediction-input" min="0" max="99" placeholder="0" 
                                        value="<?php echo $hasPredicted ? htmlspecialchars($userPrediction['Team1Score']) : ''; ?>" 
                                        <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?> required>
                                    
                                    <div class="prediction-divider">-</div>
                                    
                                    <input type="number" name="team2_score" class="prediction-input" min="0" max="99" placeholder="0" 
                                        value="<?php echo $hasPredicted ? htmlspecialchars($userPrediction['Team2Score']) : ''; ?>" 
                                        <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?> required>

                                    <?php if ($hasPredicted): ?>
                                        <div class="predicted-overlay locked-in">
                                            🔒 Prediction Submitted: <span class="predicted-score-display"><?php echo htmlspecialchars($userPrediction['Team1Score'] . ' - ' . $userPrediction['Team2Score']); ?></span>
                                        </div>
                                    <?php elseif ($isDisabled): ?>
                                        <div class="predicted-overlay" style="border-color: var(--accent); color: #ff4d6d; background: rgba(239, 35, 60, 0.05);">
                                            ⏰ Predictions Closed for this match
                                        </div>
                                    <?php else: ?>
                                        <button type="submit" class="submit-btn">Submit Prediction</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php 
                        } 
                    endforeach;
                    if (!$hasUpcoming):
                    ?>
                        <div class="match-card" style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                            No upcoming prediction matches open at the moment. Check back soon!
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- History Section with Point Coloring Highlights -->
            <section id="history">
                <h2 class="section-header-title">Past <span>Match History 🕓</span></h2>
                <div class="matches-grid">
                    <?php 
                    $hasHistory = false;
                    foreach ($matches as $match):
                        if ($match['ongoing'] == 1) {
                            $hasHistory = true;
                            $userPrediction = UserPrediction::getUserPrediction($userId, $match['MatchID']);
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
                        <div class="match-card history-card <?php echo $cardHighlightClass; ?>">
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
                                        <div class="team-name" style="color: white;"><?php echo htmlspecialchars($match['Team1Name']); ?></div>
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
                                        <div class="team-name" style="color: white;"><?php echo htmlspecialchars($match['Team2Name']); ?></div>
                                    </div>
                                </div>

                                <div class="history-pred-text">
                                    <?php if ($hasPredicted): ?>
                                        Your Prediction was: <span><?php echo htmlspecialchars($userPrediction['Team1Score'] . ' - ' . $userPrediction['Team2Score']); ?></span>
                                    <?php else: ?>
                                        You did not predict this match.
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        }
                    endforeach;
                    if (!$hasHistory):
                    ?>
                        <div class="match-card" style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                            No match history recorded yet.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
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