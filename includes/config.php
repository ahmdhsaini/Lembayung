<?php
/* Database credentials based on Laragon defaults and your database setup */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Leave blank for Laragon/XAMPP default
define('DB_NAME', 'lembayung_db');

/* Attempt to connect to MySQL database using the Week 10 lab template */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>