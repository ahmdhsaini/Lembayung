<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";
$msg = "";
$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

// Handle updating booking with specific table assignments or statuses
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = trim($_POST['booking_id']);
    $status = trim($_POST['status']);
    $table_id = isset($_POST['table_id']) && $_POST['table_id'] !== '' ? intval($_POST['table_id']) : null;
    
    // Gap 2 & 3: Save explicit table allocation and status safely via prepared statements
    $sql_update = "UPDATE bookings SET status = ?, table_id = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "sii", $status, $table_id, $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: staff_dashboard.php");
            exit();
        } else {
            $msg = '<div class="alert alert-danger">Error processing update schema matrix variables.</div>';
        }
        mysqli_stmt_close($stmt);
    }
}

// Pull individual record row to display information details
$sql = "SELECT b.*, u.full_name FROM bookings b JOIN users u ON b.customer_id = u.id WHERE b.id = ?";
$booking = null;
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

if (!$booking) {
    die("Record target context index error.");
}
?>

<?php include_once "includes/header.php"; ?>

<div class="panel panel-default" style="max-width: 600px; margin: 30px auto;">
    <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Process Action Assignment Routing Terminal</h3></div>
    <div class="panel-body">
        <?php echo $msg; ?>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($booking['full_name']); ?></p>
        <p><strong>Total Guests Count:</strong> <?php echo $booking['num_guests']; ?> Pax</p>
        <p><strong>Reservation Date Selected:</strong> <?php echo $booking['booking_date']; ?></p>
        
        <form action="update_booking.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
            
            <div class="form-group">
                <label>Set Status Value Matrix:</label>
                <select name="status" class="form-control" required>
                    <option value="Pending" <?php if($booking['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Approved" <?php if($booking['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                    <option value="Cancelled" <?php if($booking['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Assign Physical Restaurant Table (ERD Link):</label>
                <select name="table_id" class="form-control">
                    <option value="">-- No Physical Table Assigned --</option>
                    <?php
                    $tables_res = mysqli_query($link, "SELECT table_id, table_number, capacity FROM restaurant_tables");
                    while($table = mysqli_fetch_assoc($tables_res)) {
                        $selected = ($booking['table_id'] == $table['table_id']) ? 'selected' : '';
                        echo '<option value="'.$table['table_id'].'" '.$selected.'>Table Code: '.$table['table_number'].' (Max Capacity: '.$table['capacity'].' Pax)</option>';
                    }
                    ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-success" style="font-weight: bold;">Apply Processing Changes</button>
            <a href="staff_dashboard.php" class="btn btn-default">Back to Board Layout</a>
        </form>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>