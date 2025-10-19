-- Updated schema for Online Voting System
-- This file contains the enhanced database structure with additional fields

CREATE DATABASE IF NOT EXISTS online_voting_simplified;
USE online_voting_simplified;

-- Enhanced voters table with additional registration fields
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

-- Enhanced candidates table with party symbol support
CREATE TABLE candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  party VARCHAR(150),
  party_symbol VARCHAR(255) DEFAULT NULL,
  manifesto TEXT,
  constituency VARCHAR(100) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Admins table (unchanged)
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Votes table (unchanged)
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voter_id INT NOT NULL,
  candidate_id INT NOT NULL,
  cast_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (voter_id) REFERENCES voters(id) ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
  UNIQUE KEY unique_vote_per_voter (voter_id)
);

-- Election status table for managing election state
CREATE TABLE election_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  is_open TINYINT(1) DEFAULT 0,
  results_published TINYINT(1) DEFAULT 0,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default election status
INSERT INTO election_status (is_open, results_published) VALUES (0, 0);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password_hash) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample data
INSERT INTO voters (voter_id, full_name, dob, gender, address, state, district, email, phone, password_hash, is_verified) VALUES
('VOTER001', 'Raj Sharma', '2000-01-01', 'Male', '123 Main Street, New Delhi', 'Delhi', 'New Delhi', 'raj.sharma@email.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('VOTER002', 'Sita Verma', '1999-05-10', 'Female', '456 Park Avenue, Mumbai', 'Maharashtra', 'Mumbai', 'sita.verma@email.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('VOTER003', 'Aman Joshi', '1998-08-20', 'Male', '789 Garden Road, Bangalore', 'Karnataka', 'Bangalore', 'aman.joshi@email.com', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

INSERT INTO candidates (name, party, manifesto, constituency) VALUES
('A. Kumar', 'Bharatiya Janata Party', 'Development and progress for all citizens', 'Delhi North'),
('B. Singh', 'Indian National Congress', 'Unity in diversity and social justice', 'Delhi South'),
('C. Patel', 'Independent', 'Transparency and accountability in governance', 'Delhi East');
