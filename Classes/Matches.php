<?php
require_once __DIR__ . '/Database.php';

class Matches {
    private $pdo;

    // Constructor to initialize the database connection
    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    public function AddMatch($tournament, $team1Name, $team1Logo, $team2Name, $team2Logo, $ongoing, $matchDate) {
        $sql = "INSERT INTO matches (Tournament, Team1Name, Team1Logo, Team2Name, Team2Logo, ongoing, date) 
                VALUES (:tournament, :team1Name, :team1Logo, :team2Name, :team2Logo, :ongoing, :matchDate)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':tournament', $tournament);
        $stmt->bindParam(':team1Name', $team1Name);
        $stmt->bindParam(':team1Logo', $team1Logo);
        $stmt->bindParam(':team2Name', $team2Name);
        $stmt->bindParam(':team2Logo', $team2Logo);
        $stmt->bindParam(':ongoing', $ongoing, PDO::PARAM_INT);
        $stmt->bindParam(':matchDate', $matchDate);
        
        return $stmt->execute();
    }

    public function GetMatches() {
        $sql = "SELECT * FROM matches";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function retrieveAllMatchesWithoutScore() {
        $sql = "SELECT * FROM matches WHERE Team1FinalScore IS NULL AND Team2FinalScore IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function retrieveAllMatches() {
        $sql = "SELECT * FROM matches";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function isMatchOpenForPredictions($matchId) {
        $sql = "SELECT date FROM matches WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$matchId]);
        $matchDate = $stmt->fetchColumn();
    
        if (!$matchDate) {
            return false; // Match not found
        }
    
        $currentDate = new DateTime('now', new DateTimeZone('Africa/Cairo'));
        $matchDate = new DateTime($matchDate, new DateTimeZone('Africa/Cairo'));
        $predictionCutoffTime = clone $matchDate;
        $predictionCutoffTime->modify('-1 hour');
    
        return ($currentDate < $predictionCutoffTime);
    }
}