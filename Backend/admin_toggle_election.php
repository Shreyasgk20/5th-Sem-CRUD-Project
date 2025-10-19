<?php
// backend/admin_toggle_election.php
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

$action = $_POST['action'] ?? '';
$mysqli = get_db();

try {
    if ($action === 'open_election') {
        // Open election
        $stmt = $mysqli->prepare("UPDATE election_status SET is_open = 1, results_published = 0 WHERE id = 1");
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Election opened successfully']);
        
    } elseif ($action === 'close_election') {
        // Close election
        $stmt = $mysqli->prepare("UPDATE election_status SET is_open = 0 WHERE id = 1");
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Election closed successfully']);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
