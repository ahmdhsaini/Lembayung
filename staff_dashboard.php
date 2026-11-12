<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

// Analytics parameters
$r_pending = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as total FROM bookings WHERE status='Pending'"));
$total_pending = $r_pending['total'];

$r_approved = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as total FROM bookings WHERE status='Approved'"));
$total_approved = $r_approved['total'];

$today_date = date('Y-m-d');
$r_guests = mysqli_fetch_assoc(mysqli_query($link, "SELECT SUM(num_guests) as total_guests FROM bookings WHERE booking_date = '$today_date' AND status='Approved'"));
$total_guests = $r_guests['total_guests'] ?? 0;

// Gap 4: Handle Search and Status Filter Inputs
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
?>

<?php include_once "includes/staff_header.php"; ?>

<div class="row" style="margin-top: 30px;">
    <div class="col-md-4">
        <div class="panel panel-warning" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px;">Pending Queue: <strong><?php echo $total_pending; ?> Requests</strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-success" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px;">Approved Active: <strong><?php echo $total_approved; ?> Reservations</strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-danger" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px;">Expected Diners (Today): <strong><?php echo $total_guests; ?> Pax</strong></div>
        </div>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="manage_tables.php" class="btn btn-info font-weight-bold" style="margin-right:10px;"><span class="glyphicon glyphicon-th"></span> Manage Tables Catalog</a>
    <a href="manage_slots.php" class="btn btn-info font-weight-bold"><span class="glyphicon glyphicon-time"></span> Manage Operation Time Slots</a>
</div>

<div class="well well-sm" style="background: #f5f5f5;">
    <form method="GET" action="staff_dashboard.php" class="form-inline">
        <div class="form-group" style="margin-right: 10px;">
            <label for="search_name" style="margin-right: 5px;">Customer Name:</label>
            <input type="text" name="search_name" id="search_name" class="form-control" placeholder="Search by name..." value="<?php echo htmlspecialchars($search_name); ?>">
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
        <button type="submit" class="btn btn-primary btn-sm" style="font-weight: bold;">Filter Queue</button>
        <a href="staff_dashboard.php" class="btn btn-default btn-sm">Reset</a>
        <a href="export_bookings.php" class="btn btn-success btn-sm pull-right" style="font-weight: bold;"><span class="glyphicon glyphicon-export"></span> Export Report (.CSV)</a>
    </form>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Unified Processing Reservation Queue</h3></div>
    <div class="panel-body">
        <?php
        // Gap 2 & 4: Prepared Statement construction with custom string filtering criteria safely
        $sql = "SELECT b.id, u.full_name, u.phone_number, b.booking_date, t.time_range, b.num_guests, b.special_notes, b.status, b.table_id 
                FROM bookings b 
                JOIN users u ON b.customer_id = u.id 
                JOIN timeslots t ON b.slot_id = t.slot_id 
                WHERE 1=1";
        
        $params = [];
        $types = "";

        if ($search_name !== '') {
            $sql .= " AND u.full_name LIKE ?";
            $params[] = "%" . $search_name . "%";
            $types .= "s";
        }

        if ($status_filter !== '') {
            $sql .= " AND b.status = ?";
            $params[] = $status_filter;
            $types .= "s";
        }

        // ADJUSTMENT: Order chronologically by booking date, then sub-order chronologically by timeslot id
        $sql .= " ORDER BY b.booking_date ASC, t.slot_id ASC";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (!empty($types)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                echo '<div class="table-responsive"><table class="table table-bordered table-striped">';
                echo '<thead><tr><th>ID</th><th>Client</th><th>Contact</th><th>Date</th><th>Time Window</th><th>Guests</th><th>Internal Notes</th><th>Assigned Table</th><th>Status</th><th>Action Control</th></tr></thead><tbody>';
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $labelType = ($row['status'] == 'Approved') ? 'label-success' : (($row['status'] == 'Cancelled') ? 'label-danger' : 'label-warning');
                    echo '<tr>';
                    echo '<td>#' . $row['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['phone_number']) . '</td>';
                    echo '<td>' . $row['booking_date'] . '</td>';
                    echo '<td>' . $row['time_range'] . '</td>';
                    echo '<td>' . $row['num_guests'] . '</td>';
                    echo '<td><small class="text-muted">' . htmlspecialchars($row['special_notes']) . '</small></td>';
                    // Gap 3: Display assigned table code or none
                    echo '<td>' . ($row['table_id'] ? 'Table #' . $row['table_id'] : '<span class="text-danger">None</span>') . '</td>';
                    echo '<td><span class="label ' . $labelType . '">' . $row['status'] . '</span></td>';
                    echo '<td>';
                    echo '<a href="update_booking.php?id=' . $row['id'] . '" class="btn btn-xs btn-primary" style="font-weight:bold;">Process Action</a>';
                    echo '</td></tr>';
                }
                echo '</tbody></table></div>';
            } else {
                echo '<div class="alert alert-info">No reservation queue elements found matching active criteria.</div>';
            }
            mysqli_stmt_close($stmt);
        }
        ?>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>