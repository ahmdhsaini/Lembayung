<?php
session_start();
require_once "includes/config.php";

$login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // ADJUSTMENT: Added full_name to the SELECT fields inside the secure prepared statement
    $sql = "SELECT id, username, password, role, full_name FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                // ADJUSTMENT: Save the database full_name value directly into the session 
                $_SESSION['full_name'] = $row['full_name'];
                
                // Gap 1: Cookies (Remember Me) Implementation
                if (isset($_POST['remember_me'])) {
                    setcookie("user_login", $username, time() + (86400 * 30), "/"); 
                } else {
                    if (isset($_COOKIE["user_login"])) {
                        setcookie("user_login", "", time() - 3600, "/");
                    }
                }
                
                mysqli_stmt_close($stmt);
                
                if (strtolower($row['role']) === 'staff') {
                    header("Location: staff_dashboard.php");
                } else {
                    header("Location: customer_dashboard.php");
                }
                exit();
                
            } else {
                $login_err = "Invalid password.";
            }
        } else {
            $login_err = "No user found with that username.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include_once "includes/header.php"; ?>

<div class="panel panel-default login-panel" style="max-width: 500px; margin: 40px auto;">
    <div class="panel-heading" style="background-color: #f5f5f5;">
        <h3 class="panel-title" style="font-weight: bold; text-align: center;">Lembayung System Sign-In</h3>
    </div>
    <div class="panel-body">
        
        <?php if(!empty($login_err)) { echo '<div class="alert alert-danger">'.$login_err.'</div>'; } ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required placeholder="Type your username" value="<?php if(isset($_COOKIE['user_login'])) { echo htmlspecialchars($_COOKIE['user_login']); } ?>">
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required placeholder="Type your password">
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember_me" <?php if(isset($_COOKIE['user_login'])) { echo "checked"; } ?>> Remember Me
                </label>
            </div>
            <button type="submit" class="btn btn-danger btn-block" style="font-weight: bold; margin-top: 20px;">
                Secure Login
            </button>
            
            <p style="margin-top: 15px; text-align: center;" class="text-muted">
                New customer? <a href="register.php">Register here</a>
            </p>
        </form>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>