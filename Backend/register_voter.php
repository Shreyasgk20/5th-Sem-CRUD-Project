<?php
// backend/register_voter.php
require_once 'db.php';
$BASE_FRONTEND = '/Online_Voting/frontend';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$mysqli = get_db();

// Get form data
$voter_id = trim($_POST['voter_id'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$address = trim($_POST['address'] ?? '');
$state = trim($_POST['state'] ?? '');
$district = trim($_POST['district'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
$errors = [];

if (empty($voter_id)) $errors[] = 'Voter ID is required';
if (empty($full_name)) $errors[] = 'Full name is required';
if (empty($dob)) $errors[] = 'Date of birth is required';
if (empty($gender)) $errors[] = 'Gender is required';
if (empty($address)) $errors[] = 'Address is required';
if (empty($state)) $errors[] = 'State is required';
if (empty($district)) $errors[] = 'District is required';
if (empty($email)) $errors[] = 'Email is required';
if (empty($phone)) $errors[] = 'Phone number is required';
if (empty($password)) $errors[] = 'Password is required';

if (!empty($errors)) {
    $error_msg = implode(', ', $errors);
    echo "<script>alert('Validation Error: {$error_msg}'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}

// Validate password match
if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    echo "<script>alert('Password must be at least 6 characters long'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}

// Validate phone format (flexible Indian phone number validation)
// Accepts: 9876543210, +919876543210, +91-9876543210, 919876543210
$phone_cleaned = preg_replace('/[^0-9+]/', '', $phone); // Remove all non-numeric except +
if (preg_match('/^(\+91)?[6-9]\d{9}$/', $phone_cleaned) || preg_match('/^91[6-9]\d{9}$/', $phone_cleaned)) {
    // Valid phone number, clean it to store only digits
    $phone = preg_replace('/[^0-9]/', '', $phone_cleaned);
    if (strlen($phone) == 12 && substr($phone, 0, 2) == '91') {
        $phone = substr($phone, 2); // Remove country code if present
    }
} else {
    echo "<script>alert('Invalid phone number format. Please enter a valid 10-digit Indian mobile number (e.g., 9876543210 or +919876543210)'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}

// Check if voter ID already exists
$stmt = $mysqli->prepare("SELECT id FROM voters WHERE voter_id = ?");
$stmt->bind_param('s', $voter_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('Voter ID already exists'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}
$stmt->close();

// Check if email already exists
$stmt = $mysqli->prepare("SELECT id FROM voters WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('Email already registered'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    exit;
}
$stmt->close();

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert voter
try {
    $stmt = $mysqli->prepare("INSERT INTO voters (voter_id, full_name, dob, gender, address, state, district, email, phone, password_hash, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param('ssssssssss', $voter_id, $full_name, $dob, $gender, $address, $state, $district, $email, $phone, $password_hash);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Your account is pending verification. You will be notified once verified.'); window.location.href='{$BASE_FRONTEND}/voter_login.html';</script>";
    } else {
        echo "<script>alert('Registration failed. Please try again.'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Registration failed: " . $e->getMessage() . "'); window.location.href='{$BASE_FRONTEND}/register_voter.html';</script>";
}

$stmt->close();
$mysqli->close();
?>
