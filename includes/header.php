<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembayung Restaurant</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    
    <style>
        /* MASTER SCALE-UP: ENTIRE APPLICATION WIDE */
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
        /* Modern Premium Navigation Bar Styles - Blue Theme */
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
            color: #ffffff !important; /* Crisp White Branding text */
            text-decoration: none;
            text-transform: uppercase;
        }

        /* Forces items inline horizontally and removes ugly bullet points */
        .nav-links-container {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px; /* Crisp gap placement between elements */
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

        /* Active/Hover Highlight Effect - Soft Electric Blue Accent */
        .nav-link-item a:hover {
            background-color: #3b82f6; 
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        /* Webpage Hero & Component Layout Extensions */
        .hero-section {
            background-color: #f8fafc;
            padding: 65px 20px;
            text-align: center;
            border-radius: 8px;
            margin-top: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        }
        .restaurant-feature {
            padding: 25px 20px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .restaurant-feature:hover {
            transform: translateY(-4px);
            border-color: #3b82f6; /* Accent border on highlight hover */
        }
        .login-panel {
            max-width: 400px;
            margin: 60px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="container">
    <header class="custom-navbar">
        <div>
            <a class="navbar-brand-custom" href="index.php">LEMBAYUNG RESTAURANT</a>
        </div>
        
        <ul class="nav-links-container">
            <li class="nav-link-item"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
            <li class="nav-link-item"><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login Portal</a></li>
            <li class="nav-link-item"><a href="register.php"><span class="glyphicon glyphicon-user"></span> Create Account</a></li>
        </ul>
    </header>