<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['user_id'];
    $slot_id = trim($_POST['slot_id']);
    $booking_date = trim($_POST['booking_date']);
    $num_guests = trim($_POST['num_guests']);
    $special_notes = trim($_POST['special_notes']);
    
    // Calculate how many tables this new request requires (4 chairs per table element)
    $requested_tables = ceil($num_guests / 4);
    
    // --- TIME SLOT OVERBOOKING CHECK ---
    $max_restaurant_tables = 10; // Max allowed tables per time slot setup
    
    $check_sql = "SELECT SUM(CEIL(num_guests / 4)) AS tables_taken 
                  FROM bookings 
                  WHERE booking_date = ? 
                    AND slot_id = ? 
                    AND status != 'Cancelled'";
                    
    if ($check_stmt = mysqli_prepare($link, $check_sql)) {
        mysqli_stmt_bind_param($check_stmt, "si", $booking_date, $slot_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_row = mysqli_fetch_assoc($check_result);
        $current_tables_taken = $check_row['tables_taken'] ?? 0;
        mysqli_stmt_close($check_stmt);
    } else {
        $current_tables_taken = 0;
    }
    
    if (($current_tables_taken + $requested_tables) > $max_restaurant_tables) {
        $msg = '<div class="alert alert-danger" style="font-weight:bold;">Reservation Failed: This specific dining slot is fully committed. Please opt for a separate calendar date or select another time window context.</div>';
    } else {
        // --- SECURE PREPARED STATEMENT INSERT ---
        // Column Order: customer_id (int), slot_id (int), booking_date (string), num_guests (int), special_notes (string)
        $sql = "INSERT INTO bookings (customer_id, slot_id, booking_date, num_guests, special_notes, status) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
                
        if ($stmt = mysqli_prepare($link, $sql)) {
            // FIXED BINDING ORDER TYPE DEFINITION MATRIX:
            // customer_id -> i (integer)
            // slot_id     -> i (integer)
            // booking_date-> s (string)
            // num_guests  -> i (integer)
            // special_notes-> s (string)
            mysqli_stmt_bind_param($stmt, "iisis", $customer_id, $slot_id, $booking_date, $num_guests, $special_notes);
            
            if (mysqli_stmt_execute($stmt)) {
                $msg = '<div class="alert alert-success" style="font-weight:bold;">Reservation submitted successfully! Your active dining queue sequence is now pending staff confirmation layout matrix parameters.</div>';
            } else {
                $msg = '<div class="alert alert-danger">Database entry execution error: ' . mysqli_stmt_error($stmt) . '</div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            $msg = '<div class="alert alert-danger">SQL statement structure compilation breakdown: ' . mysqli_error($link) . '</div>';
        }
    }
}
?>

<?php include_once "includes/cust_header.php"; ?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading" style="background-color: #f5f5f5;">
                <h3 class="panel-title" style="font-weight: bold; text-align: center;">Coordinate a New Table Allocation</h3>
            </div>
            <div class="panel-body">
                
                <?php echo $msg; ?>

                <form name="bookingForm" action="make_booking.php" method="post">
                    <div class="form-group">
                        <label>Target Reservation Date:</label>
                        <input type="date" name="booking_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Available Time Window Allocation:</label>
                        <select name="slot_id" class="form-control" required>
                            <option value="">-- Choose an allocation window --</option>
                            <?php
                            $res_slots = mysqli_query($link, "SELECT * FROM timeslots ORDER BY slot_id ASC");
                            while ($s_row = mysqli_fetch_assoc($res_slots)) {
                                echo '<option value="' . $s_row['slot_id'] . '">' . htmlspecialchars($s_row['time_range']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests:</label>
                        <input type="number" name="num_guests" class="form-control" required min="1" max="20"
                               oninput="document.bookingForm.tables_needed.value = Math.ceil(this.value / 4);">
                    </div>

                    <div class="form-group" style="background-color: #f9f9f9; padding: 12px; border-radius: 4px; border: 1px solid #eee;">
                        <label style="color: #666; font-size: 13px;">Automated Spatial Estimate:</label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="text" name="tables_needed" class="form-control" readonly value="0" style="font-weight: bold; background:#fff;">
                            <span class="input-group-addon">Table(s)</span>
                        </div>
                        <p class="help-block" style="font-size: 11px;">Calculates client-side instantly assuming a maximum threshold of 4 chairs per table element.</p>
                    </div>

                    <div class="form-group">
                        <label>Special Instructions / Requirements:</label>
                        <textarea name="special_notes" class="form-control" rows="3" placeholder="Dietary adjustments, high-chairs, window seating preference, etc."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="font-weight: bold;">Submit Reservation Queue</button>
                    <a href="customer_dashboard.php" class="btn btn-default btn-block">Return to Dashboard</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>