<?php
// Final verification script for Online Voting System
// This script checks all components and provides a complete status report

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'online_voting_simplified';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Online Voting System - Installation Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #003366; text-align: center; }
        h2 { color: #FF9933; border-bottom: 2px solid #FF9933; padding-bottom: 5px; }
        .status { padding: 10px; margin: 5px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #dee2e6; border-radius: 4px; }
        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 20px 0; }
        .quick-links a { display: block; padding: 10px; background: #FF9933; color: white; text-decoration: none; border-radius: 4px; text-align: center; }
        .quick-links a:hover { background: #e6891a; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🏛️ Online Voting System - Installation Verification</h1>";

$all_tests_passed = true;
$test_results = [];

// Test 1: Database Connection
echo "<div class='test-section'>";
echo "<h2>1. Database Connection</h2>";

try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "<div class='status success'>✓ Database connection successful</div>";
    $test_results['database'] = true;
    
} catch (Exception $e) {
    echo "<div class='status error'>✗ Database connection failed: " . $e->getMessage() . "</div>";
    $test_results['database'] = false;
    $all_tests_passed = false;
}
echo "</div>";

if ($test_results['database']) {
    // Test 2: Table Structure
    echo "<div class='test-section'>";
    echo "<h2>2. Database Tables</h2>";
    
    $required_tables = ['voters', 'candidates', 'admins', 'votes', 'election_status'];
    $existing_tables = [];
    
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $existing_tables[] = $row[0];
    }
    
    $missing_tables = array_diff($required_tables, $existing_tables);
    
    if (empty($missing_tables)) {
        echo "<div class='status success'>✓ All required tables exist</div>";
        $test_results['tables'] = true;
    } else {
        echo "<div class='status error'>✗ Missing tables: " . implode(', ', $missing_tables) . "</div>";
        $test_results['tables'] = false;
        $all_tests_passed = false;
    }
    echo "</div>";
    
    // Test 3: Voters Table Structure
    echo "<div class='test-section'>";
    echo "<h2>3. Voters Table Structure</h2>";
    
    $result = $mysqli->query("DESCRIBE voters");
    $voter_columns = [];
    while ($row = $result->fetch_assoc()) {
        $voter_columns[] = $row['Field'];
    }
    
    $required_voter_columns = ['id', 'voter_id', 'full_name', 'dob', 'gender', 'address', 'state', 'district', 'email', 'phone', 'password_hash', 'is_verified'];
    $missing_columns = array_diff($required_voter_columns, $voter_columns);
    
    if (empty($missing_columns)) {
        echo "<div class='status success'>✓ Voters table has all required columns</div>";
        $test_results['voter_structure'] = true;
    } else {
        echo "<div class='status error'>✗ Voters table missing columns: " . implode(', ', $missing_columns) . "</div>";
        $test_results['voter_structure'] = false;
        $all_tests_passed = false;
    }
    echo "</div>";
    
    // Test 4: Sample Data
    echo "<div class='test-section'>";
    echo "<h2>4. Sample Data</h2>";
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM voters");
    $voter_count = $result->fetch_assoc()['count'];
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM candidates");
    $candidate_count = $result->fetch_assoc()['count'];
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM admins");
    $admin_count = $result->fetch_assoc()['count'];
    
    if ($voter_count > 0 && $candidate_count > 0 && $admin_count > 0) {
        echo "<div class='status success'>✓ Sample data loaded successfully</div>";
        echo "<table>";
        echo "<tr><th>Table</th><th>Records</th></tr>";
        echo "<tr><td>Voters</td><td>$voter_count</td></tr>";
        echo "<tr><td>Candidates</td><td>$candidate_count</td></tr>";
        echo "<tr><td>Admins</td><td>$admin_count</td></tr>";
        echo "</table>";
        $test_results['sample_data'] = true;
    } else {
        echo "<div class='status error'>✗ Sample data not loaded properly</div>";
        $test_results['sample_data'] = false;
        $all_tests_passed = false;
    }
    echo "</div>";
    
    // Test 5: Authentication Test
    echo "<div class='test-section'>";
    echo "<h2>5. Authentication System</h2>";
    
    // Test voter login
    $test_voter_id = 'VOTER001';
    $test_dob = '2000-01-01';
    
    $stmt = $mysqli->prepare("SELECT id, full_name, is_verified FROM voters WHERE voter_id = ? AND dob = ?");
    $stmt->bind_param('ss', $test_voter_id, $test_dob);
    $stmt->execute();
    $result = $stmt->get_result();
    $voter = $result->fetch_assoc();
    
    $voter_login_ok = ($voter && $voter['is_verified']);
    
    // Test admin login
    $test_username = 'admin';
    $test_password = 'admin123';
    
    $stmt = $mysqli->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param('s', $test_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    
    $admin_login_ok = ($admin && password_verify($test_password, $admin['password_hash']));
    
    if ($voter_login_ok && $admin_login_ok) {
        echo "<div class='status success'>✓ Authentication system working</div>";
        echo "<div class='status info'>Voter login: VOTER001 / 2000-01-01</div>";
        echo "<div class='status info'>Admin login: admin / admin123</div>";
        $test_results['authentication'] = true;
    } else {
        echo "<div class='status error'>✗ Authentication system not working</div>";
        $test_results['authentication'] = false;
        $all_tests_passed = false;
    }
    echo "</div>";
    
    // Test 6: File System
    echo "<div class='test-section'>";
    echo "<h2>6. File System</h2>";
    
    $upload_dir = 'frontend/uploads/symbols/';
    $file_tests = [];
    
    // Check if upload directory exists
    $file_tests['upload_dir_exists'] = is_dir($upload_dir);
    
    // Check if upload directory is writable
    $file_tests['upload_dir_writable'] = is_writable($upload_dir);
    
    // Check if key files exist
    $key_files = [
        'frontend/index.html',
        'frontend/voter_login.html',
        'frontend/register_voter.html',
        'frontend/admin.html',
        'frontend/ballot.html',
        'frontend/results.html',
        'frontend/candidates_list.html',
        'frontend/how_to_vote.html',
        'backend/auth.php',
        'backend/vote.php',
        'backend/register_voter.php'
    ];
    
    $missing_files = [];
    foreach ($key_files as $file) {
        if (!file_exists($file)) {
            $missing_files[] = $file;
        }
    }
    $file_tests['key_files_exist'] = empty($missing_files);
    
    if ($file_tests['upload_dir_exists'] && $file_tests['upload_dir_writable'] && $file_tests['key_files_exist']) {
        echo "<div class='status success'>✓ File system ready</div>";
        $test_results['file_system'] = true;
    } else {
        echo "<div class='status error'>✗ File system issues detected</div>";
        if (!$file_tests['upload_dir_exists']) echo "<div class='status error'>- Upload directory missing</div>";
        if (!$file_tests['upload_dir_writable']) echo "<div class='status error'>- Upload directory not writable</div>";
        if (!$file_tests['key_files_exist']) echo "<div class='status error'>- Missing files: " . implode(', ', $missing_files) . "</div>";
        $test_results['file_system'] = false;
        $all_tests_passed = false;
    }
    echo "</div>";
    
    // Test 7: PHP Extensions
    echo "<div class='test-section'>";
    echo "<h2>7. PHP Extensions</h2>";
    
    $required_extensions = ['mysqli', 'json', 'session', 'fileinfo'];
    $missing_extensions = [];
    
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            $missing_extensions[] = $ext;
        }
    }
    
    if (empty($missing_extensions)) {
        echo "<div class='status success'>✓ All required PHP extensions loaded</div>";
        $test_results['php_extensions'] = true;
    } else {
        echo "<div class='status error'>✗ Missing PHP extensions: " . implode(', ', $missing_extensions) . "</div>";
        $test_results['php_extensions'] = false;
        $all_tests_passed = false;
    }
    echo "</div>";
}

// Final Status
echo "<div class='test-section'>";
echo "<h2>📊 Overall Status</h2>";

if ($all_tests_passed) {
    echo "<div class='status success' style='font-size: 18px; font-weight: bold;'>🎉 ALL TESTS PASSED - SYSTEM IS READY!</div>";
    echo "<p>Your Online Voting System is fully installed and ready for use.</p>";
} else {
    echo "<div class='status error' style='font-size: 18px; font-weight: bold;'>❌ SOME TESTS FAILED - SYSTEM NEEDS ATTENTION</div>";
    echo "<p>Please fix the issues above before using the system.</p>";
}

echo "</div>";

// Quick Links
if ($all_tests_passed) {
    echo "<div class='test-section'>";
    echo "<h2>🚀 Quick Start</h2>";
    echo "<div class='quick-links'>";
    echo "<a href='frontend/index.html' target='_blank'>🏠 Homepage</a>";
    echo "<a href='frontend/voter_login.html' target='_blank'>👤 Voter Login</a>";
    echo "<a href='frontend/register_voter.html' target='_blank'>📝 Register Voter</a>";
    echo "<a href='frontend/admin.html' target='_blank'>⚙️ Admin Panel</a>";
    echo "<a href='frontend/candidates_list.html' target='_blank'>👥 View Candidates</a>";
    echo "<a href='frontend/results.html' target='_blank'>📊 View Results</a>";
    echo "<a href='frontend/how_to_vote.html' target='_blank'>❓ How to Vote</a>";
    echo "</div>";
    echo "</div>";
}

echo "</div></body></html>";
?>
