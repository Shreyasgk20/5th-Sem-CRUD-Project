-- Complete Database Setup for Online Voting System
-- Run this script to set up the entire database with all required tables and data

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS online_voting_simplified;
USE online_voting_simplified;

-- Drop existing tables if they exist (to start fresh)
DROP TABLE IF EXISTS votes;
DROP TABLE IF EXISTS candidates;
DROP TABLE IF EXISTS voters;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS election_status;

-- Create voters table with all required fields
CREATE TABLE voters (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voter_id VARCHAR(50) UNIQUE NOT NULL,
  full_name VARCHAR(200) NOT NULL,
  dob DATE NOT NULL,
  gender ENUM('Male', 'Female', 'Other') NOT NULL,
  address TEXT NOT NULL,
  state VARCHAR(100) NOT NULL,
  district VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  phone VARCHAR(15) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create candidates table with all required fields
CREATE TABLE candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  party VARCHAR(150),
  party_symbol VARCHAR(255) DEFAULT NULL,
  manifesto TEXT,
  constituency VARCHAR(100) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create admins table
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create votes table
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voter_id INT NOT NULL,
  candidate_id INT NOT NULL,
  cast_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (voter_id) REFERENCES voters(id) ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
  UNIQUE KEY unique_vote_per_voter (voter_id)
);

-- Create election_status table
CREATE TABLE election_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  is_open TINYINT(1) DEFAULT 0,
  results_published TINYINT(1) DEFAULT 0,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample voters with complete information
INSERT INTO voters (voter_id, full_name, dob, gender, address, state, district, email, phone, password_hash, is_verified) VALUES
('VOTER001', 'Raj Sharma', '2000-01-01', 'Male', '123 Main Street, New Delhi', 'Delhi', 'New Delhi', 'raj.sharma@email.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('VOTER002', 'Sita Verma', '1999-05-10', 'Female', '456 Park Avenue, Mumbai', 'Maharashtra', 'Mumbai', 'sita.verma@email.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('VOTER003', 'Aman Joshi', '1998-08-20', 'Male', '789 Garden Road, Bangalore', 'Karnataka', 'Bangalore', 'aman.joshi@email.com', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('VOTER004', 'Priya Patel', '2001-03-15', 'Female', '321 Tech Park, Hyderabad', 'Telangana', 'Hyderabad', 'priya.patel@email.com', '9876543213', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('VOTER005', 'Vikram Singh', '1997-11-22', 'Male', '654 Business District, Chennai', 'Tamil Nadu', 'Chennai', 'vikram.singh@email.com', '9876543214', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample candidates with complete information
INSERT INTO candidates (name, party, manifesto, constituency) VALUES
('A. Kumar', 'Bharatiya Janata Party', 'Development and progress for all citizens. Focus on infrastructure, education, and healthcare reforms.', 'Delhi North'),
('B. Singh', 'Indian National Congress', 'Unity in diversity and social justice. Empowering youth and women through education and employment.', 'Delhi South'),
('C. Patel', 'Independent', 'Transparency and accountability in governance. Clean politics and citizen-centric policies.', 'Delhi East'),
('D. Sharma', 'Aam Aadmi Party', 'Revolutionary changes in education and healthcare. Free and quality services for all citizens.', 'Delhi West'),
('E. Gupta', 'Samajwadi Party', 'Social equality and justice. Focus on farmers welfare and rural development.', 'Delhi Central');

-- Insert default election status
INSERT INTO election_status (is_open, results_published) VALUES (0, 0);

-- Insert some sample votes for testing
INSERT INTO votes (voter_id, candidate_id) VALUES
(1, 1),
(2, 2),
(3, 1),
(4, 3),
(5, 2);

-- Show table structures
DESCRIBE voters;
DESCRIBE candidates;
DESCRIBE admins;
DESCRIBE votes;
DESCRIBE election_status;

-- Show sample data
SELECT 'Voters Table:' as Table_Name;
SELECT * FROM voters LIMIT 3;

SELECT 'Candidates Table:' as Table_Name;
SELECT * FROM candidates LIMIT 3;

SELECT 'Election Status:' as Table_Name;
SELECT * FROM election_status;

SELECT 'Vote Counts:' as Table_Name;
SELECT c.name, c.party, COUNT(v.id) as vote_count 
FROM candidates c 
LEFT JOIN votes v ON c.id = v.candidate_id 
GROUP BY c.id, c.name, c.party 
ORDER BY vote_count DESC;
