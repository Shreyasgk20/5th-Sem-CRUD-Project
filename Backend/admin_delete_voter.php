<?php
// backend/admin_delete_voter.php
require_once 'db.php';
$BASE_FRONTEND = '/Online_Voting/frontend';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin login required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$voter_id = isset($_POST['voter_id']) ? (int)$_POST['voter_id'] : 0;

if ($voter_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid voter ID']);
    exit;
}

$mysqli = get_db();

try {
    // Check if voter exists
    $stmt = $mysqli->prepare("SELECT id FROM voters WHERE id = ?");
    $stmt->bind_param('i', $voter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Voter not found']);
        exit;
    }
    $stmt->close();
    
    // Delete the voter (votes will be deleted due to CASCADE)
    $stmt = $mysqli->prepare("DELETE FROM voters WHERE id = ?");
    $stmt->bind_param('i', $voter_id);
    $success = $stmt->execute();
    $stmt->close();
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Voter deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete voter']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
