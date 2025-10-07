<?php
// backend/vote.php
require_once 'db.php';
$BASE_FRONTEND = '/Online_Voting/Frontend';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "<script>alert('Method not allowed'); window.location.href='{$BASE_FRONTEND}/index.html';</script>";
    exit;
}

if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Please login to vote.'); window.location.href='{$BASE_FRONTEND}/voter_login.html';</script>";
    exit;
}

$mysqli = get_db();
$voter_id = (int)$_SESSION['voter_id'];
$candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;

if ($candidate_id <= 0) {
    echo "<script>alert('Please select a valid candidate.'); window.location.href='{$BASE_FRONTEND}/ballot.html';</script>";
    exit;
}

// Attempt to record the vote with graceful handling of duplicate and DB errors
try {
    $stmt = $mysqli->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $voter_id, $candidate_id);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        echo "<script>alert('Vote recorded successfully!'); window.location.href='{$BASE_FRONTEND}/thanks.html';</script>";
        exit;
    }

    // Fallback: if execution did not throw but failed without errno
    echo "<script>alert('We could not record your vote. Please try again.'); window.location.href='{$BASE_FRONTEND}/ballot.html';</script>";
    exit;
} catch (mysqli_sql_exception $e) {
    // 1062 = duplicate key violation (already voted)
    if ((int)$e->getCode() === 1062) {
        echo "<script>alert('You have already voted!'); window.location.href='{$BASE_FRONTEND}/voter_login.html';</script>";
        exit;
    }
    // Any other DB error
    echo "<script>alert('A database error occurred. Please try again later.'); window.location.href='{$BASE_FRONTEND}/ballot.html';</script>";
    exit;
}
