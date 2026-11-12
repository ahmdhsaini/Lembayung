<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'staff') { 
    header("Location: login.php"); 
    exit(); 
}
require_once "includes/config.php";

// Gap 2: Safe execution reporting
$sql = "SELECT b.id, u.full_name, u.phone_number, b.booking_date, t.time_range, b.num_guests, b.status 
        FROM bookings b JOIN users u ON b.customer_id = u.id JOIN timeslots t ON b.slot_id = t.slot_id ORDER BY b.booking_date DESC";
$result = mysqli_query($link, $sql);

if ($result) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Lembayung_Bookings_Report_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Booking ID', 'Customer Name', 'Contact Phone', 'Reservation Date', 'Time Window', 'Guests Count', 'Status'));
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, array("#" . $row['id'], $row['full_name'], $row['phone_number'], $row['booking_date'], $row['time_range'], $row['num_guests'], $row['status']));
    }
    fclose($output); 
    exit();
}
?>