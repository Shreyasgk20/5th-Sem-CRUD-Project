<?php
// Simple admin actions - direct CRUD operations
require_once 'db.php';
session_start();

$action = $_GET['action'] ?? '';
$mysqli = get_db();

// Simple admin check
if (!isset($_SESSION['admin_id']) && $action !== 'login') {
    header('Location: ../frontend/admin.html');
    exit;
}

switch($action) {
    case 'add_candidate':
        $name = $_POST['name'] ?? '';
        $party = $_POST['party'] ?? '';
        $manifesto = $_POST['manifesto'] ?? '';
        
        if ($name) {
            $stmt = $mysqli->prepare("INSERT INTO candidates (name, party, manifesto) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $name, $party, $manifesto);
            $stmt->execute();
        }
        header('Location: ../frontend/admin.html?success=candidate_added');
        break;
        
    case 'add_voter':
        $voter_id = $_POST['voter_id'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $is_verified = isset($_POST['is_verified']) ? 1 : 0;
        
        if ($voter_id && $full_name && $dob) {
            $password_hash = password_hash('password123', PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO voters (voter_id, full_name, dob, gender, address, state, district, email, phone, password_hash, is_verified) VALUES (?, ?, ?, 'Male', 'Address', 'State', 'District', 'email@example.com', '9876543210', ?, ?)");
            $stmt->bind_param('ssssi', $voter_id, $full_name, $dob, $password_hash, $is_verified);
            $stmt->execute();
        }
        header('Location: ../frontend/admin.html?success=voter_added');
        break;
        
    case 'delete_candidate':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $mysqli->prepare("DELETE FROM candidates WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
        }
        header('Location: ../frontend/candidates_list.html?success=candidate_deleted');
        break;
        
    case 'delete_voter':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $mysqli->prepare("DELETE FROM voters WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
        }
        header('Location: ../frontend/admin.html?success=voter_deleted');
        break;
        
    case 'open_election':
        $mysqli->query("UPDATE election_status SET is_open = 1 WHERE id = 1");
        header('Location: ../frontend/admin.html?success=election_opened');
        break;
        
    case 'close_election':
        $mysqli->query("UPDATE election_status SET is_open = 0 WHERE id = 1");
        header('Location: ../frontend/admin.html?success=election_closed');
        break;
        
    case 'publish_results':
        $mysqli->query("UPDATE election_status SET results_published = 1 WHERE id = 1");
        header('Location: ../frontend/admin.html?success=results_published');
        break;
        
    default:
        header('Location: ../frontend/admin.html');
}
?>
