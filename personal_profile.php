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
    // Redirect to the home page if no user_id is provided
    header("Location: Home.php");
    exit();
}


if (!isset($_SESSION['UserID'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: Login.php");
    exit();
}


// Retrieve the user_id from the query parameter
$userId = $_GET['user_id'];

// Fetch user data for the passed user_id
$userData = $user->retrieveUserDataWithId($userId);

if ($userData) {
    $FirstName = $userData['FirstName'];
    $LastName = $userData['LastName'];
    $TotalPoints = $userData['TotalPoints'];
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
    $prediction = $predictionManager->getUserPrediction($userId, $matchId);
    if ($prediction) {
        $userPredictions[$matchId] = $prediction;
    }
}


$users = $user->retriveAllUserScore();

// Display success or error messages (if any)
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
    <title>PredictKing - User Predictions</title>
    <link rel="icon" type="image/png" href="src/Screenshot 2025-02-10 153127.png">
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer; /* Make the header clickable */
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

        /* Leaderboard Toggle Button */
        .leaderboard-toggle {
            font-size: 1.5rem;
            transition: transform 0.3s ease; /* Add a smooth rotation animation */
        }

        .leaderboard-list.active + .leaderboard-header .leaderboard-toggle {
            transform: rotate(180deg); /* Rotate the arrow when the list is active */
        }

        /* History Section */
        .history-section {
            margin-top: 2rem;
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .history-section h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .history-section .match-card {
            margin-bottom: 1.5rem;
        }

        .history-section .match-header {
            background-color: #Ef233C;
        }

        .history-section .team-name {
            color: black;
        }

        .prediction-display {
            margin-top: 10px;
            text-align: center;
        }

        .user-prediction {
            font-weight: bold;
            color: #4CAF50; /* Green color for predictions */
        }

        .no-prediction {
            font-style: italic;
            color: black; /* Gray color for no prediction */
        }

        .prediction-score {
            font-size: 1.2em;
            color: #333;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            /* Reorder elements for mobile */
            .container > aside {
                order: 1; /* Leaderboard appears first */
            }

            .container > main {
                order: 2; /* Main content (matches and history) appears second */
            }

            .leaderboard-list {
                display: none; /* Hide leaderboard list by default on mobile */
            }

            .leaderboard-list.active {
                display: block; /* Show leaderboard list when active */
            }
        }

        .player-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 10px;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease;
}

.player-link:hover {
    background-color: #f5f5f5;
}
</style>
</head>
<body>
<header class="header">
    <nav class="nav">
        <div class="nav-logo">Predict<span>King</span></div>
        <div class="nav-menu">
            <a href="Home.php" class="nav-link">Home</a>
            <a href="Rules.php" class="nav-link">Rules</a>
            <a href="News.php" class="nav-link">News</a>
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
                <div class="stat">
                    <div class="stat-value"><?php echo htmlspecialchars($TotalPoints); ?></div>
                    <div class="stat-label">Points</div>
                </div>
            </div>
      

        <!-- History Section -->
        <div class="history-section" style="background-color: #2B2D42;">
            <h3 style="color: white;">Predictions by <?php echo htmlspecialchars($FirstName); ?> 🕓</h3>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <?php
                    // Check if the match is ongoing or completed
                    if ($match['ongoing'] == 1) {
                        // Check if the user has predicted this match
                        $matchId = $match['MatchID'];
                        $userPrediction = $userPredictions[$matchId] ?? null;
                        $hasPredicted = !empty($userPrediction);
                    ?>
                        <div class="match-card">
                            <div class="match-header" style="background-color: #Ef233C;">
                                <div class="match-league"><?php echo htmlspecialchars($match['Tournament']); ?></div>
                                <div class="match-time">Ended</div>
                            </div>
                            <div class="match-content">
                                <div class="match-teams">
                                    <div class="team">
                                        <div class="team-logo">
                                            <img src="<?php echo htmlspecialchars($match['Team1Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team1Name']); ?> Logo" style="width: 80px; height: 80px; object-fit: contain;">
                                        </div>
                                        <div class="team-name" style="color:black"><?php echo htmlspecialchars($match['Team1Name']); ?></div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <!-- Team 1 Score -->
                                        <span style="font-weight: bold; font-size: 1.2em; color: black;"><?php echo htmlspecialchars($match['Team1FinalScore'] ?? '0'); ?></span>
                                        <div class="vs">VS</div>
                                        <!-- Team 2 Score -->
                                        <span style="font-weight: bold; font-size: 1.2em; color: black;"><?php echo htmlspecialchars($match['Team2FinalScore'] ?? '0'); ?></span>
                                    </div>
                                    <div class="team">
                                        <div class="team-logo">
                                            <img src="<?php echo htmlspecialchars($match['Team2Logo']); ?>" alt="<?php echo htmlspecialchars($match['Team2Name']); ?> Logo" style="width: 80px; height: 80px; object-fit: contain;">
                                        </div>
                                        <div class="team-name" style="color:black"><?php echo htmlspecialchars($match['Team2Name']); ?></div>
                                    </div>
                                </div>
                                <div class="prediction-display">
                                    <?php if ($hasPredicted): ?>
                                        <div class="user-prediction">
                                            <span>Prediction:</span>
                                            <span class="prediction-score">
                                                <?php echo htmlspecialchars($userPrediction['Team1Score']); ?> - <?php echo htmlspecialchars($userPrediction['Team2Score']); ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="no-prediction">
                                            No prediction for this match.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>


     <!-- Leaderboard Section -->
        <div class="leaderboard">
    <div class="leaderboard-header" onclick="toggleLeaderboard()">
        <h3 class="leaderboard-title">Leaderboard</h3>
        <span class="leaderboard-toggle">▼</span>
    </div>
    <ul class="leaderboard-list" id="leaderboard-list">
        <?php foreach ($users as $key => $value): ?>
            <li class="leaderboard-item">
            <a href="personal_profile.php?user_id=<?php echo $value['UserID']; ?>" class="player-link">
                    <div class="player-info">
                        <div class="player-rank"><?php echo $key + 1; ?></div>
                        <div><?php echo htmlspecialchars($value['FirstName'] . ' ' . $value['LastName']); ?></div>
                    </div>
                    <div class="player-points"><?php echo htmlspecialchars($value['TotalPoints']); ?> Points</div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    function toggleLeaderboard() {
        const leaderboardList = document.getElementById('leaderboard-list');
        leaderboardList.classList.toggle('active');
    }
</script>
</div>
</body>
</html>