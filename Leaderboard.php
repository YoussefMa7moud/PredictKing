<?php
session_start();

require_once __DIR__ . '/Classes/User.php';

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

// Fetch all players ordered by TotalPoints DESC
$usersList = $user->retriveAllUserScore();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - PredictKing</title>
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
            
            /* Podium & Rank Colors */
            --gold: #ffd700;
            --gold-glow: rgba(255, 215, 0, 0.2);
            --silver: #c0c0c0;
            --silver-glow: rgba(192, 192, 192, 0.2);
            --bronze: #cd7f32;
            --bronze-glow: rgba(205, 127, 50, 0.2);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
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

        /* Main Container */
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

        /* Leaderboard Title */
        .leaderboard-header {
            text-align: center;
        }

        .leaderboard-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .leaderboard-title span {
            color: var(--accent);
        }

        .leaderboard-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Podium Layout */
        .podium-section {
            display: grid;
            grid-template-columns: 320px 1fr;
            align-items: flex-end;
            background: linear-gradient(135deg, var(--card-bg) 0%, #1e1e35 100%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            gap: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .podium-player-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
        }

        .podium-salah-image {
            width: 190px;
            height: 190px;
            object-fit: contain;
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.5));
        }

        .podium-player-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
        }

        .podium-player-card p {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* 3D Podium Graphic */
        .podium-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            height: 250px;
            gap: 1.5rem;
        }

        .podium-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 140px;
            position: relative;
        }

        .podium-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #252542;
            border: 2px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
            margin-bottom: 0.75rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .podium-avatar-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            text-align: center;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .podium-block {
            width: 100%;
            border-radius: 12px 12px 0 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-weight: 800;
            color: #000;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .podium-block span {
            font-size: 1rem;
            font-weight: 800;
            color: rgba(0, 0, 0, 0.6);
            margin-top: 0.25rem;
        }

        .podium-first {
            height: 140px;
            background: linear-gradient(180deg, var(--gold) 0%, #d4af37 100%);
            box-shadow: 0 0 20px var(--gold-glow);
            font-size: 2rem;
        }

        .podium-first .podium-avatar {
            border-color: var(--gold);
            box-shadow: 0 0 15px var(--gold-glow);
        }

        .podium-second {
            height: 100px;
            background: linear-gradient(180deg, var(--silver) 0%, #a9a9a9 100%);
            box-shadow: 0 0 20px var(--silver-glow);
            font-size: 1.6rem;
        }

        .podium-second .podium-avatar {
            border-color: var(--silver);
            box-shadow: 0 0 15px var(--silver-glow);
        }

        .podium-third {
            height: 70px;
            background: linear-gradient(180deg, var(--bronze) 0%, #b87333 100%);
            box-shadow: 0 0 20px var(--bronze-glow);
            font-size: 1.4rem;
        }

        .podium-third .podium-avatar {
            border-color: var(--bronze);
            box-shadow: 0 0 15px var(--bronze-glow);
        }

        /* Search Filter Styling */
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 0.5rem 1.5rem;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .search-icon {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-right: 0.75rem;
        }

        .search-input {
            background: transparent;
            border: none;
            color: white;
            font-size: 1rem;
            width: 100%;
            padding: 0.75rem 0;
            outline: none;
        }

        .search-input::placeholder {
            color: var(--text-secondary);
        }

        /* Detailed Rankings Table */
        .table-wrapper {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .rankings-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .rankings-table th, .rankings-table td {
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .rankings-table th {
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .rankings-table tr:last-child td {
            border-bottom: none;
        }

        .rankings-table tbody tr {
            transition: background-color 0.3s ease;
        }

        .rankings-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .rankings-table tbody tr.current-user {
            background-color: rgba(239, 35, 60, 0.04);
            border-left: 4px solid var(--accent);
        }

        .rankings-table tbody tr.current-user td {
            font-weight: 600;
        }

        .col-rank {
            font-size: 1.2rem;
            font-weight: 800;
            width: 80px;
        }

        .col-rank.rank-1 { color: var(--gold); }
        .col-rank.rank-2 { color: var(--silver); }
        .col-rank.rank-3 { color: var(--bronze); }

        .col-player {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
        }

        .col-player:hover {
            color: var(--accent);
        }

        .player-row-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #252542;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            border: 1px solid var(--glass-border);
        }

        .rank-1 .player-row-avatar { border-color: var(--gold); }
        .rank-2 .player-row-avatar { border-color: var(--silver); }
        .rank-3 .player-row-avatar { border-color: var(--bronze); }

        .player-row-name {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* Badges Slots in Leaderboard */
        .player-badges-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-slot {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .badge-slot:hover {
            transform: scale(1.15);
            background: rgba(255, 255, 255, 0.1);
        }

        .badge-slot.badge-gold { background: linear-gradient(135deg, var(--gold) 0%, #d4af37 100%); border-color: var(--gold); color: #000; }
        .badge-slot.badge-silver { background: linear-gradient(135deg, var(--silver) 0%, #a9a9a9 100%); border-color: var(--silver); color: #000; }
        .badge-slot.badge-bronze { background: linear-gradient(135deg, var(--bronze) 0%, #b87333 100%); border-color: var(--bronze); color: #fff; }

        .col-points {
            font-weight: 800;
            font-size: 1.1rem;
            text-align: right;
            width: 140px;
        }

        /* Tooltips */
        .badge-slot::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 130%;
            left: 50%;
            transform: translateX(-50%) translateY(5px);
            background: #0f0f1a;
            border: 1px solid var(--glass-border);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.2s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            z-index: 50;
        }

        .badge-slot:hover::before {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
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

        /* Mobile Responsive */
        @media (max-width: 900px) {
            .podium-section {
                grid-template-columns: 1fr;
                justify-items: center;
                padding: 2rem;
            }

            .podium-wrapper {
                width: 100%;
                max-width: 450px;
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

            .rankings-table th, .rankings-table td {
                padding: 1rem;
            }

            .badge-slot::before {
                display: none; /* Hide tooltips on mobile to prevent layout clipping */
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
                <a href="Leaderboard.php" class="nav-link active">Leaderboard</a>
                <a href="Badges.php" class="nav-link">Badges</a>
                <a href="Rules.php" class="nav-link">Rules</a>
                <a href="News.php" class="nav-link">News</a>
                <a href="Classes/Logout.php" class="nav-link logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Title -->
        <div class="leaderboard-header">
            <h1 class="leaderboard-title">Global <span>Leaderboard 🏆</span></h1>
            <p class="leaderboard-subtitle">Compete with predicting kings worldwide and claim your glory</p>
        </div>

        <!-- Salah Motivational and 3D Podium Graphic -->
        <section class="podium-section">
            <div class="podium-player-card">
                <img src="src/salah.png" alt="Mohamed Salah Celebrating" class="podium-salah-image">
                <div>
                    <h3>Mo Salah leads the cheer!</h3>
                    <p>"Stay consistent! The top spot on the podium is waiting for you. Out-predict your rivals and lock in the gold medal."</p>
                </div>
            </div>

            <!-- 3D Podium Render -->
            <div class="podium-wrapper">
                <!-- 2nd Place -->
                <?php if (isset($usersList[1])): ?>
                    <div class="podium-column">
                        <div class="podium-avatar"><?php echo strtoupper(substr($usersList[1]['FirstName'], 0, 1)); ?></div>
                        <div class="podium-avatar-name"><?php echo htmlspecialchars($usersList[1]['FirstName'] . ' ' . $usersList[1]['LastName']); ?></div>
                        <div class="podium-block podium-second">
                            2<span><?php echo $usersList[1]['TotalPoints']; ?> pt</span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 1st Place -->
                <?php if (isset($usersList[0])): ?>
                    <div class="podium-column">
                        <div class="podium-avatar"><?php echo strtoupper(substr($usersList[0]['FirstName'], 0, 1)); ?></div>
                        <div class="podium-avatar-name"><?php echo htmlspecialchars($usersList[0]['FirstName'] . ' ' . $usersList[0]['LastName']); ?></div>
                        <div class="podium-block podium-first">
                            1<span><?php echo $usersList[0]['TotalPoints']; ?> pt</span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3rd Place -->
                <?php if (isset($usersList[2])): ?>
                    <div class="podium-column">
                        <div class="podium-avatar"><?php echo strtoupper(substr($usersList[2]['FirstName'], 0, 1)); ?></div>
                        <div class="podium-avatar-name"><?php echo htmlspecialchars($usersList[2]['FirstName'] . ' ' . $usersList[2]['LastName']); ?></div>
                        <div class="podium-block podium-third">
                            3<span><?php echo $usersList[2]['TotalPoints']; ?> pt</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Live Search Bar -->
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="playerSearch" onkeyup="filterPlayers()" placeholder="Search players by name...">
        </div>

        <!-- Detailed Rankings Table -->
        <section class="table-wrapper">
            <table class="rankings-table" id="rankingsTable">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Player</th>
                        <th>Achievements / Badges</th>
                        <th style="text-align: right;">Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($usersList as $index => $player): 
                        $rank = $index + 1;
                        $rankClass = '';
                        if ($rank === 1) $rankClass = 'rank-1';
                        elseif ($rank === 2) $rankClass = 'rank-2';
                        elseif ($rank === 3) $rankClass = 'rank-3';

                        $isCurr = ($player['UserID'] == $userId) ? 'current-user' : '';
                        $playerPoints = intval($player['TotalPoints']);

                        // Calculate dynamic badges based on score
                        $badgesList = [];
                        if ($playerPoints >= 50) {
                            $badgesList[] = '<div class="badge-slot badge-bronze" data-tooltip="Bronze Badge Unlocked (50+ points)">🥉</div>';
                        }
                        if ($playerPoints >= 100) {
                            $badgesList[] = '<div class="badge-slot badge-silver" data-tooltip="Silver Badge Unlocked (100+ points)">🥈</div>';
                        }
                        if ($playerPoints >= 200) {
                            $badgesList[] = '<div class="badge-slot badge-gold" data-tooltip="Gold Crown Badge Unlocked (200+ points)">👑</div>';
                        }
                    ?>
                        <tr class="<?php echo $isCurr; ?>" data-playername="<?php echo strtolower($player['FirstName'] . ' ' . $player['LastName']); ?>">
                            <td class="col-rank <?php echo $rankClass; ?>"><?php echo $rank; ?></td>
                            <td>
                                <a href="personal_profile.php?user_id=<?php echo $player['UserID']; ?>" class="col-player <?php echo $rankClass; ?>">
                                    <div class="player-row-avatar"><?php echo strtoupper(substr($player['FirstName'], 0, 1)); ?></div>
                                    <div class="player-row-name">
                                        <?php echo htmlspecialchars($player['FirstName'] . ' ' . $player['LastName']); ?>
                                        <?php if ($player['UserID'] == $userId) echo ' <span style="font-size:0.75rem; color:var(--accent); font-weight:800; text-transform:uppercase;">(You)</span>'; ?>
                                    </div>
                                </a>
                            </td>
                            <td>
                                <div class="player-badges-container">
                                    <?php 
                                    if (count($badgesList) > 0) {
                                        foreach ($badgesList as $badgeHTML) {
                                            echo $badgeHTML;
                                        }
                                    } else {
                                        echo '<span style="font-size:0.8rem; color:var(--text-secondary); font-style:italic;">No badges earned yet</span>';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="col-points"><?php echo htmlspecialchars($player['TotalPoints']); ?> pts</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

        // Live Javascript Search Filter
        function filterPlayers() {
            const input = document.getElementById('playerSearch');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('rankingsTable');
            const tr = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < tr.length; i++) {
                const playerName = tr[i].getAttribute('data-playername');
                if (playerName) {
                    if (playerName.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
