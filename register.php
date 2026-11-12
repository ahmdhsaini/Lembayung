<?php
require_once "includes/config.php";

$username_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $full_name = trim($_POST["full_name"]);
    $phone_number = trim($_POST["phone_number"]);
    
    // Gap 2: Prepared statement for check query
    $sql_check = "SELECT id FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $username_err = "This username is already taken.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);
            
            // Gap 2: Prepared statement for insert query
            $sql_insert = "INSERT INTO users (username, password, full_name, phone_number, role) VALUES (?, ?, ?, ?, 'customer')";
            if ($stmt_ins = mysqli_prepare($link, $sql_insert)) {
                mysqli_stmt_bind_param($stmt_ins, "ssss", $username, $password, $full_name, $phone_number);
                if (mysqli_stmt_execute($stmt_ins)) {
                    $success_msg = "Account created successfully! You can now log in.";
                } else {
                    $username_err = "Something went wrong. Please try again.";
                }
                mysqli_stmt_close($stmt_ins);
            }
        }
    }
}
?>

<?php include_once "includes/header.php"; ?>

<div class="panel panel-default" style="max-width: 500px; margin: 40px auto;">
    <div class="panel-heading">
        <h3 class="panel-title" style="font-weight: bold;">Register Account</h3>
    </div>
    <div class="panel-body">
        <?php 
        if(!empty($username_err)) { echo '<div class="alert alert-danger">'.$username_err.'</div>'; } 
        if(!empty($success_msg)) { echo '<div class="alert alert-success">'.$success_msg.'</div>'; } 
        ?>
        <form action="register.php" method="post" onsubmit="return validateRegistration()">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password (Min 6 characters):</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-danger btn-block" style="font-weight: bold;">
                Register Account
            </button>
            <p style="margin-top:15px; text-align:center;" class="text-muted">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</div>
<script src="js/validation.js"></script>
<?php include_once "includes/footer.php"; ?>