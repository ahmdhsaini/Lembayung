<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_table'])) {
    $table_number = trim($_POST['table_number']);
    $capacity = trim($_POST['capacity']);
    
    // Adjusted database column layout structure to map 'seating_capacity' 
    $sql = "INSERT INTO restaurant_tables (table_number, seating_capacity) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $table_number, $capacity);
        if (mysqli_stmt_execute($stmt)) {
            $msg = '<div class="alert alert-success">Table catalog element saved.</div>';
        }
        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM restaurant_tables WHERE table_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $del_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: manage_tables.php");
    exit();
}
?>

<?php include_once "includes/staff_header.php"; ?>

<div class="container" style="margin-top:20px;">
    <h2>Manage Dining Room Tables</h2>
    <?php echo $msg; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Add Physical Table</h3></div>
                <div class="panel-body">
                    <form action="manage_tables.php" method="POST">
                        <div class="form-group">
                            <label>Table Code Name:</label>
                            <input type="text" name="table_number" class="form-control" required placeholder="e.g. A1">
                        </div>
                        <div class="form-group">
                            <label>Capacity Threshold:</label>
                            <input type="number" name="capacity" class="form-control" required min="1" max="10">
                        </div>
                        <button type="submit" name="add_table" class="btn btn-danger btn-block" style="font-weight:bold;">Register Table</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title" style="font-weight: bold;">Current Inventory Elements</h3></div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th>Table ID</th><th>Table Code</th><th>Capacity</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($link, "SELECT * FROM restaurant_tables ORDER BY table_number ASC");
                            while($row = mysqli_fetch_assoc($res)) {
                                echo '<tr>';
                                echo '<td>' . $row['table_id'] . '</td>';
                                echo '<td>Table ' . htmlspecialchars($row['table_number']) . '</td>';
                                // Fixed Line 78 warning by mapping database index to seating_capacity
                                echo '<td>' . htmlspecialchars($row['seating_capacity']) . ' Seats</td>';
                                echo '<td><a href="manage_tables.php?delete_id=' . $row['table_id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Delete item?\');">Remove</a></td>';
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