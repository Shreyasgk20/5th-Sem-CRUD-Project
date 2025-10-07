<?php
// backend/vote.php
require_once 'db.php';
$BASE_FRONTEND = '/Online_Voting/Frontend';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

if (!isset($_SESSION['voter_id'])) {
    if (is_ajax()) { echo json_encode(['success'=>false,'message'=>'Not logged in']); } else { header('Location: ' . $BASE_FRONTEND . '/voter_login.html?error=login'); }
    exit;
}

$mysqli = get_db();
$voter_id = (int)$_SESSION['voter_id'];
$candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;

if ($candidate_id <= 0) {
    if (is_ajax()) echo json_encode(['success'=>false,'message'=>'Invalid candidate']); else header('Location: ' . $BASE_FRONTEND . '/ballot.html?error=invalid');
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
$stmt->bind_param('ii', $voter_id, $candidate_id);
$ok = $stmt->execute();

if ($ok) {
    $stmt->close();
    if (is_ajax()) echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/thanks.html']); else header('Location: ' . $BASE_FRONTEND . '/thanks.html');
    exit;
} else {
    // Duplicate vote -> errno 1062
    $errno = $mysqli->errno;
    $stmt->close();
    if ($errno === 1062) {
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>'You already voted.']); else header('Location: ' . $BASE_FRONTEND . '/ballot.html?error=already');
    } else {
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>'Database error']); else header('Location: ' . $BASE_FRONTEND . '/ballot.html?error=db');
    }
    exit;
}
