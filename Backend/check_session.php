<?php
// backend/check_session.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Return JSON: logged_in true if voter session is set
if (isset($_SESSION['voter_id']) && intval($_SESSION['voter_id']) > 0) {
    echo json_encode(['logged_in' => true, 'voter_id' => intval($_SESSION['voter_id'])]);
    exit;
}

// Not logged in
echo json_encode(['logged_in' => false]);
exit;
