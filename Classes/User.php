<?php
require_once __DIR__ . '/Database.php';

class User {
    private $pdo;

    // Constructor to initialize the database connection
    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Method to create a new user account
    public function createAccount($firstName, $lastName, $email, $password) {
        // Validate input
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return "All fields are required.";
        }

        // Check if email already exists
        $stmt = $this->pdo->prepare("SELECT UserID FROM User WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return "Email already exists.";
        }

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $stmt = $this->pdo->prepare("INSERT INTO User (FirstName, LastName, Email, Password, TotalPoints) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$firstName, $lastName, $email, $passwordHash]);

        return "Account created successfully!";
    }

    // Method to log in a user
public function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return "Email and password are required.";
        }
    
        // Fetch user from the database
        $stmt = $this->pdo->prepare("SELECT UserID, Password, type FROM User WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verify user and password
        if ($user && password_verify($password, $user['Password'])) {
            // Start a session and store user ID and role
            session_start();
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Role'] = $user['type']; // Use 'type' as stored in the database
    
            // Check if the user is an admin or a regular user
            if ($user['type'] === 'admin') {
                return "Admin login successful!";
            } else {
                return "User login successful!";
            }
        } else {
            return "Invalid email or password.";
        }
    }
    
    public function retrieveUserDataWithId($id) {
        // Fetch user from the database
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE UserID = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function isLoggedIn() {
        // Start the session if it hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Check if the UserID session variable is set
        return isset($_SESSION['UserID']);
    }

    public function retriveAllUserScore() {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE type = 'user' ORDER BY TotalPoints DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
}