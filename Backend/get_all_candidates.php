<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

// Only allow access if voter is logged in
if (!isset($_SESSION['voter_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$mysqli = get_db();

try {
    $res = $mysqli->query("SELECT id, name, party, party_symbol, LEFT(manifesto, 250) AS manifesto FROM candidates ORDER BY id");
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
