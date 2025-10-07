<?php
// backend/auth.php
require_once 'db.php';
// Use absolute path base under XAMPP
$BASE_FRONTEND = '/Online_Voting/Frontend';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo "Only POST allowed";
    exit;
}

$action = $_POST['action'] ?? '';

$mysqli = get_db();

if ($action === 'voter_login') {
    $voter_id = trim($_POST['voter_id'] ?? '');
    $dob = trim($_POST['dob'] ?? '');

    if ($voter_id === '' || $dob === '') {
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>'Fill all fields']); else header('Location: ' . $BASE_FRONTEND . '/voter_login.html?error=1');
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, full_name, is_verified FROM voters WHERE voter_id = ? AND dob = ?");
    $stmt->bind_param('ss', $voter_id, $dob);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row && intval($row['is_verified']) === 1) {
        $_SESSION['voter_id'] = (int)$row['id'];
        $_SESSION['voter_name'] = $row['full_name'];
        session_regenerate_id(true);
        if (is_ajax()) {
            echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/ballot.html']);
        } else {
            header('Location: ' . $BASE_FRONTEND . '/ballot.html');
        }
        exit;
    } else {
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>'Invalid credentials or not verified.']); else header('Location: ' . $BASE_FRONTEND . '/voter_login.html?error=invalid');
        exit;
    }
}

if ($action === 'admin_login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>'Fill all fields']); else header('Location: ' . $BASE_FRONTEND . '/admin.html?error=1');
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['admin_id'] = (int)$row['id'];
        session_regenerate_id(true);
        if (is_ajax()) echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/admin.html?logged=1']); else header('Location: ' . $BASE_FRONTEND . '/admin.html?logged=1');
        exit;
    } else {
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>'Invalid admin credentials']); else header('Location: ' . $BASE_FRONTEND . '/admin.html?error=invalid');
        exit;
    }
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    if (is_ajax()) echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/index.html']); else header('Location: ' . $BASE_FRONTEND . '/index.html');
    exit;
}

http_response_code(400);
echo 'Unknown action';
