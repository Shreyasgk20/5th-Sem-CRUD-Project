<?php
// backend/get_results.php
require_once 'db.php';
header('Content-Type: application/json');

$mysqli = get_db();

try {
    // Get election status
    $status_res = $mysqli->query("SELECT is_open, results_published FROM election_status ORDER BY id DESC LIMIT 1");
    $election_status = $status_res->fetch_assoc();
    
    // Check if results are published
    if (!$election_status || !$election_status['results_published']) {
        echo json_encode(['success' => false, 'message' => 'Results not published yet']);
        exit;
    }
    
    // Get vote counts for each candidate
    $results_query = "
        SELECT 
            c.id,
            c.name,
            c.party,
            c.party_symbol,
            c.constituency,
            COALESCE(COUNT(v.id), 0) as vote_count
        FROM candidates c
        LEFT JOIN votes v ON c.id = v.candidate_id
        GROUP BY c.id, c.name, c.party, c.party_symbol, c.constituency
        ORDER BY vote_count DESC
    ";
    
    $res = $mysqli->query($results_query);
    $results = [];
    
    while ($row = $res->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Get statistics
    $stats_query = "
        SELECT 
            (SELECT COUNT(*) FROM voters) as total_voters,
            (SELECT COUNT(*) FROM votes) as total_votes
    ";
    
    $stats_res = $mysqli->query($stats_query);
    $stats = $stats_res->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'results' => $results,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
