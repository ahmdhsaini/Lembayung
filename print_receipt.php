<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Gap 2: Secure Prepared Statement formulation routing logic matrix profiles
$sql = "SELECT b.*, u.full_name, u.phone_number, t.time_range 
        FROM bookings b
        JOIN users u ON b.customer_id = u.id
        JOIN timeslots t ON b.slot_id = t.slot_id
        WHERE b.id = ?";

if ($role !== 'staff') {
    $sql .= " AND b.customer_id = ?";
}

if ($stmt = mysqli_prepare($link, $sql)) {
    if ($role !== 'staff') {
        mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
    }
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

if (!$data) {
    die("Error: Reservation record not found or unauthorized access.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Receipt - #<?php echo $data['id']; ?></title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
<div class="container" style="max-width: 650px; margin: 40px auto; border: 1px dashed #ccc; padding: 30px; background:#fff;">
    <div class="text-center">
        <h2 style="margin: 0; font-weight: bold; color: #2c3e50;">LEMBAYUNG RESTAURANT</h2>
        <p class="text-muted" style="margin-top: 5px; font-size: 12px;">Unified Processing Dining Order Slip Terminal</p>
    </div>
    <hr style="border-top: 1px dashed #eee;">
    <table class="table table-bordered">
        <tbody>
            <tr><td><strong>Booking Reference</strong></td><td>#<?php echo $data['id']; ?></td></tr>
            <tr><td><strong>Customer Name</strong></td><td><?php echo htmlspecialchars($data['full_name']); ?></td></tr>
            <tr><td><strong>Contact Line</strong></td><td><?php echo htmlspecialchars($data['phone_number']); ?></td></tr>
            <tr><td><strong>Reservation Date</strong></td><td><?php echo $data['booking_date']; ?></td></tr>
            <tr><td><strong>Time Allocation</strong></td><td><?php echo $data['time_range']; ?></td></tr>
            <tr><td><strong>Guests Seats</strong></td><td><?php echo $data['num_guests']; ?> Pax</td></tr>
            <tr><td><strong>Assigned Table ID</strong></td><td><?php echo ($data['table_id'] ? 'Table ' . $data['table_id'] : 'Pending Physical Setup'); ?></td></tr>
        </tbody>
    </table>
    <div class="text-center no-print" style="margin-top: 30px;">
        <button onclick="window.print();" class="btn btn-primary font-weight-bold">Trigger Print Dialog</button>
    </div>
</div>
</body>
</html>