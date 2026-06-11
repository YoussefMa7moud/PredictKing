<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football News - PredictKing</title>
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

        .news-header {
            text-align: center;
        }

        .news-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .news-title span {
            color: var(--accent);
        }

        .news-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* News Cards Grid */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .news-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .news-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .news-image-wrapper {
            width: 100%;
            height: 200px;
            background-color: #252542;
            position: relative;
            overflow: hidden;
        }

        .news-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .news-card:hover .news-image-wrapper img {
            transform: scale(1.08);
        }

        .news-category {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--accent);
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 0 10px var(--accent-glow);
        }

        .news-content {
            padding: 1.5rem 1.75rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .news-date {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .news-headline {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 0.75rem;
            color: white;
        }

        .news-excerpt {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            flex: 1;
        }

        .read-more {
            display: inline-block;
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .read-more:hover {
            color: white;
            text-decoration: underline;
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

        /* Mobile responsive */
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
                <a href="Rules.php" class="nav-link">Rules</a>
                <a href="News.php" class="nav-link active">News</a>
                <a href="Classes/Logout.php" class="nav-link logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="news-header">
            <h1 class="news-title">World Cup <span>News 📰</span></h1>
            <p class="news-subtitle">Stay updated with the latest tournament feeds, matches, and transfer news</p>
        </div>

        <div class="news-grid">
            <?php
            // Function to fetch OpenGraph image from a URL
            function fetchImageFromUrl($url) {
                // BBC links require user-agent or context to prevent blocks, setting stream context
                $options = array(
                    'http' => array(
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                    )
                );
                $context = stream_context_create($options);
                $html = @file_get_contents($url, false, $context);
                if ($html && preg_match('/<meta[^>]+property="og:image"[^>]+content="([^">]+)"/', $html, $matches)) {
                    return $matches[1];
                }
                return 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=800&auto=format&fit=crop&q=60'; // High-quality football fallback
            }

            // RSS Feed URL (BBC Football News)
            $rssUrl = 'http://feeds.bbci.co.uk/sport/football/rss.xml';

            // Fetch and parse the RSS feed with user agent context
            $options = array(
                'http' => array(
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                )
            );
            $context = stream_context_create($options);
            $xmlContent = @file_get_contents($rssUrl, false, $context);
            $rss = $xmlContent ? @simplexml_load_string($xmlContent) : false;

            if ($rss) {
                $items = $rss->channel->item;
                $count = 0;
                foreach ($items as $item) {
                    if ($count >= 6) break;
                    $title = $item->title;
                    $link = $item->link;
                    $description = $item->description;
                    $pubDate = date('F j, Y', strtotime($item->pubDate));

                    $imageUrl = fetchImageFromUrl($link);
            ?>
                    <article class="news-card">
                        <div class="news-image-wrapper">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($title); ?>">
                            <span class="news-category">World Cup</span>
                        </div>
                        <div class="news-content">
                            <div class="news-date">📅 <?php echo $pubDate; ?></div>
                            <h2 class="news-headline"><?php echo htmlspecialchars($title); ?></h2>
                            <p class="news-excerpt"><?php echo htmlspecialchars($description); ?></p>
                            <a href="<?php echo htmlspecialchars($link); ?>" class="read-more" target="_blank">Read Full Article →</a>
                        </div>
                    </article>
            <?php
                    $count++;
                }
            } else {
                // Standback offline simulation if BBC RSS is blocked or slow
                for ($i = 1; $i <= 3; $i++) {
                    $mockTitles = [
                        1 => "World Cup Tournament Kicks Off: Top Contenders Analyzed",
                        2 => "Messi and Ronaldo Prepare for Final Showdown in Tournament",
                        3 => "Salah Ready to Lead Egypt to Historic Victory"
                    ];
                    $mockExcerpts = [
                        1 => "Experts weigh in on which national teams are primed to take home the trophy this year, highlighting squad depths and key tactical setups.",
                        2 => "The legendary duo look ahead to what could be their last international campaign as fans anticipate a dream finale matchups.",
                        3 => "Mohamed Salah declares squad is fully focused on delivering a strong performance for the country and making supporters proud."
                    ];
                    $mockImages = [
                        1 => "https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=800",
                        2 => "https://images.unsplash.com/photo-1522778119026-d647f0596c20?w=800",
                        3 => "https://images.unsplash.com/photo-1543351611-58f69d7c1781?w=800"
                    ];
            ?>
                    <article class="news-card">
                        <div class="news-image-wrapper">
                            <img src="<?php echo $mockImages[$i]; ?>" alt="Mock News">
                            <span class="news-category">World Cup</span>
                        </div>
                        <div class="news-content">
                            <div class="news-date">📅 <?php echo date('F j, Y'); ?></div>
                            <h2 class="news-headline"><?php echo $mockTitles[$i]; ?></h2>
                            <p class="news-excerpt"><?php echo $mockExcerpts[$i]; ?></p>
                            <a href="Home.php" class="read-more">Read Full Article →</a>
                        </div>
                    </article>
            <?php
                }
            }
            ?>
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