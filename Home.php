<?php
// Start the session
session_start();

// Include the User class
require_once __DIR__ . '/Classes/User.php';

// Create an instance of the User class
$user = new User();

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Check if the logout action is requested


// Retrieve user data
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PredictKing - Football Prediction League</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        :root {
            --primary: #2b2d42;
            --secondary: #8d99ae;
            --accent: #ef233c;
            --background: #edf2f4;
            --card-bg: #ffffff;
            --text: #2b2d42;
        }

        body {
            background-color: var(--primary);
            color: var(--text);
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            background-color: var(--primary);
            padding: 1rem;
            border-bottom: 2px solid var(--accent);
        }

        .nav {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .nav-logo {
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .nav-menu {
            display: flex;
            gap: 3rem;
        }

        .nav-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white;
        }

        /* Main Content */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
            background-color: var(--background);
            min-height: calc(100vh - 80px);
        }

        /* Profile Card */
        .profile-card {
            background: linear-gradient(135deg, var(--primary), #3d405b);
            border-radius: 15px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .profile-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .profile-info h2 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .stat {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        /* Match Cards */
        .matches-grid {
            display: grid;
            gap: 1.5rem;
        }

        .match-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .match-card:hover {
            transform: translateY(-5px);
        }

        .match-header {
            background-color: var(--primary);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .match-league {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .match-league::before {
            content: "•";
            color: var(--accent);
        }

        .match-time {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .match-content {
            padding: 2rem;
        }

        .match-teams {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 2rem;
            align-items: center;
            margin-bottom: 2rem;
        }

        .team {
            text-align: center;
        }

        .team-logo {
            width: 80px;
            height: 80px;
            background-color: var(--background);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .team-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .vs {
            font-weight: 800;
            color: var(--accent);
            font-size: 1.2rem;
        }

        .prediction-form {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 1rem;
            align-items: center;
            background-color: var(--background);
            padding: 1.5rem;
            border-radius: 10px;
        }

        .prediction-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid transparent;
            border-radius: 8px;
            font-size: 1.2rem;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        .prediction-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .submit-btn {
            grid-column: 1 / -1;
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background-color: #d90429;
        }

        /* Leaderboard */
        .leaderboard {
            background-color: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
        }

        .leaderboard-header {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .leaderboard-title {
            font-size: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .leaderboard-list {
            list-style: none;
            padding: 1.5rem;
        }

        .leaderboard-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--background);
        }

        .leaderboard-item:last-child {
            border-bottom: none;
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .player-rank {
            font-weight: 800;
            color: var(--accent);
            font-size: 1.1rem;
        }

        .player-points {
            font-weight: 600;
            color: var(--primary);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }

            .leaderboard {
                margin-top: 2rem;
            }
        }

        @media (max-width: 768px) {
            .nav {
                padding: 0 1rem;
            }

            .nav-menu {
                gap: 1.5rem;
            }

            .container {
                padding: 1rem;
            }

            .match-teams {
                gap: 1rem;
            }

            .team-logo {
                width: 60px;
                height: 60px;
                font-size: 1.2rem;
            }

            .team-name {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-logo">PredictKing</div>
            <div class="nav-menu">
                <a href="#" class="nav-link">Matches</a>
                <a href="#" class="nav-link">History</a>
                <a href="#" class="nav-link">Rules</a>
                <a href="#" class="nav-link">Profile</a>
                <a href="Classes/Logout.php" class="nav-link">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <main>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar"><?php echo strtoupper(substr($FirstName, 0, 1)); ?></div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($FirstName . ' ' . $LastName); ?></h2>
                        <p>Premium Member</p>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo htmlspecialchars($TotalPoints); ?></div>
                        <div class="stat-label">Points</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">TBD</div>
                        <div class="stat-label">TBD</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">TBD</div>
                        <div class="stat-label">TBD</div>
                    </div>
                </div>
            </div>

            <div class="matches-grid">
    <div class="match-card">
        <div class="match-header">
            <div class="match-league">Premier League</div>
            <div class="match-time">Today, 20:45</div>
        </div>
        <div class="match-content">
            <div class="match-teams">
                <div class="team">
                    <div class="team-logo">
                        <img src="https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg" alt="Arsenal Logo" style="width: 80px; height: 80px; object-fit: contain;">
                    </div>
                    <div class="team-name">Arsenal</div>
                </div>
                <div class="vs">VS</div>
                <div class="team">
                    <div class="team-logo">
                        <img src="https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg" alt="Chelsea Logo" style="width: 80px; height: 80px; object-fit: contain;">
                    </div>
                    <div class="team-name">Chelsea</div>
                </div>
            </div>
            <form class="prediction-form">
                <input type="number" class="prediction-input" min="0" max="99" placeholder="0">
                <div class="vs">-</div>
                <input type="number" class="prediction-input" min="0" max="99" placeholder="0">
                <button type="submit" class="submit-btn">Submit Prediction</button>
            </form>
        </div>
    </div>
</div>


        </main>

        <aside class="leaderboard">
            <div class="leaderboard-header">
                <h2 class="leaderboard-title">Top Predictors</h2>
            </div>
            <ul class="leaderboard-list">
                <li class="leaderboard-item">
                    <div class="player-info">
                        <span class="player-rank">1</span>
                        <span>John Doe</span>
                    </div>
                    <span class="player-points">4,120 pts</span>
                </li>
                <li class="leaderboard-item">
                    <div class="player-info">
                        <span class="player-rank">2</span>
                        <span>Jane Smith</span>
                    </div>
                    <span class="player-points">3,980 pts</span>
                </li>
                <li class="leaderboard-item">
                    <div class="player-info">
                        <span class="player-rank">3</span>
                        <span>Mike Johnson</span>
                    </div>
                    <span class="player-points">3,845 pts</span>
                </li>
                <li class="leaderboard-item">
                    <div class="player-info">
                        <span class="player-rank">4</span>
                        <span>Sarah Wilson</span>
                    </div>
                    <span class="player-points">3,720 pts</span>
                </li>
                <li class="leaderboard-item">
                    <div class="player-info">
                        <span class="player-rank">5</span>
                        <span>Tom Brown</span>
                    </div>
                    <span class="player-points">3,590 pts</span>
                </li>
            </ul>
        </aside>
    </div>
</body>
</html>