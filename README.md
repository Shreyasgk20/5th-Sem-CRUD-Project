# Online Voting System - ElectionSeva

A comprehensive online voting system built for India with HTML, CSS, JavaScript (frontend) and PHP, MySQL (backend).

## 🏛️ Government Portal Features

- **Indian Government Theme**: Saffron, White, Green color scheme with formal government styling
- **Secure Authentication**: Voter ID + DOB login system
- **Admin Controls**: Complete election management system
- **Real-time Results**: Live vote counting and results display
- **Mobile Responsive**: Works on all devices

## 📁 Project Structure

```
Online_Voting/
├── Frontend/
│   ├── index.html                 # Homepage
│   ├── voter_login.html          # Voter login page
│   ├── register_voter.html       # Voter registration
│   ├── ballot.html              # Voting interface
│   ├── how_to_vote.html         # Voting instructions
│   ├── candidates_list.html     # All candidates view
│   ├── results.html             # Election results
│   ├── admin.html               # Admin panel
│   ├── admin_register_candidate.html # Candidate registration
│   ├── css/style.css            # Main stylesheet
│   ├── js/main.js               # JavaScript functionality
│   └── uploads/symbols/         # Party symbol images
├── Backend/
│   ├── db.php                   # Database connection
│   ├── auth.php                 # Authentication system
│   ├── vote.php                 # Vote processing
│   ├── register_voter.php       # Voter registration
│   ├── admin_register_candidate.php # Candidate registration
│   ├── get_candidates.php       # Fetch candidates
│   ├── get_all_candidates.php   # Fetch all candidates
│   ├── get_results.php          # Fetch results
│   ├── get_stats.php            # Admin statistics
│   ├── get_election_status.php  # Election status
│   ├── get_all_voters.php       # All voters list
│   ├── admin_actions.php        # Admin actions
│   ├── admin_toggle_election.php # Election control
│   ├── admin_publish_results.php # Results management
│   ├── admin_delete_voter.php   # Delete voter
│   └── admin_delete_candidate.php # Delete candidate
├── sql/
│   ├── schema.sql               # Basic schema
│   └── updated_schema.sql       # Enhanced schema
└── online_voting_simplified.sql # Complete database dump
```

## 🚀 Quick Start

### 1. Database Setup

```sql
-- Import the database
mysql -u root -p < online_voting_simplified.sql

-- Or use the updated schema
mysql -u root -p < sql/updated_schema.sql
```

### 2. Configuration

Update database credentials in `Backend/db.php`:
```php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'online_voting_simplified';
```

### 3. Web Server Setup

Place the project in your web server directory (e.g., XAMPP htdocs):
```
C:\xampp\htdocs\Online_Voting\
```

### 4. Access the System

- **Homepage**: `http://localhost/Online_Voting/frontend/index.html`
- **Admin Panel**: `http://localhost/Online_Voting/frontend/admin.html`
- **Default Admin**: username: `admin`, password: `admin123`

## 🗳️ System Features

### Voter Features
- **Registration**: Complete voter registration with personal details
- **Login**: Secure Voter ID + DOB authentication
- **Voting**: Simple radio button voting interface
- **Instructions**: Step-by-step voting guide

### Admin Features
- **Election Control**: Open/close elections
- **Results Management**: Publish/unpublish results
- **Candidate Management**: Add/delete candidates with party symbols
- **Voter Management**: View/delete voters
- **Statistics**: Real-time voting statistics

### Security Features
- **One Vote Per Voter**: Database constraint prevents duplicate voting
- **Session Management**: Secure PHP sessions
- **Input Validation**: Server-side validation for all forms
- **File Upload Security**: Image type and size validation

## 🎨 UI/UX Features

### Government Portal Design
- **Indian Tricolor Theme**: Saffron, White, Green color scheme
- **Formal Typography**: Government-style fonts and layouts
- **Compact Design**: Dense, information-rich interface
- **Accessibility**: Clear navigation and readable text

### Responsive Design
- **Mobile First**: Works on all screen sizes
- **Touch Friendly**: Large buttons and touch targets
- **Fast Loading**: Optimized CSS and JavaScript

## 📊 Database Schema

### Tables
- **voters**: Voter information and authentication
- **candidates**: Candidate details and party information
- **votes**: Vote records with foreign key constraints
- **admins**: Admin user accounts
- **election_status**: Election state management

### Key Features
- **Foreign Key Constraints**: Data integrity
- **Unique Constraints**: Prevent duplicate votes
- **Indexes**: Optimized query performance
- **Cascade Deletes**: Maintain data consistency

## 🔧 Technical Details

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with CSS Grid and Flexbox
- **JavaScript ES6**: Modern JavaScript features
- **Responsive Design**: Mobile-first approach

### Backend
- **PHP 7.4+**: Server-side processing
- **MySQL 5.7+**: Database management
- **PDO**: Secure database interactions
- **Session Management**: User authentication

### Security
- **Password Hashing**: bcrypt password encryption
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization
- **File Upload Security**: Type and size validation

## 🚦 Election Workflow

1. **Setup Phase**
   - Admin registers candidates
   - Admin registers voters (or voters self-register)
   - Admin opens election

2. **Voting Phase**
   - Voters login with Voter ID + DOB
   - Voters select candidate and cast vote
   - System prevents duplicate voting

3. **Results Phase**
   - Admin closes election
   - Admin publishes results
   - Public can view results

## 📱 Mobile Support

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔒 Security Considerations

- **HTTPS Recommended**: For production deployment
- **Regular Backups**: Database backup strategy
- **Access Logs**: Monitor system usage
- **Input Validation**: All user inputs validated
- **Session Timeout**: Automatic logout for security

## 🛠️ Customization

### Adding New Features
1. Create frontend HTML page
2. Add corresponding PHP backend
3. Update database schema if needed
4. Add JavaScript functionality
5. Style with CSS

### Theming
- Modify CSS variables in `style.css`
- Update color scheme in `:root` section
- Customize component styles

## 📞 Support

For technical support or questions:
- **Helpline**: 1800-123-4321
- **Email**: support@electionseva.gov.in

## 📄 License

This project is developed for educational purposes and government use.

---

**ElectionSeva** - निर्वाचन सेवा  
*Secure. Transparent. Accessible.*