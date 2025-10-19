-- Migration script to add new columns to existing voters table
-- Run this script to update the database schema

USE online_voting_simplified;

-- Add new columns to voters table
ALTER TABLE voters 
ADD COLUMN gender ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male' AFTER dob,
ADD COLUMN address TEXT NOT NULL DEFAULT '' AFTER gender,
ADD COLUMN state VARCHAR(100) NOT NULL DEFAULT '' AFTER address,
ADD COLUMN district VARCHAR(100) NOT NULL DEFAULT '' AFTER state,
ADD COLUMN email VARCHAR(255) UNIQUE NOT NULL DEFAULT '' AFTER district,
ADD COLUMN phone VARCHAR(15) NOT NULL DEFAULT '' AFTER email,
ADD COLUMN password_hash VARCHAR(255) NOT NULL DEFAULT '' AFTER phone;

-- Create election_status table if it doesn't exist
CREATE TABLE IF NOT EXISTS election_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  is_open TINYINT(1) DEFAULT 0,
  results_published TINYINT(1) DEFAULT 0,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default election status if not exists
INSERT IGNORE INTO election_status (is_open, results_published) VALUES (0, 0);

-- Add party_symbol column to candidates table if it doesn't exist
ALTER TABLE candidates 
ADD COLUMN party_symbol VARCHAR(255) DEFAULT NULL AFTER party,
ADD COLUMN constituency VARCHAR(100) DEFAULT NULL AFTER party_symbol;

-- Update existing voters with default values (you may want to update these manually)
UPDATE voters SET 
  gender = 'Male',
  address = 'Address not provided',
  state = 'Delhi',
  district = 'New Delhi',
  email = CONCAT('voter', id, '@example.com'),
  phone = CONCAT('9876543', LPAD(id, 3, '0')),
  password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = '' OR email IS NULL;

-- Update existing candidates with default values
UPDATE candidates SET 
  constituency = 'General Constituency'
WHERE constituency IS NULL OR constituency = '';

-- Show the updated table structure
DESCRIBE voters;
DESCRIBE candidates;
DESCRIBE election_status;
