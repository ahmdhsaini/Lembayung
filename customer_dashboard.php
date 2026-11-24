<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";
$customer_id = $_SESSION['user_id'];

// Gap 4: Handle Search and Status Filter Inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
?>

<?php include_once "includes/cust_header.php"; ?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div class="jumbotron" style="padding: 20px 30px; background-color: #fcfcfc; border: 1px solid #eee;">
            <h2>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p style="font-size: 16px;" class="text-muted">Manage your reservation histories or book new dining allocations below.</p>
        </div>
    </div>
</div>

<div class="well well-sm" style="background: #f5f5f5;">
    <form method="GET" action="customer_dashboard.php" class="form-inline">
        <div class="form-group" style="margin-right: 10px;">
            <label for="search" style="margin-right: 5px;">Search Booking ID:</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="e.g. 5" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="form-group" style="margin-right: 10px;">
            <label for="status" style="margin-right: 5px;">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="">-- All Statuses --</option>
                <option value="Pending" <?php if($status_filter == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Approved" <?php if($status_filter == 'Approved') echo 'selected'; ?>>Approved</option>
                <option value="Cancelled" <?php if($status_filter == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="font-weight: bold;">Filter Results</button>
        <a href="customer_dashboard.php" class="btn btn-default btn-sm">Clear</a>
    </form>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title" style="font-weight: bold;">Your Historical Table Bookings</h3>
    </div>
    <div class="panel-body">
        
        <?php
        // Gap 2 & 4: Dynamically construct Prepared Statement SQL 
        $sql = "SELECT b.id, b.booking_date, b.num_guests, b.status, t.time_range, b.table_id 
                FROM bookings b 
                JOIN timeslots t ON b.slot_id = t.slot_id 
                WHERE b.customer_id = ?";
        
        $params = [$customer_id];
        $types = "i";

        if ($search !== '') {
            $sql .= " AND b.id = ?";
            $params[] = intval($search);
            $types .= "i";
        }

        if ($status_filter !== '') {
            $sql .= " AND b.status = ?";
            $params[] = $status_filter;
            $types .= "s";
        }

        $sql .= " ORDER BY b.booking_date DESC";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                echo '<table class="table table-striped table-bordered">';
                echo '<thead><tr><th>Booking ID</th><th>Date</th><th>Time Range</th><th>Guests</th><th>Assigned Physical Table</th><th>Status</th><th>Actions</th></tr></thead>';
                echo '<tbody>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>#' . $row['id'] . '</td>';
                    echo '<td>' . $row['booking_date'] . '</td>';
                    echo '<td>' . $row['time_range'] . '</td>';
                    echo '<td>' . $row['num_guests'] . '</td>';
                    // Gap 3: Display actual physical table assignment text element dynamically
                    echo '<td>' . ($row['table_id'] ? 'Table ' . $row['table_id'] : '<span class="text-warning">Not Assigned Yet</span>') . '</td>';
                    echo '<td>' . $row['status'] . '</td>';
                    echo '<td>';
                    if ($row['status'] == 'Pending') {
                        echo '<a href="edit_booking.php?id=' . $row['id'] . '" class="btn btn-xs btn-primary" style="margin-right: 5px; font-weight: bold;">Edit</a>';
                        echo '<a href="delete_booking.php?id=' . $row['id'] . '" class="btn btn-xs btn-danger" style="font-weight: bold;" onclick="return confirm(\'Are you sure you want to delete this booking request?\');">Delete</a>';
                    } elseif ($row['status'] == 'Approved') {
                        echo '<a href="print_receipt.php?id=' . $row['id'] . '" target="_blank" class="btn btn-xs btn-success" style="font-weight: bold;"><span class="glyphicon glyphicon-print"></span> Print Slip</a>';
                    } else {
                        echo '<span class="text-muted" style="font-size: 11px;">Locked</span>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="alert alert-info" style="margin-bottom:0;">No bookings matched your criteria. <a href="make_booking.php">Make a new booking!</a></div>';
            }
            mysqli_stmt_close($stmt);
        }
        ?>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>