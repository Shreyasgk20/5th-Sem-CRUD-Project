<?php
require_once 'db.php';
session_start();

// Only allow admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Not authorized']);
    exit;
}

$mysqli = get_db();
$voter_id = intval($_POST['voter_id'] ?? 0);

if ($voter_id <= 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid voter ID']);
    exit;
}

$stmt = $mysqli->prepare("UPDATE voters SET is_verified = 1 WHERE id = ?");
$stmt->bind_param("i", $voter_id);
$success = $stmt->execute();
$stmt->close();

echo json_encode(['success'=>$success, 'message'=> $success ? 'Voter verified' : 'Database error']);
