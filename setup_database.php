<?php
// Database Setup Script for Online Voting System
// Run this file in your browser to set up the database automatically

// Database configuration
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'online_voting_simplified';

echo "<h2>Online Voting System - Database Setup</h2>";
echo "<p>Setting up database and tables...</p>";

try {
    // Connect to MySQL server (without database)
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "<p>✓ Connected to MySQL server</p>";
    
    // Read and execute the SQL setup file
    $sql = file_get_contents('sql/complete_setup.sql');
    
    if ($sql === false) {
        throw new Exception("Could not read SQL setup file");
    }
    
    echo "<p>✓ Read SQL setup file</p>";
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        if ($mysqli->query($statement)) {
            $success_count++;
            if (strpos($statement, 'CREATE TABLE') !== false) {
                $table_name = preg_match('/CREATE TABLE\s+(\w+)/i', $statement, $matches);
                if ($table_name) {
                    echo "<p>✓ Created table: " . $matches[1] . "</p>";
                }
            } elseif (strpos($statement, 'INSERT INTO') !== false) {
                $table_name = preg_match('/INSERT INTO\s+(\w+)/i', $statement, $matches);
                if ($table_name) {
                    echo "<p>✓ Inserted data into: " . $matches[1] . "</p>";
                }
            }
        } else {
            $error_count++;
            echo "<p style='color: red;'>✗ Error: " . $mysqli->error . "</p>";
            echo "<p style='color: red;'>Statement: " . htmlspecialchars(substr($statement, 0, 100)) . "...</p>";
        }
    }
    
    echo "<h3>Setup Summary</h3>";
    echo "<p>✓ Successful operations: $success_count</p>";
    echo "<p style='color: " . ($error_count > 0 ? 'red' : 'green') . ";'>✗ Errors: $error_count</p>";
    
    if ($error_count == 0) {
        echo "<h3 style='color: green;'>🎉 Database setup completed successfully!</h3>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ul>";
        echo "<li>Go to <a href='frontend/index.html'>Homepage</a></li>";
        echo "<li>Test <a href='frontend/voter_login.html'>Voter Login</a> (use VOTER001, 2000-01-01)</li>";
        echo "<li>Test <a href='frontend/admin.html'>Admin Panel</a> (username: admin, password: admin123)</li>";
        echo "<li>Test <a href='frontend/register_voter.html'>Voter Registration</a></li>";
        echo "</ul>";
        
        // Show sample data
        $mysqli->select_db($DB_NAME);
        echo "<h3>Sample Data Created:</h3>";
        
        // Show voters
        $result = $mysqli->query("SELECT voter_id, full_name, email, is_verified FROM voters LIMIT 3");
        echo "<h4>Sample Voters:</h4><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['voter_id'] . " - " . $row['full_name'] . " (" . $row['email'] . ") - " . ($row['is_verified'] ? 'Verified' : 'Pending') . "</li>";
        }
        echo "</ul>";
        
        // Show candidates
        $result = $mysqli->query("SELECT name, party, constituency FROM candidates LIMIT 3");
        echo "<h4>Sample Candidates:</h4><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['name'] . " (" . $row['party'] . ") - " . $row['constituency'] . "</li>";
        }
        echo "</ul>";
        
    } else {
        echo "<h3 style='color: red;'>❌ Setup completed with errors. Please check the errors above.</h3>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Setup failed: " . $e->getMessage() . "</h3>";
    echo "<p>Please check your MySQL connection settings in this file.</p>";
}

$mysqli->close();
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #003366; }
p { margin: 5px 0; }
ul { margin: 10px 0; }
a { color: #FF9933; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
