<?php
// backend/vote.php
require_once 'db.php';
session_start();

// This file handles admin-only actions; enforce admin session
$BASE_FRONTEND = '/Online_Voting/Frontend';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    if (is_ajax()) { echo json_encode(['success'=>false,'message':'Admin login required']); } else { header('Location: ' . $BASE_FRONTEND . '/admin.html?error=login'); }
    exit;
}

$mysqli = get_db();
$action = $_POST['action'] ?? '';

if ($action === 'add_candidate') {
    $name = trim($_POST['name'] ?? '');
    $party = trim($_POST['party'] ?? '');
    $manifesto = trim($_POST['manifesto'] ?? '');
    if ($name === '') { echo json_encode(['success'=>false,'message'=>'Name required']); exit; }
    $stmt = $mysqli->prepare("INSERT INTO candidates (name, party, manifesto) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $party, $manifesto);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>$ok]);
    exit;
}

if ($action === 'add_voter') {
    $voter_id = trim($_POST['voter_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    if ($voter_id === '' || $full_name === '' || $dob === '') { echo json_encode(['success'=>false,'message'=>'All fields required']); exit; }
    $stmt = $mysqli->prepare("INSERT INTO voters (voter_id, full_name, dob, is_verified) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sssi', $voter_id, $full_name, $dob, $is_verified);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>$ok]);
    exit;
}

if ($action === 'get_stats') {
    require __DIR__ . '/get_stats.php';
    exit;
}

http_response_code(400);
echo json_encode(['success'=>false,'message'=>'Unknown action']);
