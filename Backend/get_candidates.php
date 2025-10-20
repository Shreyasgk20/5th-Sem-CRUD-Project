<?php
// backend/get_candidates.php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

// get_db() must return mysqli
$mysqli = get_db();
if (!$mysqli) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT id, name, party, LEFT(manifesto, 250) AS manifesto FROM candidates ORDER BY id";
$res = $mysqli->query($sql);
if (!$res) {
    http_response_code(500);
    echo json_encode(['error' => 'DB query failed: ' . $mysqli->error]);
    exit;
}

$list = [];
while ($row = $res->fetch_assoc()) {
    $list[] = $row;
}

echo json_encode($list, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
$mysqli->close();
