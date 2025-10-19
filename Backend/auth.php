<?php
require_once 'db.php';
session_start();
$BASE_FRONTEND = '/Online_Voting/frontend';

if (!function_exists('is_ajax')) {
    function is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}

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
    $dob_input = trim($_POST['dob'] ?? '');

    if ($voter_id === '' || $dob_input === '') {
        $msg = 'Fill all fields';
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>$msg]);
        else header('Location: ' . $BASE_FRONTEND . '/voter_login.html?error=1');
        exit;
    }

    // Support multiple DOB formats
    $dob_formats = [
        date('Y-m-d', strtotime($dob_input)),  // 2000-01-01
        date('d/m/Y', strtotime($dob_input)),  // 01/01/2000
        date('d-m-Y', strtotime($dob_input)),  // 01-01-2000
        date('Y/m/d', strtotime($dob_input)),  // 2000/01/01
    ];

    // Correctly build placeholders
    $placeholders = implode(' OR ', array_fill(0, count($dob_formats), 'dob = ?'));
    $sql = "SELECT id, full_name, is_verified FROM voters WHERE voter_id = ? AND ($placeholders)";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $mysqli->error);
        exit('Database error');
    }

    // Bind parameters dynamically
    $types = str_repeat('s', count($dob_formats) + 1);
    $params = array_merge([$voter_id], $dob_formats);
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row) {
        if (intval($row['is_verified']) === 1) {
            $_SESSION['voter_id'] = (int)$row['id'];
            $_SESSION['voter_name'] = $row['full_name'];
            session_regenerate_id(true);
            if (is_ajax()) echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/ballot.html']);
            else header('Location: ' . $BASE_FRONTEND . '/ballot.html');
        } else {
            $msg = 'Voter not verified';
            if (is_ajax()) echo json_encode(['success'=>false,'message'=>$msg]);
            else header('Location: ' . $BASE_FRONTEND . '/voter_login.html?error=not_verified');
        }
    } else {
        $msg = 'Invalid credentials';
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>$msg]);
        else header('Location: ' . $BASE_FRONTEND . '/voter_login.html?error=invalid');
    }
    exit;
}

// --- Admin login unchanged ---
if ($action === 'admin_login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $msg = 'Fill all fields';
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>$msg]);
        else header('Location: ' . $BASE_FRONTEND . '/admin.html?error=1');
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
        if (is_ajax()) echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/admin.html?logged=1']);
        else header('Location: ' . $BASE_FRONTEND . '/admin.html?logged=1');
    } else {
        $msg = 'Invalid admin credentials';
        if (is_ajax()) echo json_encode(['success'=>false,'message'=>$msg]);
        else header('Location: ' . $BASE_FRONTEND . '/admin.html?error=invalid');
    }
    exit;
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    if (is_ajax()) echo json_encode(['success'=>true,'redirect'=> $BASE_FRONTEND . '/index.html']);
    else header('Location: ' . $BASE_FRONTEND . '/index.html');
    exit;
}

http_response_code(400);
echo 'Unknown action';
