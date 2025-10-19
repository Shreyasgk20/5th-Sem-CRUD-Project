<?php
// backend/admin_register_candidate.php
require_once 'db.php';
$BASE_FRONTEND = '/Online_Voting/frontend';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Admin login required'); window.location.href='{$BASE_FRONTEND}/admin.html';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$mysqli = get_db();

// Get form data
$candidate_name = trim($_POST['candidate_name'] ?? '');
$party_name = trim($_POST['party_name'] ?? '');
$constituency = trim($_POST['constituency'] ?? '');
$manifesto = trim($_POST['manifesto'] ?? '');

// Validation
$errors = [];

if (empty($candidate_name)) $errors[] = 'Candidate name is required';
if (empty($party_name)) $errors[] = 'Party name is required';
if (empty($constituency)) $errors[] = 'Constituency is required';
if (empty($manifesto)) $errors[] = 'Manifesto is required';

if (!empty($errors)) {
    $error_msg = implode(', ', $errors);
    echo "<script>alert('Validation Error: {$error_msg}'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
    exit;
}

// Handle file upload
$party_symbol = '';
if (isset($_FILES['party_symbol']) && $_FILES['party_symbol']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../frontend/uploads/symbols/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_info = pathinfo($_FILES['party_symbol']['name']);
    $file_extension = strtolower($file_info['extension']);
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Validate file extension
    if (!in_array($file_extension, $allowed_extensions)) {
        echo "<script>alert('Invalid file type. Only JPG, PNG, and GIF files are allowed.'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
        exit;
    }
    
    // Validate file size (2MB limit)
    if ($_FILES['party_symbol']['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('File size must be less than 2MB'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
        exit;
    }
    
    // Generate unique filename
    $party_symbol = 'symbol_' . time() . '_' . uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $party_symbol;
    
    // Move uploaded file
    if (!move_uploaded_file($_FILES['party_symbol']['tmp_name'], $upload_path)) {
        echo "<script>alert('Failed to upload file'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
        exit;
    }
} else {
    echo "<script>alert('Party symbol file is required'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
    exit;
}

// Insert candidate
try {
    $stmt = $mysqli->prepare("INSERT INTO candidates (name, party, party_symbol, manifesto, constituency) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $candidate_name, $party_name, $party_symbol, $manifesto, $constituency);
    
    if ($stmt->execute()) {
        echo "<script>alert('Candidate registered successfully!'); window.location.href='{$BASE_FRONTEND}/candidates_list.html';</script>";
    } else {
        echo "<script>alert('Failed to register candidate. Please try again.'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Registration failed: " . $e->getMessage() . "'); window.location.href='{$BASE_FRONTEND}/admin_register_candidate.html';</script>";
}

$stmt->close();
$mysqli->close();
?>
