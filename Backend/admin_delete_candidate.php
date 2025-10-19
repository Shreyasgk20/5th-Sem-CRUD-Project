<?php
// backend/admin_delete_candidate.php
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

$candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;

if ($candidate_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid candidate ID']);
    exit;
}

$mysqli = get_db();

try {
    // First, get the candidate's party symbol to delete the file
    $stmt = $mysqli->prepare("SELECT party_symbol FROM candidates WHERE id = ?");
    $stmt->bind_param('i', $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    $stmt->close();
    
    if (!$candidate) {
        echo json_encode(['success' => false, 'message' => 'Candidate not found']);
        exit;
    }
    
    // Delete the candidate from database
    $stmt = $mysqli->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param('i', $candidate_id);
    $success = $stmt->execute();
    $stmt->close();
    
    if ($success) {
        // Delete the party symbol file if it exists
        if (!empty($candidate['party_symbol'])) {
            $file_path = '../frontend/uploads/symbols/' . $candidate['party_symbol'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Candidate deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete candidate']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
