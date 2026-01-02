<?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

// --- Firebase Setup ---
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// --- Fetch counts directly from Realtime Database ---
$total_orders = $database->getReference('orders_count')->getValue() ?? 0;
$total_reservations = $database->getReference('reservations_count')->getValue() ?? 0;
$total_promotions = $database->getReference('promotions_count')->getValue() ?? 0;
$total_food_items = $database->getReference('menu_items_count')->getValue() ?? 0;
$total_messages = $database->getReference('inquiries_count')->getValue() ?? 0;

// --- User counts (assume you stored them in a stats node) ---
$user_stats = $database->getReference('stats/users')->getValue() ?? [
    'total_members' => 0,
    'admin' => 0,
    'staff' => 0,
    'client' => 0
];

$total_members = $user_stats['total_members'];
$user_counts = [
    'admin' => $user_stats['admin'],
    'staff' => $user_stats['staff'],
    'client' => $user_stats['client']
];
?>

<!doctype html>
<html>
    <head>
        <title>Shark Port City</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
            rel="stylesheet"
        />
    </head>
    <body>
        <style>
            /* --- CSS Variables (Centralized Color Palette) --- */
            :root {
                --primary-dark: #2c3e50;
                --secondary-light: #f4f7f6;
                --text-color: #333;
                --white: #ffffff;
                --accent-green: #2ecc71;
                --accent-blue: #3498db;
                --accent-purple: #9b59b6;
                --online-status: #34c759;
                --accent-red: #e74c3c;
            }

            /* --- General Styles --- */
            body {
                background-color: var(--secondary-light);
                color: var(--text-color);
                margin: 0;
                font-family: 'Poppins', sans-serif;
                display: flex;
                min-height: 100vh;
            }

            /* --- Navigation Bar --- */
            nav {
                background-color: #000000;
                width: 350px;
                padding: 30px 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
                position: sticky;
                top: 0;
                left: 0;
                height: 100vh;
            }

            .logo{
                display: flex;
                align-items: center;
            }
            .profile-status {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                margin-top: 5px;
            }
            .online-indicator {
                width: 10px;
                height: 10px;
                padding-left: 2px;
                background-color: var(--online-status);
                border-radius: 50%;
                animation: pulse-online 1.5s infinite;
            }

            @keyframes pulse-online {
                0% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.7); }
                70% { box-shadow: 0 0 0 10px rgba(52, 199, 89, 0); }
                100% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0); }
            }

            nav ul {
                list-style: none;
                padding: 0;
                width: 100%;
                margin: 0;
            }

            nav ul li {
                position: relative;
            }

            nav ul li a {
                display: block;
                padding: 20px 30px;
                color: var(--white);
                text-decoration: none;
                font-size: 16px;
                transition: background-color 0.3s ease, padding-left 0.3s ease;
                cursor: pointer;
            }

            nav ul li a:hover {
                background-color: rgba(255, 255, 255, 0.1);
                padding-left: 40px;
                text-decoration: underline solid #f70404;
                text-decoration-thickness: 3px;
            }
            
            /* --- Dropdown Styles --- */
            .dropdown-content {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                background-color: #000000;
                width: 100%; /* Match parent width */
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                z-index: 100;
                border-radius: 0 0 8px 8px;
            }

            .dropdown:hover .dropdown-content {
                display: block;
            }

            .dropdown-content li a:hover {
                background-color: #1a1a1a;
                padding-left: 45px; /* Slight extra indentation */
            }
            
            /* --- Main Content & Dashboard Cards --- */
            .main-content {
                flex-grow: 1;
                padding: 50px;
                display: flex;
                flex-direction: column;
                gap: 40px;
            }

            .dashboard-row {
                display: flex;
                gap: 30px;
                width: 100%;
                justify-content: space-between;
            }

            .dashboard-card {
                background-color: var(--white);
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                padding: 30px;
                flex: 1;
                min-width: 180px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border: 3px solid var(--accent-green);
            }

            .dashboard-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            .dashboard-card h2 {
                font-size: 1.5rem;
                color: var(--primary-dark);
                margin-bottom: 10px;
                font-weight: 600;
            }

            .dashboard-card h3,
            .dashboard-card h4,
            .dashboard-card h5 {
                font-size: 2.5rem;
                font-weight: 300;
                color: var(--accent-green);
                margin: 0;
            }

            /* --- Themed Card Styles --- */
            .dashboard-card.theme-2 {
                border-color: var(--accent-blue);
            }
            .dashboard-card.theme-2 h2, .dashboard-card.theme-2 h3, .dashboard-card.theme-2 h4, .dashboard-card.theme-2 h5, .dashboard-card.theme-2 ul li {
                color: var(--accent-blue);
            }

            .dashboard-card.theme-3 {
                border-color: var(--accent-purple);
            }
            .dashboard-card.theme-3 h2, .dashboard-card.theme-3 h3, .dashboard-card.theme-3 h4, .dashboard-card.theme-3 h5, .dashboard-card.theme-3 ul li {
                color: var(--accent-purple);
            }
            
            /* --- Social Media Cards --- */
            .social-media-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 30px;
                margin-top: 40px;
            }

            .social-card {
                background-color: var(--white);
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                padding: 20px;
                transition: transform 0.3s ease;
            }

            .social-card:hover {
                transform: translateY(-5px);
            }

            .social-icon {
                font-size: 3rem;
                color: var(--white);
                width: 70px;
                height: 70px;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%;
                margin-right: 15px;
            }

            .social-card.facebook .social-icon { background-color: #3b5998; }
            .social-card.twitter .social-icon { background-color: #1da1f2; }
            .social-card.linkedin .social-icon { background-color: #0077b5; }
            .social-card.google-plus .social-icon { background-color: #db4437; }

            .social-content ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .social-content ul li {
                font-size: 1rem;
                color: #555;
            }

            .social-content strong {
                font-size: 1.2rem;
                font-weight: 600;
                color: var(--primary-dark);
            }
        </style>
        <nav>
          <div class="logo">
            <img src="../images/Group 17.png">
          </div>
                <div class="profile-status">
                    <span class="online-indicator"></span>
                    Online
                </div>
            
            <ul>
                <li><a href="home.php">Dashboard</a></li>
                <li class="dropdown">
                    <a href="#">User Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="customer.html">Customer</a></li>
                        <li><a href="partner.html">Delivery Partners</a></li>
                        <li><a href="staff.html">Staff</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Content Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="banners.html">Banners</a></li>
                        <li><a href="photogallery.html">Photo Galleries</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Dish Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="Menumanage.php">Menus</a></li>
                        <li><a href="promotionmanagement.php">Promotions</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Order & Reservation Management▾</a>
                    <ul class="dropdown-content">
                        <li><a href="ordermanage.html">Orders</a></li>
                        <li><a href="reservationmanage.html">Reservations</a></li>
                    </ul>
                </li>
                <li><a href="inquiries.html">Inquiries</a></li>
           <li><a href="send-email.php">Notifications</a></li>
                <li><a href="login.html">Log Out</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <div class="dashboard-row">
                <div class="dashboard-card">
                    <h2>Total Members</h2>
                    <h3><?= $total_members ?></h3>
                </div>
                <div class="dashboard-card theme-2">
                    <h2>Account Type</h2>
                    <ul>
                        <li>Admins: <?= $user_counts['admin'] ?? 0 ?></li>
                        <li>Staff: <?= $user_counts['staff'] ?? 0 ?></li>
                        <li>Customers: <?= $user_counts['client'] ?? 0 ?></li>
                    </ul>
                </div>
                <div class="dashboard-card theme-3">
                    <h2>Inquiries</h2>
                    <h4><?= $total_messages ?></h4>
                </div>
            </div>

            <div class="dashboard-row">
                <div class="dashboard-card">
                    <h2>Total Orders</h2>
                    <h5><?= $total_orders ?></h5>
                </div>
                <div class="dashboard-card theme-2">
                    <h2>Total Reservations</h2>
                    <h5><?= $total_reservations ?></h5>
                </div>
            </div>

            <div class="dashboard-row">
                <div class="dashboard-card theme-3">
                    <h2>Available Promotions</h2>
                    <h3><?= $total_promotions ?></h3>
                </div>
                <div class="dashboard-card">
                    <h2>Total Food Items</h2>
                    <h3><?= $total_food_items ?></h3>
                </div>
            </div>
            
            <div class="social-media-grid">
                <div class="social-card facebook">
                    <div class="social-icon">
                        <i class="fa fa-facebook"></i>
                    </div>
                    <div class="social-content">
                        <ul>
                            <li><strong>35k</strong> Friends</li>
                            <li><strong>128</strong> Feeds</li>
                        </ul>
                    </div>
                </div>
                <div class="social-card twitter">
                    <div class="social-icon">
                        <i class="fa fa-twitter"></i>
                    </div>
                    <div class="social-content">
                        <ul>
                            <li><strong>584k</strong> Followers</li>
                            <li><strong>978</strong> Tweets</li>
                        </ul>
                    </div>
                </div>
                <div class="social-card linkedin">
                    <div class="social-icon">
                        <i class="fa fa-linkedin"></i>
                    </div>
                    <div class="social-content">
                        <ul>
                            <li><strong>758+</strong> Contacts</li>
                            <li><strong>365</strong> Feeds</li>
                        </ul>
                    </div>
                </div>
                <div class="social-card google-plus">
                    <div class="social-icon">
                        <i class="fa fa-google-plus"></i>
                    </div>
                    <div class="social-content">
                        <ul>
                            <li><strong>450</strong> Followers</li>
                            <li><strong>57</strong> Circles</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>