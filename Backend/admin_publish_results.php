<?php
// backend/admin_publish_results.php
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
    if ($action === 'publish_results') {
        // Publish results (close election and publish results)
        $stmt = $mysqli->prepare("UPDATE election_status SET is_open = 0, results_published = 1 WHERE id = 1");
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Results published successfully']);
        
    } elseif ($action === 'unpublish_results') {
        // Unpublish results
        $stmt = $mysqli->prepare("UPDATE election_status SET results_published = 0 WHERE id = 1");
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Results unpublished successfully']);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
