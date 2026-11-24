<?php
session_start();

session_unset();
session_destroy();

// Gap 1: Invalidate and clear local client tracking cookies safely
if (isset($_COOKIE['user_login'])) {
    setcookie("user_login", "", time() - 3600, "/");
}

header("Location: index.php");
exit();
?>