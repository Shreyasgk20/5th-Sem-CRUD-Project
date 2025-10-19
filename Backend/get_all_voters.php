<?php
// backend/get_all_voters.php
require_once 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin login required']);
    exit;
}

header('Content-Type: application/json');

$mysqli = get_db();

try {
    $res = $mysqli->query("SELECT id, voter_id, full_name, email, is_verified, created_at FROM voters ORDER BY created_at DESC");
    $voters = [];
    
    while ($row = $res->fetch_assoc()) {
        $voters[] = $row;
    }
    
    echo json_encode($voters);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
