<?php
// Simple Database Setup - No complex features, just basic CRUD
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'online_voting_simplified';

echo "<h2>Simple Database Setup</h2>";

try {
    // Connect to MySQL
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "<p>✓ Connected to MySQL</p>";
    
    // Create database
    $mysqli->query("CREATE DATABASE IF NOT EXISTS $DB_NAME");
    $mysqli->select_db($DB_NAME);
    echo "<p>✓ Database created/selected</p>";
    
    // Drop existing tables
    $mysqli->query("DROP TABLE IF EXISTS votes");
    $mysqli->query("DROP TABLE IF EXISTS candidates");
    $mysqli->query("DROP TABLE IF EXISTS voters");
    $mysqli->query("DROP TABLE IF EXISTS admins");
    $mysqli->query("DROP TABLE IF EXISTS election_status");
    echo "<p>✓ Old tables removed</p>";
    
    // Create voters table
    $mysqli->query("CREATE TABLE voters (
        id INT AUTO_INCREMENT PRIMARY KEY,
        voter_id VARCHAR(50) UNIQUE NOT NULL,
        full_name VARCHAR(200) NOT NULL,
        dob DATE NOT NULL,
        gender ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
        address TEXT NOT NULL DEFAULT '',
        state VARCHAR(100) NOT NULL DEFAULT '',
        district VARCHAR(100) NOT NULL DEFAULT '',
        email VARCHAR(255) UNIQUE NOT NULL DEFAULT '',
        phone VARCHAR(15) NOT NULL DEFAULT '',
        password_hash VARCHAR(255) NOT NULL DEFAULT '',
        is_verified TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>✓ Voters table created</p>";
    
    // Create candidates table
    $mysqli->query("CREATE TABLE candidates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        party VARCHAR(150),
        party_symbol VARCHAR(255) DEFAULT NULL,
        manifesto TEXT,
        constituency VARCHAR(100) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>✓ Candidates table created</p>";
    
    // Create admins table
    $mysqli->query("CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>✓ Admins table created</p>";
    
    // Create votes table
    $mysqli->query("CREATE TABLE votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        voter_id INT NOT NULL,
        candidate_id INT NOT NULL,
        cast_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (voter_id) REFERENCES voters(id) ON DELETE CASCADE,
        FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
        UNIQUE KEY unique_vote_per_voter (voter_id)
    )");
    echo "<p>✓ Votes table created</p>";
    
    // Create election_status table
    $mysqli->query("CREATE TABLE election_status (
        id INT AUTO_INCREMENT PRIMARY KEY,
        is_open TINYINT(1) DEFAULT 0,
        results_published TINYINT(1) DEFAULT 0,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "<p>✓ Election status table created</p>";
    
    // Insert sample data
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $mysqli->query("INSERT INTO admins (username, password_hash) VALUES ('admin', '$password_hash')");
    echo "<p>✓ Admin user created (admin/admin123)</p>";
    
    // Insert sample voters
    $voter_password = password_hash('password123', PASSWORD_DEFAULT);
    $mysqli->query("INSERT INTO voters (voter_id, full_name, dob, gender, address, state, district, email, phone, password_hash, is_verified) VALUES
        ('VOTER001', 'Raj Sharma', '2000-01-01', 'Male', '123 Main Street, New Delhi', 'Delhi', 'New Delhi', 'raj@email.com', '9876543210', '$voter_password', 1),
        ('VOTER002', 'Sita Verma', '1999-05-10', 'Female', '456 Park Avenue, Mumbai', 'Maharashtra', 'Mumbai', 'sita@email.com', '9876543211', '$voter_password', 1),
        ('VOTER003', 'Aman Joshi', '1998-08-20', 'Male', '789 Garden Road, Bangalore', 'Karnataka', 'Bangalore', 'aman@email.com', '9876543212', '$voter_password', 1)");
    echo "<p>✓ Sample voters created</p>";
    
    // Insert sample candidates
    $mysqli->query("INSERT INTO candidates (name, party, manifesto, constituency) VALUES
        ('A. Kumar', 'BJP', 'Development and progress for all citizens', 'Delhi North'),
        ('B. Singh', 'Congress', 'Unity in diversity and social justice', 'Delhi South'),
        ('C. Patel', 'Independent', 'Transparency and accountability in governance', 'Delhi East')");
    echo "<p>✓ Sample candidates created</p>";
    
    // Insert election status
    $mysqli->query("INSERT INTO election_status (is_open, results_published) VALUES (0, 0)");
    echo "<p>✓ Election status initialized</p>";
    
    echo "<h3 style='color: green;'>🎉 Setup Complete!</h3>";
    echo "<p><strong>Test the system:</strong></p>";
    echo "<ul>";
    echo "<li><a href='frontend/index.html'>Homepage</a></li>";
    echo "<li><a href='frontend/voter_login.html'>Voter Login</a> (VOTER001 / 2000-01-01)</li>";
    echo "<li><a href='frontend/simple_admin.html'>Admin Panel</a> (admin / admin123)</li>";
    echo "<li><a href='frontend/simple_candidates.html'>View Candidates</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
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
