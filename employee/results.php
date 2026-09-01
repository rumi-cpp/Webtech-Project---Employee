<?php
// ========================================================
// controllers/employee/results.php - Enter & Save Match Results
// ========================================================
require_once __DIR__ . '/../auth.php';
// Admin or Employee can enter results
if ($_SESSION['role'] !== 'employee' && $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/Login.php");
    exit();
}

require_once __DIR__ . '/../../models/result.php';
require_once __DIR__ . '/../../models/match.php';
require_once __DIR__ . '/../../models/notification.php';
require_once __DIR__ . '/../../models/team.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id    = intval($_POST['match_id']);
    $team1_score = intval($_POST['team1_score']);
    $team2_score = intval($_POST['team2_score']);
    $winner_id   = !empty($_POST['winner_id']) ? intval($_POST['winner_id']) : null;
    $recorded_by = $_SESSION['user_id'];

    if (empty($match_id)) {
        header("Location: ../../views/employees/results.php?error=" . urlencode("Match selection is required."));
        exit();
    }

    // Save result
    saveMatchResult($match_id, $team1_score, $team2_score, $winner_id, $recorded_by);
    
    // Set match status to completed
    updateMatchStatus($match_id, 'completed');

    // Notify team members
    $match = getMatchById($match_id);
    if ($match) {
        $t1 = getTeamWithMembers($match['team1_id']);
        $t2 = getTeamWithMembers($match['team2_id']);

        $msg = "Match Result Published: " . $match['team1_name'] . " ($team1_score) vs " . $match['team2_name'] . " ($team2_score).";
        
        if ($t1 && !empty($t1['members'])) {
            foreach ($t1['members'] as $member) {
                createNotification($member['id'], $msg);
            }
        }
        if ($t2 && !empty($t2['members'])) {
            foreach ($t2['members'] as $member) {
                createNotification($member['id'], $msg);
            }
        }
    }

    $redirect_url = ($_SESSION['role'] === 'admin') ? '../../views/admins/results.php' : '../../views/employees/results.php';
    header("Location: " . $redirect_url . "?success=" . urlencode("Match result and scores successfully published!"));
    exit();
}

// Fallback
header("Location: ../../views/employees/results.php");
exit();
?>
