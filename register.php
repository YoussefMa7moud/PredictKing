<?php
session_start();
require_once __DIR__ . '/Classes/Database.php';
require_once __DIR__ . '/Classes/User.php';

$user = new User();
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $result = "Passwords do not match.";
    } else {
        $result = $user->createAccount($firstName, $lastName, $email, $password);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PredictKing</title>
    <link rel="icon" type="image/png" href="src/Screenshot 2025-02-10 153127.png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
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
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-15px) scale(1.02); }
            100% { transform: translateY(0px) scale(1); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--background);
            color: var(--text);
            display: flex;
            overflow-x: hidden;
        }

        .signup-container {
            flex: 1;
            display: flex;
            width: 100%;
        }

        /* Sidebar Styling */
        .signup-sidebar {
            width: 45%;
            background: linear-gradient(135deg, #111122 0%, #1e1e38 100%);
            border-right: 1px solid var(--glass-border);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .signup-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 10%, rgba(239, 35, 60, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 90% 80%, rgba(26, 54, 93, 0.2) 0%, transparent 60%);
            pointer-events: none;
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 10;
        }

        .brand-world-cup-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.5));
        }

        .brand-logo {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            letter-spacing: 1px;
        }

        .brand-logo span {
            color: var(--accent);
        }

        .sidebar-middle {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            z-index: 10;
            margin: 2rem 0;
        }

        .sidebar-player-image {
            width: 280px;
            height: 280px;
            object-fit: contain;
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 15px 20px rgba(0, 0, 0, 0.5));
        }

        .sidebar-promo-text {
            text-align: center;
            max-width: 360px;
        }

        .sidebar-promo-text h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #ffffff, #8d99ae);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-promo-text p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            z-index: 10;
        }

        .feature {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 1rem 1.5rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .feature:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--accent);
        }

        .feature-icon {
            font-size: 1.5rem;
        }

        .feature-info h4 {
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff;
        }

        .feature-info p {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Main Section Styling */
        .signup-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            position: relative;
        }

        .signup-main::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 10%;
            width: 250px;
            height: 250px;
            background: rgba(26, 54, 93, 0.05);
            filter: blur(100px);
            border-radius: 50%;
            pointer-events: none;
        }

        .signup-form {
            width: 100%;
            max-width: 520px;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .form-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 30%, #a0aec0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            color: var(--text-secondary);
            font-size: 1.1rem;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1.5px solid var(--glass-border);
            border-radius: 14px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 15px var(--accent-glow);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 2rem;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .terms-checkbox input[type="checkbox"] {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.1rem;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .terms-link {
            color: white;
            text-decoration: underline;
            font-weight: 600;
        }

        .terms-link:hover {
            color: var(--accent);
        }

        .submit-btn {
            width: 100%;
            padding: 1.1rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            background: #ff3355;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px var(--accent-glow);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn.loading {
            color: transparent;
            pointer-events: none;
        }

        .submit-btn.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 22px;
            height: 22px;
            margin: -11px 0 0 -11px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s infinite linear;
        }

        .login-prompt {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .login-link {
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
            margin-left: 0.25rem;
        }

        .login-link:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        .message {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.3s ease;
        }

        .message.success {
            background: rgba(46, 164, 79, 0.15);
            border: 1px solid var(--success);
            color: #58a6ff;
        }

        .message.error {
            background: rgba(239, 35, 60, 0.1);
            border: 1px solid var(--accent);
            color: #ff4d6d;
        }

        /* Responsive Layouts */
        @media (max-width: 1024px) {
            .signup-sidebar {
                display: none;
            }
            .signup-main {
                padding: 2rem;
                background: linear-gradient(135deg, #111122 0%, #0f0f1a 100%);
            }
        }

        @media (max-width: 480px) {
            .signup-main {
                padding: 1rem;
            }
            .signup-form {
                padding: 2rem 1.25rem;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .form-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <!-- World Cup Themed Sidebar -->
        <div class="signup-sidebar">
            <div class="brand-logo-container">
                <img src="src/world_cup_logo.png" alt="World Cup Trophy" class="brand-world-cup-logo">
                <div class="brand-logo">Predict<span>King</span></div>
            </div>
            
            <div class="sidebar-middle">
                <img src="src/ronaldo.png" alt="Cristiano Ronaldo Celebrating" class="sidebar-player-image">
                <div class="sidebar-promo-text">
                    <h2>Join Ronaldo on the pitch!</h2>
                    <p>Start your predicting career now, compete with users worldwide, and score points to reach the top level.</p>
                </div>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🎯</div>
                    <div class="feature-info">
                        <h4>Predict & Win</h4>
                        <p>Submit predictions and accumulate points for correct scores</p>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">🏆</div>
                    <div class="feature-info">
                        <h4>Compete Globally</h4>
                        <p>Climb leaderboard ranks and win custom medals</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signup Form Area -->
        <div class="signup-main">
            <form class="signup-form" method="POST" onsubmit="handleSubmit(event)" action="register.php">
                <div class="form-header">
                    <h1 class="form-title">Create Account</h1>
                    <p class="form-subtitle">Join the prediction community today</p>
                </div>

                <?php if (!empty($result)): ?>
                    <div class="message <?php echo ($result === 'Account created successfully!') ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($result); ?>
                    </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName" class="form-label">First Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="firstName" name="firstName" class="form-input" required placeholder="First name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="form-label">Last Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="lastName" name="lastName" class="form-input" required placeholder="Last name">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉️</span>
                        <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" class="form-input" required placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" required placeholder="Confirm">
                        </div>
                    </div>
                </div>

                <label class="terms-checkbox">
                    <input type="checkbox" required>
                    <span>I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
                </label>

                <button type="submit" class="submit-btn">Create Account</button>

                <div class="login-prompt">
                    Already registered? <a href="Login.php" class="login-link">Sign in here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleSubmit(event) {
            const form = event.target;
            const button = form.querySelector('.submit-btn');
            button.classList.add('loading');
        }
    </script>
</body>
</html>