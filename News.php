<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football News - PredictKing</title>
    <link rel="icon" type="image/png" href="src/Screenshot 2025-02-10 153127.png">
    <style>
        /* Your existing CSS styles */
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .news-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .news-title {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .news-subtitle {
            color: var(--secondary);
            font-size: 1.1rem;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .news-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        .news-image {
            width: 100%;
            height: 200px;
            background-color: var(--secondary);
            position: relative;
            overflow: hidden;
        }

        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-category {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--accent);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .news-content {
            padding: 1.5rem;
        }

        .news-date {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .news-headline {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .news-excerpt {
            color: var(--secondary);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .read-more {
            display: inline-block;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .read-more:hover {
            color: var(--primary);
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

            .news-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .news-title {
                font-size: 1.5rem;
            }

            .news-subtitle {
                font-size: 1rem;
            }

            .news-grid {
                grid-template-columns: 1fr;
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
        <div class="news-header">
            <h1 class="news-title">Latest Football News</h1>
            <p class="news-subtitle">Stay updated with the latest football news and transfers</p>
        </div>

        <div class="news-grid">
            <?php
            // Function to fetch OpenGraph image from a URL
            function fetchImageFromUrl($url) {
                $html = file_get_contents($url);
                if ($html && preg_match('/<meta[^>]+property="og:image"[^>]+content="([^">]+)"/', $html, $matches)) {
                    return $matches[1]; // Return the OpenGraph image URL
                }
                return 'https://via.placeholder.com/400x200'; // Fallback image
            }

            // RSS Feed URL (BBC Football News)
            $rssUrl = 'http://feeds.bbci.co.uk/sport/football/rss.xml';

            // Fetch and parse the RSS feed
            $rss = simplexml_load_file($rssUrl);

            if ($rss) {
                // Loop through the first 6 news items
                $items = $rss->channel->item;
                $count = 0;
                foreach ($items as $item) {
                    if ($count >= 6) break; // Limit to 6 items
                    $title = $item->title;
                    $link = $item->link;
                    $description = $item->description;
                    $pubDate = date('F j, Y', strtotime($item->pubDate));

                    // Fetch image using OpenGraph
                    $imageUrl = fetchImageFromUrl($link);

                    // Output the news card
                    echo "
                    <article class='news-card'>
                        <div class='news-image'>
                            <img src='$imageUrl' alt='$title'>
                            <span class='news-category'>Football</span>
                        </div>
                        <div class='news-content'>
                            <div class='news-date'>$pubDate</div>
                            <h2 class='news-headline'>$title</h2>
                            <p class='news-excerpt'>$description</p>
                            <a href='$link' class='read-more' target='_blank'>Read More →</a>
                        </div>
                    </article>
                    ";
                    $count++;
                }
            } else {
                echo "<p>Failed to fetch news. Please try again later.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>