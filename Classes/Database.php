<?php
class Database {
    private static $instance = null;
    private $pdo;

    // Private constructor to prevent direct instantiation
    private function __construct() {
        $host = 'localhost';
        $dbname = 'FootballPredictionGame';
        $username = 'root';
        $password = '';

        try {
            // Create a PDO connection
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

            // Set PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Method to get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the PDO connection
    public function getConnection() {
        return $this->pdo;
    }
}
?>