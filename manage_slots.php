<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_slot'])) {
    $time_range = trim($_POST['time_range']);
    $status = trim($_POST['status']);
    
    // Gap 2: Operational time allocations
    $sql = "INSERT INTO timeslots (time_range, status) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $time_range, $status);
        if (mysqli_stmt_execute($stmt)) {
            $msg = '<div class="alert alert-success">Time frame matrix segment saved.</div>';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include_once "includes/staff_header.php"; ?>

<div class="container" style="margin-top:20px;">
    <h2>Manage Operating Time Slots</h2>
    <?php echo $msg; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Add Time Allocation Segment</h3></div>
                <div class="panel-body">
                    <form action="manage_slots.php" method="POST">
                        <div class="form-group">
                            <label>Time Window Scope Descriptor:</label>
                            <input type="text" name="time_range" class="form-control" required placeholder="e.g. 11:00 AM - 1:00 PM">
                        </div>
                        <div class="form-group">
                            <label>Initial Status:</label>
                            <select name="status" class="form-control">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" name="add_slot" class="btn btn-danger btn-block" style="font-weight:bold;">Create operational window slot</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Registered Database Time Slots</h3></div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th>Slot ID</th><th>Time Window Range</th><th>Operational Status</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($link, "SELECT * FROM timeslots");
                            while($row = mysqli_fetch_assoc($res)) {
                                echo '<tr>';
                                echo '<td>' . $row['slot_id'] . '</td>';
                                echo '<td>' . htmlspecialchars($row['time_range']) . '</td>';
                                echo '<td>' . $row['status'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>