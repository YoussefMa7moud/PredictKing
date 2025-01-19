<?php
session_start();

// Redirect if the user is not logged in or not an admin
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

// Include the Matches class
require_once __DIR__ . '/Classes/Matches.php';

// Check if the form is submitted for adding a match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['league'])) {
    // Get form data
    $tournament = $_POST['league'];
    $team1Name = $_POST['team1Name'];
    $team1Logo = $_POST['team1Logo'];
    $team2Name = $_POST['team2Name'];
    $team2Logo = $_POST['team2Logo'];
    $matchDate = $_POST['matchDate'];

    // Default values for ongoing and final score
    $ongoing = 0; // Match is not ongoing by default
    
    // Create an instance of the Matches class
    $matches = new Matches();

    // Call the AddMatch function
    $result = $matches->AddMatch($tournament, $team1Name, $team1Logo, $team2Name, $team2Logo, $ongoing, $matchDate);

    // Check if the match was added successfully
    if ($result) {
        // Redirect back to the admin dashboard with a success message
        header('Location: Admin.php?status=success');
        exit();
    } else {
        // Redirect back to the admin dashboard with an error message
        header('Location: Admin.php?status=error');
        exit();
    }
}

// Check if the form is submitted for updating the score
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameSelect'])) {
    // Get form data
    $matchId = $_POST['gameSelect'];
    $team1FinalScore = $_POST['team1Score'];
    $team2FinalScore = $_POST['team2Score'];
    $ExactscorePoints = $_POST['exactScorePoints'];
    $WinnerPoints = $_POST['winnerPoints'];

    // Create an instance of the Matches class
    $matches = new Matches();

    // Call the AddFinalScore function
    $result = $matches->AddFinalScore($matchId, $team1FinalScore, $team2FinalScore, $ExactscorePoints, $WinnerPoints);

    // Check if the score was updated successfully
    if ($result) {
        // Redirect back to the admin dashboard with a success message
        header('Location: Admin.php?status=success');
        exit();
    } else {
        // Redirect back to the admin dashboard with an error message
        header('Location: Admin.php?status=error');
        exit();
    }
}

// Display success or error message
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        echo '<div class="alert success">Match added successfully!</div>';
    } elseif ($_GET['status'] === 'error') {
        echo '<div class="alert error">Failed to add match. Please try again.</div>';
    }
}

$matches = new Matches();
$MatchesData = $matches->retrieveAllMatches();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PredictKing</title>
    <style>
        /* Add your CSS styles here */
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
            --success: #2ea44f;
            --warning: #d97706;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            min-height: 100vh;
        }

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
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .admin-header {
            margin-bottom: 2rem;
        }

        .admin-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .admin-subtitle {
            color: var(--secondary);
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .admin-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 1rem;
            background-color: white;
            cursor: pointer;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .submit-btn {
            width: 100%;
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

        .submit-btn.success {
            background-color: var(--success);
        }

        .submit-btn.success:hover {
            background-color: #2c974b;
        }

        .games-list {
            margin-top: 2rem;
        }

        .game-item {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1rem;
            align-items: center;
        }

        .game-info {
            display: grid;
            gap: 0.5rem;
        }

        .game-teams {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .game-meta {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .game-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: var(--warning);
            color: white;
        }

        .status-completed {
            background-color: var(--success);
            color: white;
        }

        .points-info {
            background-color: var(--background);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .points-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .points-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem;
            border-bottom: 1px solid var(--secondary);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .admin-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .game-item {
                grid-template-columns: 1fr;
            }
        }

        .menu-toggle {
            display: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
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
                background-color: var(--primary);
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu.active {
                display: flex;
            }
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .alert.success {
            background-color: var(--success);
            color: white;
        }

        .alert.error {
            background-color: var(--accent);
            color: white;
        }
        </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="index.html" class="nav-logo">PredictKing</a>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            <div class="nav-menu">
                <a href="Classes/Logout.php" class="nav-link">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="admin-header">
            <h1 class="admin-title">Admin Dashboard</h1>
            <p class="admin-subtitle">Manage games, scores, and point allocations</p>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-header">
                    <h2 class="card-title">Add New Game</h2>
                    <p class="card-subtitle">Create a new prediction game</p>
                </div>
                <div class="card-body">
                    <form id="addGameForm" method="POST" action="">
                        <!-- League Input Field -->
                        <div class="form-group">
                            <label class="form-label">League</label>
                            <input type="text" name="league" class="form-input" placeholder="Enter League Name" required>
                        </div>

                        <!-- Team 1 Details -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Team 1 Name</label>
                                <input type="text" name="team1Name" class="form-input" placeholder="Enter Team 1 Name" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Team 1 Image URL</label>
                                <input type="url" name="team1Logo" class="form-input" placeholder="Enter Team 1 Image Link" required>
                            </div>
                        </div>

                        <!-- Team 2 Details -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Team 2 Name</label>
                                <input type="text" name="team2Name" class="form-input" placeholder="Enter Team 2 Name" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Team 2 Image URL</label>
                                <input type="url" name="team2Logo" class="form-input" placeholder="Enter Team 2 Image Link" required>
                            </div>
                        </div>

                        <!-- Match Date & Time -->
                        <div class="form-group">
                            <label class="form-label">Match Date & Time</label>
                            <input type="datetime-local" name="matchDate" class="form-input" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="submit-btn">Add Game</button>
                    </form>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-header">
                    <h2 class="card-title">Update Game Score</h2>
                    <p class="card-subtitle">Add final scores and calculate points</p>
                </div>
                <div class="card-body">
                    <form id="updateScoreForm" method="POST" action="">
                        <div class="form-group">
                            <label for="gameSelect" class="form-label">Select Game</label>
                            <select id="gameSelect" class="form-select" name="gameSelect" required>
                                <option value="">Choose a game</option>
                                <?php foreach ($MatchesData as $match): ?>
                                    <option value="<?php echo htmlspecialchars($match['MatchID']); ?>">
                                        <?php echo htmlspecialchars($match['Team1Name']) . ' vs ' . htmlspecialchars($match['Team2Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Team 1 Score</label>
                                <input type="number" name="team1Score" class="form-input" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Team 2 Score</label>
                                <input type="number" name="team2Score" class="form-input" min="0" required>
                            </div>
                        </div>
                        <div class="points-info">
                            <h3 class="points-title">Point Allocation</h3>
                            <div class="points-grid">
                                <div class="points-item">
                                    <span>Exact Score</span>
                                    <input type="number" name="exactScorePoints" class="form-input" value="3" min="0">
                                </div>
                                <div class="points-item">
                                    <span>Correct Result</span>
                                    <input type="number" name="winnerPoints" class="form-input" value="1" min="0">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn success">Update Score & Calculate Points</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="games-list">
            <h2 class="admin-title">Games List</h2>
            <?php foreach ($MatchesData as $match): ?>
                <div class="game-item">
                    <div class="game-info">
                        <div class="game-teams"><?php echo htmlspecialchars($match['Team1Name']) . ' vs ' . htmlspecialchars($match['Team2Name']); ?></div>
                        <div class="game-meta"><?php echo htmlspecialchars($match['Tournament']); ?></div>
                    </div>
                    <?php if ($match['ongoing'] == 0): ?>
                        <div class="game-status status-pending">Pending</div>
                    <?php else: ?>
                        <div class="game-status status-completed">Finished</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.querySelector('.nav-menu');
            menu.classList.toggle('active');
        }
    </script>
</body>
</html>