<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Rules - PredictKing</title>
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

        /* Container Layout */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 3rem;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .rules-header {
            text-align: center;
        }

        .rules-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .rules-title span {
            color: var(--accent);
        }

        .rules-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .rules-grid {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        /* Glass Cards */
        .rules-section {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .rules-section:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .section-header {
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-number {
            background: var(--accent);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 0 10px var(--accent-glow);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
        }

        .section-content {
            padding: 2rem;
        }

        .rules-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .rule-item {
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .rule-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .rule-icon {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            color: var(--accent);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }

        .rule-content h3 {
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .rule-content p {
            color: var(--text-secondary);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Points Table */
        .points-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .points-table th, .points-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .points-table th {
            background: rgba(0,0,0,0.2);
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .points-table td {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .points-table tr:last-child td {
            border-bottom: none;
        }

        .points-value {
            color: var(--success);
            font-weight: 800;
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

        /* Mobile */
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
                <a href="Badges.php" class="nav-link">Badges</a>
                <a href="Rules.php" class="nav-link active">Rules</a>
                <a href="News.php" class="nav-link">News</a>
                <a href="Classes/Logout.php" class="nav-link logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="rules-header">
            <h1 class="rules-title">How to <span>Play 📖</span></h1>
            <p class="rules-subtitle">Learn the rules and start accumulating points on the leaderboard</p>
        </div>

        <div class="rules-grid">
            <!-- Section 1 -->
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

            <!-- Section 2 -->
            <section class="rules-section">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <h2 class="section-title">Points System</h2>
                </div>
                <div class="section-content">
                    <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">Points are awarded based on the accuracy of your predictions. Multiplier matches (e.g. 2x, 3x) scale these values:</p>
                    <table class="points-table">
                        <thead>
                            <tr>
                                <th>Prediction Type</th>
                                <th>Base Points</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Exact Score</td>
                                <td class="points-value">+30 points</td>
                                <td>Correctly predicting both teams' exact scorelines (e.g., predicted 2-1 and ended 2-1)</td>
                            </tr>
                            <tr>
                                <td>Correct Outcome</td>
                                <td class="points-value">+15 points</td>
                                <td>Correctly predicting the match winner or draw outcome, but not the exact scoreline</td>
                            </tr>
                            <tr>
                                <td>Incorrect Result</td>
                                <td class="points-value">0 points</td>
                                <td>No points awarded for wrong outcomes or missing predictions</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 3 -->
            <section class="rules-section">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <h2 class="section-title">Achievements & Badges</h2>
                </div>
                <div class="section-content">
                    <ul class="rules-list">
                        <li class="rule-item">
                            <div class="rule-icon">🥉</div>
                            <div class="rule-content">
                                <h3>Bronze Shield Badge</h3>
                                <p>Automatically unlocked when your overall score crosses 50 total points.</p>
                            </div>
                        </li>
                        <li class="rule-item">
                            <div class="rule-icon">🥈</div>
                            <div class="rule-content">
                                <h3>Silver Shield Badge</h3>
                                <p>Automatically unlocked when your overall score crosses 100 total points.</p>
                            </div>
                        </li>
                        <li class="rule-item">
                            <div class="rule-icon">👑</div>
                            <div class="rule-content">
                                <h3>Gold Crown Badge</h3>
                                <p>The ultimate prestige! Unlocked at 200 total points. Displays next to your rank on the leaderboard.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
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
