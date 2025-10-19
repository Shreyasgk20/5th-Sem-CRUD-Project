<?php
// backend/get_election_status.php
require_once 'db.php';
header('Content-Type: application/json');

$mysqli = get_db();

try {
    $res = $mysqli->query("SELECT is_open, results_published FROM election_status ORDER BY id DESC LIMIT 1");
    $status = $res->fetch_assoc();
    
    if (!$status) {
        // If no status exists, create default
        $mysqli->query("INSERT INTO election_status (is_open, results_published) VALUES (0, 0)");
        $status = ['is_open' => 0, 'results_published' => 0];
    }
    
    echo json_encode($status);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
