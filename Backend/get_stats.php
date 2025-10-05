<?php
// backend/get_stats.php
require_once 'db.php';
session_start();
if (!isset($_SESSION['admin_id'])) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'login']); exit; }
$mysqli = get_db();
$out = [];
$res = $mysqli->query("SELECT COUNT(*) AS total_voters FROM voters");
$out['total_voters'] = $res->fetch_assoc()['total_voters'] ?? 0;
$res = $mysqli->query("SELECT COUNT(*) AS votes_cast FROM votes");
$out['votes_cast'] = $res->fetch_assoc()['votes_cast'] ?? 0;
$res = $mysqli->query("SELECT c.id, c.name, c.party, COUNT(v.id) AS votes FROM candidates c LEFT JOIN votes v ON c.id = v.candidate_id GROUP BY c.id ORDER BY votes DESC");
$out['by_candidate'] = [];
while ($r = $res->fetch_assoc()) $out['by_candidate'][] = $r;
header('Content-Type: application/json');
echo json_encode($out);
