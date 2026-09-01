<?php
// ========================================================
// controllers/employee/payments.php - Verify Payments
// ========================================================
require_once __DIR__ . '/../auth.php';
requireRole('employee');
require_once __DIR__ . '/../../models/payment.php';
require_once __DIR__ . '/../../models/notification.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action     = $_POST['action']; // 'approve' or 'reject'
    $payment_id = intval($_POST['payment_id']);

    $payment = getPaymentById($payment_id);
    if (!$payment) {
        header("Location: ../../views/employees/payments.php?error=" . urlencode("Payment record not found."));
        exit();
    }

    if ($action === 'approve') {
        updatePaymentStatus($payment_id, 'paid');
        createNotification($payment['user_id'], "Payment of ৳" . $payment['amount'] . " for '" . $payment['tournament_name'] . "' has been verified & approved! ✅");
        header("Location: ../../views/employees/payments.php?success=" . urlencode("Payment approved!"));
        exit();
    } elseif ($action === 'reject') {
        updatePaymentStatus($payment_id, 'failed');
        createNotification($payment['user_id'], "Alert: Your payment verification for '" . $payment['tournament_name'] . "' failed. Please check transaction ID or contact desk.");
        header("Location: ../../views/employees/payments.php?success=" . urlencode("Payment status set to failed."));
        exit();
    }
}

// Fallback
header("Location: ../../views/employees/payments.php");
exit();
?>
