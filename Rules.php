<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Rules - PredictKing</title>
    <link rel="icon" type="image/png" href="src/Screenshot 2025-02-10 153127.png">
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
        }

        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
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


        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .rules-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: slideIn 0.5s ease-out;
        }

        .rules-title {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .rules-subtitle {
            color: var(--secondary);
            font-size: 1.1rem;
        }

        .rules-grid {
            display: grid;
            gap: 2rem;
        }

        .rules-section {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
        }

        .section-header {
            background: var(--primary);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-number {
            background: var(--accent);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .section-content {
            padding: 2rem;
        }

        .rules-list {
            list-style: none;
            display: grid;
            gap: 1.5rem;
        }

        .rule-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--background);
            transition: transform 0.3s ease;
        }

        .rule-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .rule-item:hover {
            transform: translateX(10px);
        }

        .rule-icon {
            background: var(--background);
            color: var(--accent);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .rule-content h3 {
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .rule-content p {
            color: var(--secondary);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .points-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .points-table th,
        .points-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--background);
        }

        .points-table th {
            background: var(--background);
            color: var(--primary);
            font-weight: 600;
        }

        .points-value {
            color: var(--success);
            font-weight: 600;
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
            .container {
                padding: 1rem;
            }

            .rules-title {
                font-size: 2rem;
            }

            .section-header {
                padding: 1rem;
            }

            .section-content {
                padding: 1.5rem;
            }

        }

        @media (max-width: 480px) {
            .rules-title {
                font-size: 1.5rem;
            }

            .rules-subtitle {
                font-size: 1rem;
            }

            .section-title {
                font-size: 1.1rem;
            }

            .rule-item {
                flex-direction: column;
            }

            .points-table {
                font-size: 0.9rem;
            }
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
        <div class="rules-header">
            <h1 class="rules-title">How to Play</h1>
            <p class="rules-subtitle">Learn the rules and start winning points</p>
        </div>

        <div class="rules-grid">
            <section class="rules-section">
                <div class="section-header">
                    <div class="section-number">1</div>
                    <h2 class="section-title">Basic Rules</h2>
                </div>
                <div class="section-content">
                    <ul class="rules-list">
                        <li class="rule-item">
                            <div class="rule-icon">✓</div>
                            <div class="rule-content">
                                <h3>Match Predictions</h3>
                                <p>Submit your score predictions before the match starts. Once the match begins, predictions are locked and cannot be changed.</p>
                            </div>
                        </li>
                        <li class="rule-item">
                            <div class="rule-icon">⏰</div>
                            <div class="rule-content">
                                <h3>Deadlines</h3>
                                <p>All predictions must be submitted at least 1 hour before kick-off. The system automatically closes predictions when this deadline is reached.</p>
                            </div>
                        </li>
                        <li class="rule-item">
                            <div class="rule-icon">🎯</div>
                            <div class="rule-content">
                                <h3>Score Format</h3>
                                <p>Enter your predicted scores as whole numbers (0-99) for both teams. Use the designated input fields for home and away teams.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="rules-section">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <h2 class="section-title">Points System</h2>
                </div>
                <div class="section-content">
                    <p style="margin-bottom: 1rem; color: var(--secondary);">Points are awarded based on the accuracy of your predictions:</p>
                    <table class="points-table">
                        <tr>
                            <th>Prediction Type</th>
                            <th>Points</th>
                            <th>Description</th>
                        </tr>
                        <tr>
                            <td>Exact Score</td>
                            <td class="points-value">30 points</td>
                            <td>Correctly predicting both teams' exact scores</td>
                        </tr>
                        <tr>
                            <td>Correct Result</td>
                            <td class="points-value">15 point</td>
                            <td>Correctly predicting the match outcome (win/draw) but not the exact score</td>
                        </tr>
                        <tr>
                            <td>Player Goals</td>
                            <td class="points-value">20points</td>
                            <td>Correctly predicting the exact goals the player sccored</td>
                        </tr>
                    </table>
                </div>
            </section>

            <section class="rules-section">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <h2 class="section-title">Rankings & Rewards</h2>
                </div>
                <div class="section-content">
                    <ul class="rules-list">
                        <li class="rule-item">
                            <div class="rule-icon">🏆</div>
                            <div class="rule-content">
                                <h3>Leaderboard</h3>
                                <p>Players are ranked based on their total points. The leaderboard is updated in real-time as matches are completed and points are awarded.</p>
                            </div>
                        </li>
                        <li class="rule-item">
                            <div class="rule-icon">⭐</div>
                            <div class="rule-content">
                                <h3>Weekly Prizes</h3>
                                <p>The top 3 predictors each week receive special badges and bonus points that contribute to their overall ranking.</p>
                            </div>
                        </li>
                        <li class="rule-item">
                            <div class="rule-icon">📈</div>
                            <div class="rule-content">
                                <h3>Season Rankings</h3>
                                <p>Points accumulate throughout the season. End-of-season rewards are given to the top performers across all competitions.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>

</body>
</html>

