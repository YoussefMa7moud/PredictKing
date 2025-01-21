<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/UserPrediction.php';

class Matches extends UserPrediction {
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


    public function AddFinalScore($MatchID, $Team1FinalScore, $Team2FinalScore, $ExactscorePoints, $WinnerPoints) {
        // Debugging: Check input values
        error_log("AddFinalScore called with MatchID: $MatchID, Team1FinalScore: $Team1FinalScore, Team2FinalScore: $Team2FinalScore");
    
        // Update the match scores and ongoing status in the database
        $sql = "UPDATE matches SET Team1FinalScore = :team1FinalScore, Team2FinalScore = :team2FinalScore, ongoing = 1 WHERE MatchID = :matchId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':team1FinalScore', $Team1FinalScore);
        $stmt->bindParam(':team2FinalScore', $Team2FinalScore);
        $stmt->bindParam(':matchId', $MatchID);
        $result = $stmt->execute();
    
        // Debugging: Check if the SQL update was successful
        if ($result) {
            error_log("SQL Update Successful");
        } else {
            error_log("SQL Update Failed");
        }
    
        // If the update was successful, calculate the points
        if ($result) {
            // Debugging: Ensure CalculatePoints is called
            error_log("Calling CalculatePoints method");
            $this->CalculatePoints($MatchID, $Team1FinalScore, $Team2FinalScore, $ExactscorePoints, $WinnerPoints);
        }
    
        return $result;
    }
}
