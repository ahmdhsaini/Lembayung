<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Admin Panel - Lembayung</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    
    <style>
        /* MASTER SCALE-UP: ENTIRE APPLICATION WIDE */
        html, body {
            font-size: 16px; /* Bumps the base font from 14px to 16px globally */
        }

        /* Automatically scale up tables, buttons, inputs, and alerts to match */
        .table th, .table td, .form-control, .btn, .alert, .panel {
            font-size: 15px !important; 
        }

        /* Keep headers slightly larger for proper visual hierarchy */
        h1, h2, h3, .panel-title {
            font-size: 22px !important;
        }
        /* Modern Premium Navigation Bar Styles - Staff Theme */
        .custom-navbar {
            background-color: #1e3a8a; /* Premium Deep Royal Blue */
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            padding: 12px 25px;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand-custom {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #ffffff !important;
            text-decoration: none;
            text-transform: uppercase;
        }

        .navbar-brand-custom small {
            font-size: 11px;
            letter-spacing: 1px;
            color: #fcd34d; /* Yellow gold accent tag for administration */
            display: block;
            font-weight: 400;
        }

        /* Forces items inline horizontally */
        .nav-links-container {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link-item a {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        /* Active/Hover Highlight Effect */
        .nav-link-item a:hover {
            background-color: #3b82f6; 
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        /* START OF UPDATE: Style rule for staff greeting component */
        .nav-user-greeting {
            color: #fcd34d;
            font-weight: 600;
            font-size: 13px;
            margin-right: 10px;
        }

        .logout-btn a {
            background-color: #ef4444; /* Crisp red logout button */
        }
        .logout-btn a:hover {
            background-color: #dc2626;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        }
    </style>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
<body>

<div class="container">
    <header class="custom-navbar">
        <div>
            <a class="navbar-brand-custom" href="staff_dashboard.php">
                LEMBAYUNG RESTAURANT
                <small>Staff Admin Panel</small>
            </a>
        </div>
        
        <ul class="nav-links-container">
            <li class="nav-user-greeting"><span class="glyphicon glyphicon-king"></span> Hi, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Staff'); ?></li>
            <li class="nav-link-item"><a href="staff_dashboard.php"><span class="glyphicon glyphicon-folder-open"></span> Manage Bookings</a></li>
            <li class="nav-link-item logout-btn"><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout Admin</a></li>
        </ul>
    </header>