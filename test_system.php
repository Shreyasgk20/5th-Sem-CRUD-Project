<?php
// Test script to verify the Online Voting System is working correctly

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'online_voting_simplified';

echo "<h2>Online Voting System - System Test</h2>";

try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($mysqli->connect_error) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }
    
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test 1: Check if all tables exist
    $tables = ['voters', 'candidates', 'admins', 'votes', 'election_status'];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "<p style='color: green;'>✓ All required tables exist</p>";
    } else {
        echo "<p style='color: red;'>✗ Missing tables: " . implode(', ', $missing_tables) . "</p>";
    }
    
    // Test 2: Check voters table structure
    $result = $mysqli->query("DESCRIBE voters");
    $voter_columns = [];
    while ($row = $result->fetch_assoc()) {
        $voter_columns[] = $row['Field'];
    }
    
    $required_voter_columns = ['id', 'voter_id', 'full_name', 'dob', 'gender', 'address', 'state', 'district', 'email', 'phone', 'password_hash', 'is_verified'];
    $missing_columns = array_diff($required_voter_columns, $voter_columns);
    
    if (empty($missing_columns)) {
        echo "<p style='color: green;'>✓ Voters table has all required columns</p>";
    } else {
        echo "<p style='color: red;'>✗ Voters table missing columns: " . implode(', ', $missing_columns) . "</p>";
    }
    
    // Test 3: Check sample data
    $result = $mysqli->query("SELECT COUNT(*) as count FROM voters");
    $voter_count = $result->fetch_assoc()['count'];
    echo "<p>Voters in database: $voter_count</p>";
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM candidates");
    $candidate_count = $result->fetch_assoc()['count'];
    echo "<p>Candidates in database: $candidate_count</p>";
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM admins");
    $admin_count = $result->fetch_assoc()['count'];
    echo "<p>Admins in database: $admin_count</p>";
    
    // Test 4: Test voter login functionality
    $test_voter_id = 'VOTER001';
    $test_dob = '2000-01-01';
    
    $stmt = $mysqli->prepare("SELECT id, full_name, is_verified FROM voters WHERE voter_id = ? AND dob = ?");
    $stmt->bind_param('ss', $test_voter_id, $test_dob);
    $stmt->execute();
    $result = $stmt->get_result();
    $voter = $result->fetch_assoc();
    
    if ($voter && $voter['is_verified']) {
        echo "<p style='color: green;'>✓ Voter login test successful (VOTER001)</p>";
    } else {
        echo "<p style='color: red;'>✗ Voter login test failed</p>";
    }
    
    // Test 5: Test admin login functionality
    $test_username = 'admin';
    $test_password = 'admin123';
    
    $stmt = $mysqli->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param('s', $test_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    
    if ($admin && password_verify($test_password, $admin['password_hash'])) {
        echo "<p style='color: green;'>✓ Admin login test successful (admin/admin123)</p>";
    } else {
        echo "<p style='color: red;'>✗ Admin login test failed</p>";
    }
    
    // Test 6: Check file permissions
    $upload_dir = 'frontend/uploads/symbols/';
    if (is_dir($upload_dir)) {
        if (is_writable($upload_dir)) {
            echo "<p style='color: green;'>✓ Upload directory exists and is writable</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Upload directory exists but is not writable</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Upload directory does not exist</p>";
    }
    
    // Test 7: Check PHP extensions
    $required_extensions = ['mysqli', 'json', 'session'];
    $missing_extensions = [];
    
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            $missing_extensions[] = $ext;
        }
    }
    
    if (empty($missing_extensions)) {
        echo "<p style='color: green;'>✓ All required PHP extensions are loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ Missing PHP extensions: " . implode(', ', $missing_extensions) . "</p>";
    }
    
    echo "<h3>System Status: " . (empty($missing_tables) && empty($missing_columns) && empty($missing_extensions) ? "READY" : "NEEDS ATTENTION") . "</h3>";
    
    if (empty($missing_tables) && empty($missing_columns) && empty($missing_extensions)) {
        echo "<h3 style='color: green;'>🎉 System is ready for use!</h3>";
        echo "<p><strong>Quick Start:</strong></p>";
        echo "<ul>";
        echo "<li><a href='frontend/index.html' target='_blank'>Open Homepage</a></li>";
        echo "<li><a href='frontend/voter_login.html' target='_blank'>Test Voter Login</a> (VOTER001 / 2000-01-01)</li>";
        echo "<li><a href='frontend/admin.html' target='_blank'>Test Admin Panel</a> (admin / admin123)</li>";
        echo "<li><a href='frontend/register_voter.html' target='_blank'>Test Voter Registration</a></li>";
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Test failed: " . $e->getMessage() . "</p>";
}

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
h2, h3 { color: #003366; }
p { margin: 8px 0; padding: 5px; background: white; border-radius: 4px; }
ul { margin: 10px 0; }
a { color: #FF9933; text-decoration: none; font-weight: bold; }
a:hover { text-decoration: underline; }
</style>
