<?php
// backend/get_candidates.php
require_once 'db.php';
header('Content-Type: application/json');
$mysqli = get_db();
$res = $mysqli->query("SELECT id, name, party, LEFT(manifesto, 250) AS manifesto FROM candidates ORDER BY id");
$list = [];
while ($row = $res->fetch_assoc()) $list[] = $row;
echo json_encode($list);
