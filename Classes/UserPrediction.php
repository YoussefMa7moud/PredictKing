<?php
require_once __DIR__ . '/Database.php';

class UserPrediction extends Matches {
    // Class properties
    private $UserPredictionID;
    private $UserID;
    private $MatchID;
    private $Team1Score;
    private $Team2Score;

    // Constructor
    public function __construct($UserID = null, $MatchID = null, $Team1Score = null, $Team2Score = null) {
        $this->UserID = $UserID;
        $this->MatchID = $MatchID;
        $this->Team1Score = $Team1Score;
        $this->Team2Score = $Team2Score;
    }

    // Getters
    public function getUserPredictionID() {
        return $this->UserPredictionID;
    }

    public function getUserID() {
        return $this->UserID;
    }

    public function getMatchID() {
        return $this->MatchID;
    }

    public function getTeam1Score() {
        return $this->Team1Score;
    }

    public function getTeam2Score() {
        return $this->Team2Score;
    }

    // Setters
    public function setUserPredictionID($UserPredictionID) {
        $this->UserPredictionID = $UserPredictionID;
    }

    public function setUserID($UserID) {
        $this->UserID = $UserID;
    }

    public function setMatchID($MatchID) {
        $this->MatchID = $MatchID;
    }

    public function setTeam1Score($Team1Score) {
        $this->Team1Score = $Team1Score;
    }

    public function setTeam2Score($Team2Score) {
        $this->Team2Score = $Team2Score;
    }

    // Method to save the prediction to the database
    public function save($UserID, $MatchID, $Team1Score, $Team2Score) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Check if the user has already predicted this match
        if ($this->hasUserPredicted($UserID, $MatchID)) {
            return false;
        }

        $sql = "INSERT INTO userprediction (UserID, MatchID, Team1Score, Team2Score) 
                VALUES (:UserID, :MatchID, :Team1Score, :Team2Score)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':UserID', $UserID);
        $stmt->bindParam(':MatchID', $MatchID);
        $stmt->bindParam(':Team1Score', $Team1Score);
        $stmt->bindParam(':Team2Score', $Team2Score);

        return $stmt->execute();
    }

    // Method to check if a user has already predicted a match
    public static function hasUserPredicted($UserID, $MatchID) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $sql = "SELECT UserID FROM userprediction WHERE UserID = :UserID AND MatchID = :MatchID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':UserID', $UserID);
        $stmt->bindParam(':MatchID', $MatchID);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }


    public static function getUserPrediction($UserID, $MatchID) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
    
        $sql = "SELECT Team1Score, Team2Score FROM userprediction WHERE UserID = :UserID AND MatchID = :MatchID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':UserID', $UserID);
        $stmt->bindParam(':MatchID', $MatchID);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}