<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

if (isset($_GET['id'])) {
    $booking_id = trim($_GET['id']);
    $customer_id = $_SESSION['user_id'];
    
    // Gap 2: Prepared statement structural deletion
    $sql_delete = "DELETE FROM bookings WHERE id = ? AND customer_id = ? AND status = 'Pending'";
    if ($stmt = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt, "ii", $booking_id, $customer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: customer_dashboard.php");
exit();
?>