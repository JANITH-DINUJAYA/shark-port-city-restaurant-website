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
 $codes = $_POST['codes'] ?? [];
        foreach ($codes as &$code) {
            $code['discount'] = floatval($code['discount']);
        }
        // Push new promotion to Firebase
        $newPromo = [
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'ingredients' => $_POST['ingredients'],
            'image' => $imgUrl,
            'codes' => $codes,
            'created_at' => time()
        ];
        $database->getReference('promotions')->push($newPromo);

        $message = "Promotion uploaded successfully!";
        $messageType = "success";
    } else {
        $message = "ImgBB upload failed: " . ($result['error']['message'] ?? 'Unknown error');
        $messageType = "danger";
    }
}

// Fetch existing promotions from Firebase
$promotionsRef = $database->getReference('promotions');
$promotionsSnapshot = $promotionsRef->getValue();
$promotions = $promotionsSnapshot ?: [];
?>

<!doctype html>
<html>
<head>
    <title>Shark Port City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <style>
            /* --- CSS Variables --- */
            :root {
                --primary-dark: #2c3e50;
                --secondary-light: #f4f7f6;
                --text-color: #333;
                --white: #ffffff;
                --accent-green: #2ecc71;
                --accent-blue: #3498db;
                --accent-red: #e74c3c;
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

            .promo-form-container, .promotions-display-container {
                background-color: var(--white);
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                padding: 40px;
                width: 100%;
                max-width: 1000px;
                text-align: left;
                margin-bottom: 30px;
            }

            .promo-form-container h1, .promotions-display-container h1 {
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
            .form-group textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 1rem;
                transition: border-color 0.3s ease;
            }

            .form-group input:focus,
            .form-group textarea:focus {
                outline: none;
                border-color: var(--accent-green);
                box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
            }

            .form-group textarea {
                resize: vertical;
                min-height: 120px;
            }

            .submit-button {
                background-color: #f70404;
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

            /* --- Promotions Grid Layout --- */
            .promotions-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 25px;
                padding-top: 20px;
            }

            .promo-card {
                background-color: var(--white);
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                text-align: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                position: relative;
            }

            .promo-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            .promo-card img {
                width: 100%;
                height: 200px;
                object-fit: cover;
            }

            .promo-details {
                padding: 20px;
            }

            .promo-details h3 {
                font-size: 1.25rem;
                color: var(--primary-dark);
                margin-bottom: 10px;
            }

            .promo-details .price {
                font-size: 1.1rem;
                color: #000000ff;
                font-weight: 600;
                margin-bottom: 15px;
            }

            .promo-actions {
                display: flex;
                justify-content: center;
                gap: 10px;
                padding: 0 20px 20px;
            }

            .action-button {
                padding: 10px 20px;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                transition: background-color 0.3s ease;
            }

            .update-button {
                background-color: #000000ff;
                color: var(--white);
            }

            .update-button:hover {
                background-color: #000000ff;
            }

            .delete-button {
                background-color: var(--accent-red);
                color: var(--white);
            }

            .delete-button:hover {
                background-color: #c0392b;
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
            /* --- Promo Codes Section --- */
#promoCodesContainer {
    margin-top: 10px;
}

.promo-code-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.promo-code-row input,
.promo-code-row select {
    flex: 1 1 150px;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 0.95rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.promo-code-row input:focus,
.promo-code-row select:focus {
    outline: none;
    border-color: var(--accent-green);
    box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
}

.remove-code-btn {
    background-color: var(--accent-red);
    color: var(--white);
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    flex: 0 0 auto;
}

.remove-code-btn:hover {
    background-color: #c0392b;
}

#addPromoCodeBtn {
    margin-top: 5px;
    margin-bottom:10px;
    background-color: #000000ff;
    color: var(--white);
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#addPromoCodeBtn:hover {
    background-color: #000000ff;
}

/* Optional: visually separate promo codes section from other form fields */
.form-group label + #promoCodesContainer {
    margin-bottom: 15px;
}

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
    <?php if($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="promo-form-container">
        <h1>Add New Promotion Item</h1>
        <form id="promoForm" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="promoTitle">Promotion Title</label>
                <input type="text" id="promoTitle" name="title" placeholder="e.g., Seafood Fiesta" required>
            </div>
            <div class="form-group">
                <label for="promoPrice">Price</label>
                <input type="number" id="promoPrice" name="price" step="0.01" placeholder="e.g., 25.00" required>
            </div>
            <div class="form-group">
                <label for="promoIngredients">Description / Details</label>
                <textarea id="promoIngredients" name="ingredients" placeholder="Describe the promotion" required></textarea>
            </div>
            <div class="form-group">
                <label for="promoImage">Promotion Photo</label>
                <input type="file" id="promoImage" name="image" accept="image/*" required>
            </div>
               <label>Promotion Codes</label>
    <div id="promoCodesContainer">
        <div class="promo-code-row mb-2">
            <input type="text" name="codes[0][code]" placeholder="Code e.g. NEWYEAR" class="form-control mb-1" required>
            <input type="number" name="codes[0][discount]" placeholder="Discount %" class="form-control mb-1" required>
            <select name="codes[0][status]" class="form-control mb-1" required>
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>
    <button type="button" id="addPromoCodeBtn" class="btn btn-secondary btn-sm">Add Another Code</button>
            <button type="submit" class="submit-button">Add Promotion Item</button>
          
        </form>
    </div>

    <div class="promotions-display-container">
        <h1>Current Promotions</h1>
        <div class="promotions-grid">
            <?php if(!empty($promotions)): ?>
                <?php foreach($promotions as $id => $promo): ?>
                    <div class="promo-card">
                        <img src="<?= htmlspecialchars($promo['image'] ?? 'https://via.placeholder.com/200') ?>" alt="<?= htmlspecialchars($promo['title'] ?? '') ?>">
                        <div class="promo-details">
                            <h3><?= htmlspecialchars($promo['title'] ?? '') ?></h3>
                            <div class="price">Rs. <?= number_format($promo['price'] ?? 0, 2) ?></div>
                        </div>
                        <div class="promo-actions">
                            <a href="edit_promotion.php?id=<?= $id ?>" class="action-button update-button">Edit</a>
                            <a href="delete_promotion.php?id=<?= $id ?>" class="action-button delete-button" onclick="return confirm('Are you sure you want to delete this promotion?');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No promotions found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    let codeIndex = 1; // Start from 1 because 0 already exists
    const addBtn = document.getElementById('addPromoCodeBtn');
    const container = document.getElementById('promoCodesContainer');

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.classList.add('promo-code-row', 'mb-2');
        row.innerHTML = `
            <input type="text" name="codes[${codeIndex}][code]" placeholder="Code e.g. NEWYEAR" class="form-control mb-1" required>
            <input type="number" name="codes[${codeIndex}][discount]" placeholder="Discount %" class="form-control mb-1" required>
            <select name="codes[${codeIndex}][status]" class="form-control mb-1" required>
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <button type="button" class="btn btn-danger btn-sm remove-code-btn">Remove</button>
        `;
        container.appendChild(row);
        codeIndex++;

        // Remove row
        row.querySelector('.remove-code-btn').addEventListener('click', () => row.remove());
    });
});
    </script>
</body>
</html>
