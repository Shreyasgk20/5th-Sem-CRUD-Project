<?php
// backend/get_all_candidates.php
require_once 'db.php';
header('Content-Type: application/json');

$mysqli = get_db();

try {
    $res = $mysqli->query("SELECT id, name, party, party_symbol, manifesto, constituency, created_at FROM candidates ORDER BY id");
    $candidates = [];
    
    while ($row = $res->fetch_assoc()) {
        $candidates[] = $row;
    }
    
    echo json_encode($candidates);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
