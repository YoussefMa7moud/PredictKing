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

    // Validate password match
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
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

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

        .loader {
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
            transition: opacity 0.5s ease-out;
        }

        .loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader-football {
            font-size: 4rem;
            animation: bounce 1s infinite ease-in-out;
        }

        .loader-logo {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-top: 1rem;
        }

        .loader-logo span {
            color: var(--accent);
        }

        body {
            min-height: 100vh;
            background: var(--primary);
            display: flex;
            overflow-x: hidden;
        }

        .signup-container {
            flex: 1;
            display: flex;
            position: relative;
        }

        .signup-sidebar {
            width: 40%;
            background: linear-gradient(135deg, #1a1b2e 0%, var(--primary) 100%);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            animation: slideIn 1s ease-out;
        }

        .signup-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.5;
        }

        .brand {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-out 0.5s both;
        }

        .brand-logo span {
            color: var(--accent);
        }

        .features {
            position: relative;
            z-index: 1;
        }

        .feature {
            color: white;
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-out;
            animation-fill-mode: both;
        }

        .feature:nth-child(1) { animation-delay: 0.7s; }
        .feature:nth-child(2) { animation-delay: 0.9s; }
        .feature:nth-child(3) { animation-delay: 1.1s; }

        .feature-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(239, 35, 60, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .feature-text {
            color: var(--secondary);
            line-height: 1.6;
        }

        .signup-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            animation: fadeIn 1s ease-out 0.3s both;
        }

        .signup-form {
            width: 100%;
            max-width: 500px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .form-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .form-subtitle {
            color: var(--secondary);
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            color: white;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 2rem;
            color: var(--secondary);
        }

        .terms-checkbox input[type="checkbox"] {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.2rem;
            accent-color: var(--accent);
        }

        .terms-link {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .terms-link:hover {
            color: var(--accent);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            background: #ff4d6d;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn.loading {
            color: transparent;
        }

        .submit-btn.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s infinite linear;
        }

        .social-login {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .social-btn {
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .login-prompt {
            text-align: center;
            margin-top: 2rem;
            color: var(--secondary);
        }

        .login-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link:hover {
            color: var(--accent);
        }

        @media (max-width: 1024px) {
            .signup-sidebar {
                display: none;
            }

            .signup-main {
                padding: 2rem;
            }
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .social-login {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="loader-football">⚽</div>
        <div class="loader-logo">Predict<span>King</span></div>
    </div>

    <div class="signup-container">
        <div class="signup-sidebar">
            <div class="brand">
                <div class="brand-logo">Predict<span>King</span></div>
            </div>
            <div class="features">
                <div class="feature">
                    <div class="feature-title">
                        <div class="feature-icon">🎯</div>
                        Predict & Win
                    </div>
                    <p class="feature-text">Make predictions on upcoming matches and earn points for accurate predictions.</p>
                </div>
                <div class="feature">
                    <div class="feature-title">
                        <div class="feature-icon">🏆</div>
                        Compete & Lead
                    </div>
                    <p class="feature-text">Climb the leaderboard rankings and compete with predictors worldwide.</p>
                </div>
                <div class="feature">
                    <div class="feature-title">
                        <div class="feature-icon">📊</div>
                        Track Progress
                    </div>
                    <p class="feature-text">Monitor your prediction accuracy and performance with detailed statistics.</p>
                </div>
            </div>
        </div>

        <div class="signup-main">
            <form class="signup-form" method="POST" action="register.php">
                <div class="form-header">
                    <h1 class="form-title">Create Account</h1>
                    <p class="form-subtitle">Join the prediction community today</p>
                </div>

                <?php if (!empty($result)): ?>
    <div class="message" style="text-align: center; margin-bottom: 1rem; color: <?php echo ($result === 'Account created successfully!') ? 'green' : 'red'; ?>;">
        <?php echo $result; ?>
    </div>
<?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" id="firstName" name="firstName" class="form-input" required placeholder="Enter your first name">
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" id="lastName" name="lastName" class="form-input" required placeholder="Enter your last name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" required placeholder="Create a password">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" required placeholder="Confirm your password">
                    </div>
                </div>

                <label class="terms-checkbox">
                    <input type="checkbox" required>
                    <span>I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
                </label>

                <button type="submit" class="submit-btn">Create Account</button>


                <div class="login-prompt">
                    Already have an account? <a href="Login.php" class="login-link">Sign in</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initial loading animation
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.querySelector('.loader').classList.add('hidden');
            }, 1500);
        });

        // Form submission handling with loading animation
        function handleSubmit(event) {
            event.preventDefault();
            const button = event.target.querySelector('.submit-btn');
            button.classList.add('loading');
            
            // Simulate API call
            setTimeout(() => {
                button.classList.remove('loading');
                // Add your actual form submission logic here
            }, 2000);
        }
    </script>
</body>
</html>