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

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchId = $_POST['match_id'];
    $team1Score = $_POST['team1_score'];
    $team2Score = $_POST['team2_score'];

    // Save the prediction
    if ($predictionManager->save($userId, $matchId, $team1Score, $team2Score)) {
        // Set a success message in the session
        $_SESSION['success_message'] = 'Prediction saved successfully!';
    } else {
        // Set an error message in the session
        $_SESSION['error_message'] = 'You have already predicted this match or an error occurred.';
    }

    // Redirect to the home page to prevent form resubmission
    header("Location: home.php");
    exit();
}

// Fetch matches and user data
$matches = $matchHandler->GetMatches();
$users = $user->retriveAllUserScore();

// Display success or error messages
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']); // Clear the message after displaying
unset($_SESSION['error_message']); // Clear the message after displaying
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
            scroll-behavior: smooth;
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
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 0 2rem;
        }

        .nav-logo {
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .nav-logo span {
            color: var(--accent);
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
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
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .nav-menu {
                flex-direction: row;
                align-items: center;
                gap: 0.5rem;
            }

            .container {
                grid-template-columns: 1fr;
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

       /* Success and Error Messages */
.message {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
}

.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-logo">Predict<span>King</span></div>
            <div class="nav-menu">
                <a href="#" class="nav-link">Rules</a>
                <a href="#" class="nav-link">News</a>
                <a href="Classes/Logout.php" class="nav-link" style="color: red;">Logout</a>
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
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo htmlspecialchars($TotalPoints); ?></div>
                        <div class="stat-label">Points</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">2X</div>
                        <div class="stat-label">Ongoing Round</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">4X</div>
                        <div class="stat-label">Next Round</div>
                    </div>
                </div>
            </div>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <?php
                    // Set the default timezone to match your local timezone
                    date_default_timezone_set('Africa/Cairo'); // Use 'Africa/Cairo' as the timezone

                    // Get the current date and time
                    $currentDate = new DateTime('now', new DateTimeZone('Africa/Cairo'));

                    // Get the match date and time from the database (format: 2025-01-31 20:50:00)
                    $matchDate = new DateTime($match['date'], new DateTimeZone('Africa/Cairo'));

                    // Calculate the cutoff time (1 hour before the match starts)
                    $predictionCutoffTime = clone $matchDate;
                    $predictionCutoffTime->modify('-1 hour');

                    // Disable predictions if:
                    // 1. The current time is after the cutoff time (less than 1 hour before the match), or
                    // 2. The match has already started (current time is after the match time)
                    $isDisabled = ($currentDate >= $predictionCutoffTime);

                    // Check if the user has already predicted this match
                    $userPrediction = UserPrediction::getUserPrediction($userId, $match['MatchID']);
                    $hasPredicted = !empty($userPrediction);
                    ?>
                    <div class="match-card">
                        <div class="match-header">
                            <div class="match-league"><?php echo htmlspecialchars($match['Tournament']); ?></div>
                            <div class="match-time"><?php echo htmlspecialchars($match['date']); ?></div>
                        </div>
                        <div class="match-content">
                            <div class="match-teams">
                                <div class="team">
                                    <div class="team-logo">
                                        <img src="<?php echo htmlspecialchars($match['Team1Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team1Name']); ?> Logo" style="width: 80px; height: 80px; object-fit: contain;">
                                    </div>
                                    <div class="team-name"><?php echo htmlspecialchars($match['Team1Name']); ?></div>
                                </div>
                                <div class="vs">VS</div>
                                <div class="team">
                                    <div class="team-logo">
                                        <img src="<?php echo htmlspecialchars($match['Team2Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team2Name']); ?> Logo" style="width: 80px; height: 80px; object-fit: contain;">
                                    </div>
                                    <div class="team-name"><?php echo htmlspecialchars($match['Team2Name']); ?></div>
                                </div>
                            </div>
                            <form class="prediction-form" method="POST" action="" <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?>>
                                <input type="hidden" name="match_id" value="<?php echo $match['MatchID']; ?>">
                                <input type="number" name="team1_score" class="prediction-input" min="0" max="99" placeholder="0" 
                                    value="<?php echo $hasPredicted ? htmlspecialchars($userPrediction['Team1Score']) : ''; ?>" 
                                    <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?>>
                                <div class="vs">-</div>
                                <input type="number" name="team2_score" class="prediction-input" min="0" max="99" placeholder="0" 
                                    value="<?php echo $hasPredicted ? htmlspecialchars($userPrediction['Team2Score']) : ''; ?>" 
                                    <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?>>
                                <button type="submit" class="submit-btn" <?php echo $hasPredicted || $isDisabled ? 'disabled' : ''; ?> style="<?php echo $hasPredicted || $isDisabled ? 'background-color: black;' : ''; ?>">
                                    <?php 
                                    if ($hasPredicted) {
                                        echo 'Predicted: ' . htmlspecialchars($userPrediction['Team1Score']) . ' - ' . htmlspecialchars($userPrediction['Team2Score']);
                                    } elseif ($isDisabled) {
                                        echo 'You cannot predict';
                                    } else {
                                        echo 'Submit Prediction';
                                    }
                                    ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <aside>
            <div class="leaderboard">
                <div class="leaderboard-header">
                    <h3 class="leaderboard-title">Leaderboard</h3>
                </div>
                <ul class="leaderboard-list">
                    <?php foreach ($users as $key => $value): ?>
                        <li class="leaderboard-item">
                            <div class="player-info">
                                <div class="player-rank"><?php echo $key + 1; ?></div>
                                <div><?php echo htmlspecialchars($value['FirstName'] . ' ' . $value['LastName']); ?></div>
                            </div>
                            <div class="player-points"><?php echo htmlspecialchars($value['TotalPoints']); ?> Points</div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</body>
</html>