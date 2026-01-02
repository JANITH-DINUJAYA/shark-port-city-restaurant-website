<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

session_start();

// Firebase setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Initialize message variables
$message = '';
$messageType = ''; // 'success' or 'danger'

// Only handle form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];

    // Encode image to base64
    $imageData = base64_encode(file_get_contents($fileTmpPath));

    // ImgBB API key
    $apiKey = 'bbfda5a6eaea6c85b9c3125b4c8cc463';

    // Upload to ImgBB
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'key' => $apiKey,
        'image' => $imageData,
        'name' => pathinfo($fileName, PATHINFO_FILENAME)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if(isset($result['data']['url'])) {
        $imgUrl = $result['data']['url'];

        // Push new menu item to Firebase
        $newItem = [
            'category' => $_POST['category'],
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'ingredients' => $_POST['ingredients'],
            'image' => $imgUrl,
            'created_at' => time()
        ];
        $database->getReference('menu_items')->push($newItem);

        $message = "Menu item uploaded successfully!";
        $messageType = "success";
    } else {
        $message = "ImgBB upload failed: " . ($result['error']['message'] ?? 'Unknown error');
        $messageType = "danger";
    }
}

// Fetch existing menu items from Firebase
$menuItemsRef = $database->getReference('menu_items');
$menuItemsSnapshot = $menuItemsRef->getValue();
$menuItems = $menuItemsSnapshot ?: [];
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
            /* --- CSS Variables --- */
            :root {
                --primary-dark: #2c3e50;
                --secondary-light: #f4f7f6;
                --text-color: #333;
                --white: #ffffff;
                --accent-green: #2ecc71;
                --accent-blue: #3498db;
                --accent-red: #e74c3c; /* Added for delete button */
                --accent-purple: #9b59b6;
                --accent-orange: #f39c12;
                --online-status: #34c759;
            }

            /* --- General & Main Container Styles --- */
            body {
                background-color: var(--secondary-light);
                color: var(--text-color);
                margin: 0;
                font-family: 'Poppins', sans-serif;
                display: flex;
                min-height: 100vh;
            }

            .main-content {
                flex-grow: 1;
                padding: 50px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .header-bar {
                width: 100%;
                max-width: 1000px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
            }

            .back-button {
                background-color: var(--primary-dark);
                color: var(--white);
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                text-decoration: none;
                transition: background-color 0.3s ease;
            }

            .back-button:hover {
                background-color: #34495e;
            }

            .menu-form-container, .menu-list-container {
                background-color: var(--white);
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                padding: 40px;
                width: 100%;
                max-width: 1000px;
                text-align: left;
                margin-bottom: 30px;
            }

            .menu-form-container h1, .menu-list-container h1 {
                font-size: 2rem;
                font-weight: 600;
                color: var(--primary-dark);
                margin-bottom: 25px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
                color: var(--text-color);
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 1rem;
                transition: border-color 0.3s ease;
            }

            .form-group input:focus,
            .form-group textarea:focus,
            .form-group select:focus {
                outline: none;
                border-color: var(--accent-green);
                box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
            }

            .form-group textarea {
                resize: vertical;
                min-height: 120px;
            }

            .submit-button, .action-button {
                background-color:#f70404;
                color: var(--white);
                border: none;
                padding: 15px 30px;
                font-size: 1.1rem;
                border-radius: 8px;
                cursor: pointer;
                transition: background-color 0.3s ease;
                display: block;
                width: 100%;
                font-weight: 600;
            }

            .submit-button:hover {
                background-color: #f70404;
            }
            .delete-button {
                background-color: var(--accent-red);
            }
            .delete-button:hover {
                background-color: #c0392b;
            }
            .update-button {
                background-color: #000000ff;
            }
            .update-button:hover {
                background-color: #000000ff;
            }

            /* --- Table Styles for displaying items --- */
            .menu-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .menu-table th, .menu-table td {
                padding: 12px 15px;
                border: 1px solid #ddd;
                text-align: left;
            }
            .menu-table th {
                background-color: #000000ff;
                color: var(--white);
                font-weight: 600;
            }
            .menu-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .menu-table tr:hover {
                background-color: #f1f1f1;
            }
            .menu-table img {
                max-width: 80px;
                height: auto;
                border-radius: 4px;
            }
            .menu-actions {
                display: flex;
                gap: 10px;
            }
            .menu-actions button {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            /* --- Navigation Bar (existing styles from your previous code) --- */
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
            .logo { margin-bottom: 20px; }
            .logo img { width: 150px; }
            .profile-status { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 20px; color: var(--white); }
            .online-indicator { width: 10px; height: 10px; background-color: var(--online-status); border-radius: 50%; animation: pulse-online 1.5s infinite; }
            @keyframes pulse-online { 0% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(52, 199, 89, 0); } 100% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0); } }
            nav ul { list-style: none; padding: 0; width: 100%; margin: 0; }
            nav ul li { position: relative; }
            nav ul li a { display: block; padding: 20px 30px; color: var(--white); text-decoration: none; font-size: 16px; transition: background-color 0.3s ease, padding-left 0.3s ease; cursor: pointer; }
            nav ul li a:hover { background-color: rgba(255, 255, 255, 0.1); padding-left: 40px; text-decoration: underline solid #f70404; text-decoration-thickness: 3px; }
            .dropdown-content { display: none; position: absolute; top: 100%; left: 0; background-color: #000000; width: 100%; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); z-index: 100; border-radius: 0 0 8px 8px; }
            .dropdown:hover .dropdown-content { display: block; }
            .dropdown-content li a:hover { background-color: #1a1a1a; padding-left: 45px; }
            @media (max-width: 992px) { .main-content { padding: 20px; } }
        </style>

        <nav>
            <div class="logo">
                <img src="../images/Group 17.png" alt="Shark Port City Logo">
            </div>
            <div class="profile-status">
                <span class="online-indicator"></span>
                Online
            </div>
            <ul>
                <li><a href="staf.html">Dashboard</a></li>
                
                <li class="dropdown">
                    <a href="#">Content Management ▾</a>
                    <ul class="dropdown-content">
                       
                        <li><a href="pgallery.html">Photo Galleries</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Dish Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="Menum.php">Menus</a></li>
                       
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Order & Reservation Management▾</a>
                    <ul class="dropdown-content">
                        <li><a href="orderm.html">Orders</a></li>
                        <li><a href="reservationm.html">Reservations</a></li>
                    </ul>
                </li>
                <li><a href="inquirie.html">Inquiries</a></li>
                <li><a href="login.html">Log Out</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <div class="header-bar">
                <h1>Menu Management</h1>
                <a href="staf.html" class="back-button">Back to Dashboard</a>
            </div>

          <div class="main-content">
        <?php if($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="menu-form-container">
            <h1>Add New Menu Item</h1>
            <form id="menuForm" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="menuCategory">Menu Category</label>
                    <select id="menuCategory" name="category" required>
                        <option value="">-- Select a Category --</option>
                        <option value="Appertizers">Appertizers</option>
                        <option value="Soups">Soups & Salads</option>
                        <option value="Rice & Noodles">Rice & Noodles</option>
                        <option value="Seafood">Seafood</option>
                        <option value="Grilled_Specials">Grilled Specials</option>
                        <option value="Sizzlers">Sizzlers</option>
                        <option value="Veggie Delights">Veggie Delights</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Pizza & Pasta">Pizza & Pasta</option>
                        <option value="Chef_Specials">Chef's Specials</option>
                        <option value="Snacks">Snacks</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="menuTitle">Dish Title</label>
                    <input type="text" id="menuTitle" name="title" placeholder="e.g., Seafood Pasta" required>
                </div>
                <div class="form-group">
                    <label for="menuPrice">Price</label>
                    <input type="number" id="menuPrice" name="price" step="0.01" placeholder="e.g., 12.99" required>
                </div>
                <div class="form-group">
                    <label for="menuIngredients">Ingredients</label>
                    <textarea id="menuIngredients" name="ingredients" placeholder="List ingredients separated by commas" required></textarea>
                </div>
                <div class="form-group">
                    <label for="menuImage">Dish Photo</label>
                    <input type="file" id="menuImage" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="submit-button">Add Menu Item</button>
            </form>
        </div>

        <div class="menu-list-container">
            <h1>Existing Menu Items</h1>
            <table class="menu-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Ingredients</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($menuItems)): ?>
                    <?php foreach($menuItems as $id => $data): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($data['image'] ?? 'https://via.placeholder.com/150') ?>" alt="Dish Photo"></td>
                            <td><?= htmlspecialchars($data['category'] ?? '') ?></td>
                            <td><?= htmlspecialchars($data['title'] ?? '') ?></td>
                            <td>$<?= number_format($data['price'] ?? 0, 2) ?></td>
                            <td><?= htmlspecialchars($data['ingredients'] ?? '') ?></td>
                            <td class="menu-actions">
                                <a href="edit_item.php?id=<?= $id ?>" class="action-button update-button">Edit</a>
                             
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No menu items found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>