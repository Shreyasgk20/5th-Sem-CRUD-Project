@echo off
echo ========================================
echo Online Voting System - Quick Setup
echo ========================================
echo.
echo This script will help you set up the Online Voting System
echo.
echo Step 1: Make sure XAMPP is running
echo - Start Apache service
echo - Start MySQL service
echo.
pause
echo.
echo Step 2: Opening setup page in your browser...
echo.
start http://localhost/Online_Voting/setup_database.php
echo.
echo Step 3: After the database setup is complete,
echo        run the verification script:
echo.
start http://localhost/Online_Voting/verify_installation.php
echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo If everything shows green checkmarks,
echo your Online Voting System is ready!
echo.
pause
