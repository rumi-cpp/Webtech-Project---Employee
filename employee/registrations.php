<?php
// ========================================================
// controllers/employee/registrations.php - Verify Registrations
// ========================================================
require_once __DIR__ . '/../auth.php';
requireRole('employee');
require_once __DIR__ . '/../../models/registration.php';
require_once __DIR__ . '/../../models/notification.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action          = $_POST['action']; // 'approve' or 'reject'
    $registration_id = intval($_POST['registration_id']);

    $reg = getRegistrationById($registration_id);
    if (!$reg) {
        header("Location: ../../views/employees/registrations.php?error=" . urlencode("Registration record not found."));
        exit();
    }

    if ($action === 'approve') {
        updateRegistrationStatus($registration_id, 'approved');
        createNotification($reg['user_id'], "Congratulations! Your registration for '" . $reg['tournament_name'] . "' has been APPROVED by the Sports Department.");
        header("Location: ../../views/employees/registrations.php?success=" . urlencode("Registration for " . htmlspecialchars($reg['user_name']) . " approved successfully!"));
        exit();
    } elseif ($action === 'reject') {
        updateRegistrationStatus($registration_id, 'rejected');
        createNotification($reg['user_id'], "Notice: Your registration for '" . $reg['tournament_name'] . "' was not approved. Please contact the sports desk.");
        header("Location: ../../views/employees/registrations.php?success=" . urlencode("Registration rejected."));
        exit();
    }
}

// Fallback
header("Location: ../../views/employees/registrations.php");
exit();
?>
