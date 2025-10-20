<?php
require_once 'db.php';
session_start();

$BASE_FRONTEND = '/Online_Voting/frontend';
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

// Check if voter is logged in
if (!isset($_SESSION['voter_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$voter_id = (int)$_SESSION['voter_id'];
$candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;

if ($candidate_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'No candidate selected']);
    exit;
}

$mysqli = get_db();

// Check if voter already voted
$stmt_check = $mysqli->prepare("SELECT id FROM votes WHERE voter_id = ?");
$stmt_check->bind_param('i', $voter_id);
$stmt_check->execute();
$res_check = $stmt_check->get_result();
if ($res_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already voted']);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

// Record vote
$stmt = $mysqli->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
$stmt->bind_param('ii', $voter_id, $candidate_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vote cast successfully', 'redirect' => $BASE_FRONTEND . '/thanks.html']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cast vote. Try again later.']);
}

$stmt->close();
$mysqli->close();
