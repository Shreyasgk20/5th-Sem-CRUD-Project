<?php
require_once 'db.php';
session_start();

// Only allow admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$mysqli = get_db();

$result = $mysqli->query("SELECT id, voter_id, full_name FROM voters WHERE is_verified = 0 ORDER BY id ASC");
$voters = [];
while ($row = $result->fetch_assoc()) {
    $voters[] = $row;
}

header('Content-Type: application/json');
echo json_encode($voters);
