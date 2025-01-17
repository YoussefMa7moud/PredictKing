<?php
require_once __DIR__ . '/Database.php';

class Matches{
    private $pdo;

    // Constructor to initialize the database connection
    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }


    public function AddMatch($tournament, $team1Name, $team1Logo, $team2Name, $team2Logo, $finalScore, $ongoing, $matchDate) {
        $sql = "INSERT INTO matches (Tournament, Team1Name, Team1Logo, Team2Name, Team2Logo, FinalScore, ongoing, date) 
                VALUES (:tournament, :team1Name, :team1Logo, :team2Name, :team2Logo, :finalScore, :ongoing, :matchDate)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':tournament', $tournament);
        $stmt->bindParam(':team1Name', $team1Name);
        $stmt->bindParam(':team1Logo', $team1Logo);
        $stmt->bindParam(':team2Name', $team2Name);
        $stmt->bindParam(':team2Logo', $team2Logo);
        $stmt->bindParam(':finalScore', $finalScore);
        $stmt->bindParam(':ongoing', $ongoing, PDO::PARAM_INT);
        $stmt->bindParam(':matchDate', $matchDate);
        
        return $stmt->execute();
    }

}