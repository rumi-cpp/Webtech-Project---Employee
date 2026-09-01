<?php
// ========================================================
// controllers/employee/teams.php - Employee Team Management
// ========================================================
require_once __DIR__ . '/../auth.php';
requireRole('employee');
require_once __DIR__ . '/../../models/team.php';
require_once __DIR__ . '/../../models/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'assign_player') {
        $team_id    = intval($_POST['team_id']);
        $student_id = trim($_POST['student_id']);

        $athlete = findUserForLogin($student_id);
        if (!$athlete) {
            header("Location: ../../views/employees/teams.php?view=" . $team_id . "&error=" . urlencode("Student ID not found."));
            exit();
        }

        addTeamMember($team_id, $athlete['id']);
        header("Location: ../../views/employees/teams.php?view=" . $team_id . "&success=" . urlencode("Student assigned to team."));
        exit();
    } elseif ($action === 'remove_player') {
        $team_id = intval($_POST['team_id']);
        $user_id = intval($_POST['user_id']);

        removeTeamMember($team_id, $user_id);
        header("Location: ../../views/employees/teams.php?view=" . $team_id . "&success=" . urlencode("Player removed from team."));
        exit();
    }
}

// Fallback
header("Location: ../../views/employees/teams.php");
exit();
?>
