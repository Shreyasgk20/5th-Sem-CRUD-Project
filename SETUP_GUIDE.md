# 🚀 Online Voting System - Complete Setup Guide

## 📋 Prerequisites

- **XAMPP** installed and running
- **MySQL** service running
- **Apache** service running
- **PHP 7.4+** (included with XAMPP)

## 🔧 Step-by-Step Setup

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service
4. Ensure both services are running (green status)

### Step 2: Database Setup

#### Option A: Automatic Setup (Recommended)
1. Open your web browser
2. Navigate to: `http://localhost/Online_Voting/setup_database.php`
3. The script will automatically create all tables and sample data
4. Look for "🎉 Database setup completed successfully!" message

#### Option B: Manual Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `online_voting_simplified`
3. Import the SQL file: `sql/complete_setup.sql`

### Step 3: Verify Installation
1. Navigate to: `http://localhost/Online_Voting/test_system.php`
2. Check that all tests show green checkmarks
3. Look for "🎉 System is ready for use!" message

### Step 4: Test the System

#### Test Voter Login
1. Go to: `http://localhost/Online_Voting/frontend/voter_login.html`
2. Use credentials:
   - **Voter ID**: `VOTER001`
   - **Date of Birth**: `2000-01-01`
3. Should successfully login and show ballot

#### Test Admin Panel
1. Go to: `http://localhost/Online_Voting/frontend/admin.html`
2. Use credentials:
   - **Username**: `admin`
   - **Password**: `admin123`
3. Should show admin control panel

#### Test Voter Registration
1. Go to: `http://localhost/Online_Voting/frontend/register_voter.html`
2. Fill out the registration form
3. Should successfully register new voter

## 🗄️ Database Structure

### Tables Created:
- **voters** - Voter information and authentication
- **candidates** - Candidate details and party information
- **votes** - Vote records with foreign key constraints
- **admins** - Admin user accounts
- **election_status** - Election state management

### Sample Data Included:
- **5 Sample Voters** (VOTER001 to VOTER005)
- **5 Sample Candidates** with different parties
- **1 Admin Account** (admin/admin123)
- **Sample Votes** for testing
- **Election Status** (initially closed)

## 🔐 Default Credentials

### Admin Access:
- **Username**: `admin`
- **Password**: `admin123`

### Sample Voters:
| Voter ID | Name | DOB | Email | Status |
|----------|------|-----|-------|--------|
| VOTER001 | Raj Sharma | 2000-01-01 | raj.sharma@email.com | Verified |
| VOTER002 | Sita Verma | 1999-05-10 | sita.verma@email.com | Verified |
| VOTER003 | Aman Joshi | 1998-08-20 | aman.joshi@email.com | Verified |
| VOTER004 | Priya Patel | 2001-03-15 | priya.patel@email.com | Verified |
| VOTER005 | Vikram Singh | 1997-11-22 | vikram.singh@email.com | Verified |

## 🎯 System Features

### Voter Features:
- ✅ Voter Registration with complete details
- ✅ Secure Login (Voter ID + DOB)
- ✅ Voting Interface with candidate selection
- ✅ Step-by-step voting instructions
- ✅ View all candidates before voting

### Admin Features:
- ✅ Election Control (Open/Close)
- ✅ Results Management (Publish/Unpublish)
- ✅ Candidate Management (Add/Delete)
- ✅ Voter Management (View/Delete)
- ✅ Real-time Statistics
- ✅ Party Symbol Upload

### Security Features:
- ✅ Password Hashing (bcrypt)
- ✅ SQL Injection Prevention
- ✅ Input Validation
- ✅ Session Management
- ✅ One Vote Per Voter
- ✅ File Upload Security

## 🚨 Troubleshooting

### Common Issues:

#### 1. "Database connection failed"
- **Solution**: Ensure MySQL service is running in XAMPP
- **Check**: Go to XAMPP Control Panel → MySQL → Start

#### 2. "Table doesn't exist" errors
- **Solution**: Run the setup script again
- **URL**: `http://localhost/Online_Voting/setup_database.php`

#### 3. "Permission denied" for file uploads
- **Solution**: Check folder permissions
- **Command**: `chmod 755 frontend/uploads/symbols/`

#### 4. Phone number validation errors
- **Solution**: Use 10-digit format (9876543210) or with country code (+919876543210)

#### 5. "Invalid credentials" for admin login
- **Solution**: Use exact credentials: username `admin`, password `admin123`

### File Structure Check:
```
Online_Voting/
├── frontend/           ✅ HTML/CSS/JS files
├── backend/            ✅ PHP backend files
├── sql/                ✅ Database scripts
├── setup_database.php  ✅ Auto setup script
├── test_system.php     ✅ System test script
└── README.md           ✅ Documentation
```

## 📞 Support

If you encounter any issues:
1. Check the test script: `http://localhost/Online_Voting/test_system.php`
2. Verify XAMPP services are running
3. Check file permissions
4. Review error logs in XAMPP

## 🎉 Success!

Once all tests pass, your Online Voting System is ready for use! You can:
- Register new voters
- Add candidates with party symbols
- Conduct elections
- View real-time results
- Manage the entire voting process

**Happy Voting! 🗳️**
