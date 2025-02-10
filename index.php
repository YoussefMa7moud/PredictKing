<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PredictKing - Loading</title>
    <link rel="icon" type="image/png" href="src/Screenshot 2025-02-10 153127.png">
    <style>
        @keyframes logoAnimation {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes textReveal {
            0% { width: 0; }
            100% { width: 100%; }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #e94560;
            --text: #ffffff;
        }

        body {
            background-color: var(--primary);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Loading Screen Styles */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-logo {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 2rem;
            animation: logoAnimation 1.5s ease-out;
        }

        .loading-logo span {
            color: var(--accent);
        }

        .loading-bar-container {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .loading-bar {
            height: 100%;
            background: var(--accent);
            animation: textReveal 4s linear forwards;
        }

        .loading-text {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: var(--text);
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .loading-logo {
                font-size: 2rem;
            }

            .loading-bar-container {
                width: 150px;
            }
        }

        @media (max-width: 480px) {
            .loading-logo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen">
        <div class="loading-logo">
            Predict<span>King</span>
        </div>
        <div class="loading-bar-container">
            <div class="loading-bar"></div>
        </div>
        <div class="loading-text">Loading amazing predictions...</div>
    </div>

    <script>
        // Redirect to another page after 4 seconds (matches animation duration)
        setTimeout(() => {
            window.location.href = "./Login.php"; // Ensure this path is correct
        }, 4000); // 4 seconds to match the loading animation duration
    </script>
</body>
</html>