<?php


session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PredictKing</title>
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
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="index.html" class="nav-logo">PredictKing</a>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            <div class="nav-menu">
                <a href="#" class="nav-link">Dashboard</a>
                <a href="#" class="nav-link">Games</a>
                <a href="#" class="nav-link">Users</a>
                <a href="#" class="nav-link">Settings</a>
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
                    <form id="addGameForm">
                        <div class="form-group">
                            <label class="form-label">League</label>
                            <select class="form-select" required>
                                <option value="">Select League</option>
                                <option value="premier-league">Premier League</option>
                                <option value="la-liga">La Liga</option>
                                <option value="bundesliga">Bundesliga</option>
                                <option value="serie-a">Serie A</option>
                            </select>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Home Team</label>
                                <select class="form-select" required>
                                    <option value="">Select Team</option>
                                    <option value="arsenal">Arsenal</option>
                                    <option value="chelsea">Chelsea</option>
                                    <option value="liverpool">Liverpool</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Away Team</label>
                                <select class="form-select" required>
                                    <option value="">Select Team</option>
                                    <option value="arsenal">Arsenal</option>
                                    <option value="chelsea">Chelsea</option>
                                    <option value="liverpool">Liverpool</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Match Date & Time</label>
                            <input type="datetime-local" class="form-input" required>
                        </div>
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
                    <form id="updateScoreForm">
                        <div class="form-group">
                            <label class="form-label">Select Game</label>
                            <select class="form-select" required>
                                <option value="">Choose a game</option>
                                <option value="1">Arsenal vs Chelsea - 20:45 Today</option>
                                <option value="2">Barcelona vs Real Madrid - 21:00 Tomorrow</option>
                            </select>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Home Score</label>
                                <input type="number" class="form-input" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Away Score</label>
                                <input type="number" class="form-input" min="0" required>
                            </div>
                        </div>
                        <div class="points-info">
                            <h3 class="points-title">Point Allocation</h3>
                            <div class="points-grid">
                                <div class="points-item">
                                    <span>Exact Score</span>
                                    <input type="number" class="form-input" value="3" min="0">
                                </div>
                                <div class="points-item">
                                    <span>Correct Result</span>
                                    <input type="number" class="form-input" value="1" min="0">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn success">Update Score & Calculate Points</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="games-list">
            <h2 class="card-title">Recent Games</h2>
            <div class="game-item">
                <div class="game-info">
                    <div class="game-teams">Arsenal vs Chelsea</div>
                    <div class="game-meta">Premier League • Today, 20:45</div>
                </div>
                <span class="game-status status-pending">Pending</span>
            </div>
            <div class="game-item">
                <div class="game-info">
                    <div class="game-teams">Barcelona vs Real Madrid</div>
                    <div class="game-meta">La Liga • Tomorrow, 21:00</div>
                </div>
                <span class="game-status status-pending">Pending</span>
            </div>
            <div class="game-item">
                <div class="game-info">
                    <div class="game-teams">Liverpool vs Manchester United</div>
                    <div class="game-meta">Premier League • Yesterday, 18:30</div>
                    <div class="game-meta">Final Score: 2 - 1</div>
                </div>
                <span class="game-status status-completed">Completed</span>
            </div>
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

