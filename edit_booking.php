<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

$msg = "";
$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$customer_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = trim($_POST['booking_id']);
    $slot_id = trim($_POST['slot_id']);
    $booking_date = trim($_POST['booking_date']);
    $num_guests = trim($_POST['num_guests']);
    $special_notes = trim($_POST['special_notes']);
    
    // Gap 2: Prepared statement for updates
    $sql_update = "UPDATE bookings SET slot_id = ?, booking_date = ?, num_guests = ?, special_notes = ? WHERE id = ? AND customer_id = ? AND status = 'Pending'";
    if ($stmt = mysqli_prepare($link, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "isisii", $slot_id, $booking_date, $num_guests, $special_notes, $booking_id, $customer_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: customer_dashboard.php");
            exit();
        } else {
            $msg = '<div class="alert alert-danger">Error updating booking record elements.</div>';
        }
        mysqli_stmt_close($stmt);
    }
}

// Gap 2: Fetch details securely
$sql = "SELECT * FROM bookings WHERE id = ? AND customer_id = ? AND status = 'Pending'";
$booking = null;
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $customer_id);
    mysqli_stmt_execute($stmt);
    $booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

if (!$booking) {
    header("Location: customer_dashboard.php");
    exit();
}
?>

<?php include_once "includes/cust_header.php"; ?>

<div class="panel panel-default" style="max-width: 600px; margin: 30px auto;">
    <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Modify Pending Reservation Elements</h3></div>
    <div class="panel-body">
        <?php echo $msg; ?>
        <form name="bookingForm" action="edit_booking.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
            
            <div class="form-group">
                <label>Operational Window Time:</label>
                <select name="slot_id" class="form-control" required>
                    <?php
                    $slots = mysqli_query($link, "SELECT * FROM timeslots WHERE status='Active' OR slot_id = " . intval($booking['slot_id']));
                    while($s = mysqli_fetch_assoc($slots)) {
                        $sel = ($booking['slot_id'] == $s['slot_id']) ? 'selected' : '';
                        echo '<option value="'.$s['slot_id'].'" '.$sel.'>'.$s['time_range'].'</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date Frame:</label>
                <input type="date" name="booking_date" class="form-control" required value="<?php echo $booking['booking_date']; ?>">
            </div>

            <div class="form-group">
                <label>Number of Guests:</label>
                <input type="number" name="num_guests" class="form-control" required min="1" max="20" value="<?php echo $booking['num_guests']; ?>" oninput="document.bookingForm.tables_needed.value = Math.ceil(this.value / 4);">
            </div>

            <div class="form-group" style="background-color: #f9f9f9; padding: 12px; border-radius: 4px; border: 1px solid #eee;">
                <label style="color: #666; font-size: 13px;">Automated Spatial Estimate:</label>
                <div class="input-group" style="max-width: 200px;">
                    <input type="text" name="tables_needed" class="form-control" readonly value="<?php echo ceil($booking['num_guests'] / 4); ?>" style="font-weight: bold; background:#fff;">
                    <span class="input-group-addon">Table(s)</span>
                </div>
            </div>

            <div class="form-group">
                <label>Special Instructions / Requirements:</label>
                <textarea name="special_notes" class="form-control" rows="3"><?php echo htmlspecialchars($booking['special_notes']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="font-weight: bold;">Save Changes</button>
            <a href="customer_dashboard.php" class="btn btn-default">Discard Modifications</a>
        </form>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>