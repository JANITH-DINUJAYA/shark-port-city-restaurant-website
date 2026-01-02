<?php
// PHP logic remains the same
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

session_start();

// Firebase RTDB setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');
$database = $factory->createDatabase();

// Get item ID
if (!isset($_GET['id'])) {
    die('Menu item ID missing.');
}
$id = $_GET['id'];

// Fetch current menu item
$menuItemRef = $database->getReference('menu_items/' . $id);
$menuItem = $menuItemRef->getValue();
if (!$menuItem) die('Menu item not found.');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? $menuItem['category'];
    $title = $_POST['title'] ?? $menuItem['title'];
    $price = $_POST['price'] ?? $menuItem['price'];
    $ingredients = $_POST['ingredients'] ?? $menuItem['ingredients'];
    $imageUrl = $menuItem['image'] ?? '';

    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = time() . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $filename;

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $imageUrl = 'uploads/' . $filename;
        } else {
            // Handle file upload error if needed
        }
    }

    // Update item in RTDB
    $menuItemRef->update([
        'category' => $category,
        'title' => $title,
        'price' => floatval($price),
        'ingredients' => $ingredients,
        'image' => $imageUrl
    ]);

    header('Location: Menumanage.php');
    exit;
}
?>

<!doctype html>
<html>
<head>
    <title>Edit Menu Item | Shark Port City</title>
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
    <style>
        /* --- CSS Variables (Modified for form styling) --- */
        :root {
            --primary-dark: #2c3e50;
            --secondary-light: #f4f7f6;
            --text-color: #333;
            --white: #ffffff;
            --accent-green: #2ecc71;
            --accent-blue: #3498db;
            --accent-purple: #9b59b6;
            --accent-orange: #f39c12;
            --online-status: #34c759;
            --shadow-light: rgba(0, 0, 0, 0.1);
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
            gap: 40px;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-button {
            background-color: var(--primary-dark);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-weight: 600;
        }

        .back-button:hover {
            background-color: #34495e;
            color: var(--white);
        }

        /* --- Form Card Styles --- */
        .form-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 15px var(--shadow-light);
            padding: 40px;
            border: 1px solid #e0e0e0;
            max-width: 800px;
            width: 100%;
            margin: 0 auto; /* Center the form card */
        }

        .form-card h1 {
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--primary-dark);
            border-bottom: 3px solid var(--accent-orange);
            display: inline-block;
            padding-bottom: 10px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px 15px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-orange);
            box-shadow: 0 0 0 0.25rem rgba(243, 156, 18, 0.25); /* Accent orange glow */
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
        }

        textarea.form-control {
            min-height: 150px;
        }

        /* --- Button Styles (using the theme's colors) --- */
        .btn-primary {
            background-color: var(--accent-orange);
            border-color: var(--accent-orange);
            color: var(--white);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e67e22; /* Darker orange */
            border-color: #e67e22;
        }
        
        .btn-secondary {
            background-color: #bdc3c7;
            border-color: #bdc3c7;
            color: var(--primary-dark);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #95a5a6;
            border-color: #95a5a6;
        }
        
        /* --- Image Preview Style --- */
        .image-preview {
            max-width: 120px;
            height: auto;
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 5px;
            margin-top: 10px;
            box-shadow: 0 2px 4px var(--shadow-light);
        }

        /* --- Sidebar Navigation Styles (Copied from template) --- */
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
            z-index: 50;
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
</head>
<body>

<nav>
    <div class="logo">
        <img src="../images/Group 17.png" alt="Shark Port City Logo">
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
                <li><a href="Menumanage.php" style="text-decoration: underline solid greenyellow; text-decoration-thickness: 3px; padding-left: 40px; background-color: rgba(255, 255, 255, 0.1);">Menus</a></li>
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
    <div class="header-bar">
        <h1>Dish Management</h1>
        <a href="Menumanage.php" class="back-button"><i class="fas fa-arrow-left me-2"></i>Back to Menu</a>
    </div>

    <div class="form-card">
        <h1>Edit Menu Item</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-select" required>
                    <?php
                    $categories = ['Appertizers','Soups','Rice & Noodles','Grilled_Specials','Seafood','Pizza & Pasta','Desserts','Beverages','Sizzlers','Chef_Specials','Veggie Delights','Snacks'];
                    foreach ($categories as $cat) {
                        $selected = ($menuItem['category'] === $cat) ? 'selected' : '';
                        echo "<option value='$cat' $selected>" . str_replace('_', ' ', $cat) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($menuItem['title']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Price (LKR)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= htmlspecialchars($menuItem['price']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="ingredients" class="form-label">Ingredients</label>
                <textarea name="ingredients" id="ingredients" class="form-control" required><?= htmlspecialchars($menuItem['ingredients']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Dish Image</label>
                <?php if (!empty($menuItem['image'])): ?>
                    <div>Current Image:</div>
                    <img src="<?= htmlspecialchars($menuItem['image']) ?>" alt="Current Dish Image" class="image-preview mb-2">
                <?php else: ?>
                    <p class="text-muted">No current image.</p>
                <?php endif; ?>
                <label for="image" class="form-label mt-2">Replace Image </label><br>
                <input type="file" name="image" id="image" accept="image/jpeg, image/png class="form-control">
            </div>
            
            <div class="d-flex justify-content-between pt-3"> 
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Menu Item</button>
                <a href="Menumanage.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
            </div>
        </form>
    </div>
    </div>
</body>
</html>