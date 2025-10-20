<?php
require_once 'db.php';
session_start();

$BASE_FRONTEND = '/Online_Voting/frontend';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

$action = $_POST['action'] ?? '';
$mysqli = get_db();

if ($action === 'voter_login') {
    $voter_id = trim($_POST['voter_id'] ?? '');
    $dob_input = trim($_POST['dob'] ?? '');

    if ($voter_id === '' || $dob_input === '') {
        echo json_encode(['success' => false, 'message' => 'Fill all fields']);
        exit;
    }

    $dob_formats = [
        date('Y-m-d', strtotime($dob_input)),
        date('d-m-Y', strtotime($dob_input)),
        date('d/m/Y', strtotime($dob_input)),
        date('Y/m/d', strtotime($dob_input)),
    ];

    // Build SQL with placeholders
    $placeholders = implode(' OR ', array_fill(0, count($dob_formats), 'dob = ?'));
    $sql = "SELECT id, full_name, is_verified FROM voters WHERE voter_id = ? AND ($placeholders)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }

    $types = str_repeat('s', count($dob_formats) + 1);
    $params = array_merge([$voter_id], $dob_formats);
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row) {
        if ((int)$row['is_verified'] === 1) {
            $_SESSION['voter_id'] = (int)$row['id'];
            $_SESSION['voter_name'] = $row['full_name'];
            session_regenerate_id(true);
            echo json_encode(['success' => true, 'redirect' => $BASE_FRONTEND . '/ballot.html']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Voter not verified']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
    exit;
}

// Other actions like admin_login or logout can be added similarly
echo json_encode(['success' => false, 'message' => 'Unknown action']);
exit;
