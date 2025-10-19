<?php
// backend/vote.php - Simplified voting system
require_once 'db.php';
$BASE_FRONTEND = '/Online_Voting/frontend';
session_start();

// Simple validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$BASE_FRONTEND}/index.html");
    exit;
}

if (!isset($_SESSION['voter_id'])) {
    header("Location: {$BASE_FRONTEND}/voter_login.html");
    exit;
}

$voter_id = (int)$_SESSION['voter_id'];
$candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;

if ($candidate_id <= 0) {
    header("Location: {$BASE_FRONTEND}/ballot.html");
    exit;
}

$mysqli = get_db();

// Simple vote recording
try {
    $stmt = $mysqli->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $voter_id, $candidate_id);
    
    if ($stmt->execute()) {
        header("Location: {$BASE_FRONTEND}/thanks.html");
        exit;
    } else {
        // Check if already voted
        if ($mysqli->errno == 1062) {
            header("Location: {$BASE_FRONTEND}/voter_login.html?error=already_voted");
            exit;
        }
        header("Location: {$BASE_FRONTEND}/ballot.html?error=vote_failed");
        exit;
    }
} catch (Exception $e) {
    header("Location: {$BASE_FRONTEND}/ballot.html?error=system_error");
    exit;
}
