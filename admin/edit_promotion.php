<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

session_start();

// Firebase setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Get promotion ID from URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Promotion ID not specified.");
}

// Fetch promotion
$promoRef = $database->getReference('promotions/' . $id);
$promo = $promoRef->getValue();
if (!$promo) {
    die("Promotion not found.");
}

// Get current promo codes for display
$promoCodes = array_values($promo['codes'] ?? []);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';

    // Handle image upload
    $imageUrl = $promo['image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $imageData = base64_encode(file_get_contents($fileTmpPath));

        $apiKey = 'bbfda5a6eaea6c85b9c3125b4c8cc463';
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
        if (isset($result['data']['url'])) {
            $imageUrl = $result['data']['url'];
        } else {
            die('ImgBB upload failed: ' . ($result['error']['message'] ?? 'Unknown error'));
        }
    }

    // Get submitted promo codes
    $submittedCodes = $_POST['promoCodes'] ?? [];
    foreach ($submittedCodes as &$code) {
        $code['discount'] = floatval($code['discount']);
    }

    // Update promotion in Firebase
    $promoRef->update([
        'title' => $title,
        'price' => floatval($price),
        'ingredients' => $description,
        'image' => $imageUrl,
        'codes' => $submittedCodes
    ]);

    header('Location: promotionmanagement.php');
    exit;
}
?>

<!doctype html>
<html>
<head>
    <title>Edit Promotion | Shark Port City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary-dark: #2c3e50;
            --secondary-light: #f4f7f6;
            --text-color: #333;
            --white: #ffffff;
            --accent-green: #2ecc71;
            --accent-blue: #3498db;
            --accent-orange: #f39c12;
            --online-status: #34c759;
            --shadow-light: rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--secondary-light);
            color: var(--text-color);
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            min-height: 100vh;
        }

        nav {
            background-color: #000;
            width: 350px;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
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
        @keyframes pulse-online { 0% { box-shadow:0 0 0 0 rgba(52,199,89,0.7); } 70% { box-shadow:0 0 0 10px rgba(52,199,89,0); } 100% { box-shadow:0 0 0 0 rgba(52,199,89,0); } }
        nav ul { list-style: none; padding: 0; width: 100%; margin:0; }
        nav ul li { position: relative; }
        nav ul li a { display: block; padding: 20px 30px; color: var(--white); text-decoration:none; font-size:16px; transition: background-color 0.3s, padding-left 0.3s; cursor:pointer; }
        nav ul li a:hover { background-color: rgba(255,255,255,0.1); padding-left:40px; text-decoration: underline solid #f70404; text-decoration-thickness: 3px; }
        .dropdown-content { display:none; position:absolute; top:100%; left:0; background-color:#000; width:100%; box-shadow:0 8px 16px rgba(0,0,0,0.2); z-index:100; border-radius:0 0 8px 8px; }
        .dropdown:hover .dropdown-content { display:block; }
        .dropdown-content li a:hover { background-color: #1a1a1a; padding-left:45px; }

        .main-content { flex-grow:1; padding:50px; display:flex; flex-direction:column; gap:40px; }
        .header-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .back-button { background-color: var(--primary-dark); color: var(--white); border:none; padding:10px 20px; border-radius:8px; font-weight:600; text-decoration:none; transition:0.3s; }
        .back-button:hover { background-color:#34495e; }

        .form-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow:0 4px 15px var(--shadow-light);
            padding:40px;
            border:1px solid #e0e0e0;
            max-width:800px;
            width:100%;
            margin:0 auto;
        }
        .form-card h1 { font-weight:600; margin-bottom:30px; color: var(--primary-dark); border-bottom:3px solid var(--accent-orange); display:inline-block; padding-bottom:10px; }
        .form-control { border-radius:8px; border:1px solid #ccc; padding:10px 15px; font-size:1rem; transition:0.3s; }
        .form-control:focus { border-color: var(--accent-orange); box-shadow:0 0 0 0.25rem rgba(243,156,18,0.25); }
        label { font-weight:600; margin-bottom:8px; color: var(--text-color); }
        textarea.form-control { min-height:150px; }
        .btn-primary { background-color: var(--accent-orange); border-color: var(--accent-orange); color: var(--white); padding:10px 25px; border-radius:8px; font-weight:600; transition:0.3s; }
        .btn-primary:hover { background-color:#e67e22; border-color:#e67e22; }
        .btn-secondary { background-color:#bdc3c7; border-color:#bdc3c7; color:var(--primary-dark); padding:10px 25px; border-radius:8px; font-weight:600; transition:0.3s; }
        .btn-secondary:hover { background-color:#95a5a6; border-color:#95a5a6; }
        .image-preview { max-width:120px; height:auto; border-radius:6px; border:1px solid #ccc; padding:5px; margin-top:10px; box-shadow:0 2px 4px var(--shadow-light); }
   .promo-code-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.promo-code-row input,
.promo-code-row select {
    flex: 1 1 120px;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
.promo-code-row input:focus,
.promo-code-row select:focus {
    outline: none;
    border-color: var(--accent-orange);
    box-shadow: 0 0 0 3px rgba(243,156,18,0.2);
}
.remove-code-btn {
    background-color: #e74c3c;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
}
.remove-code-btn:hover {
    background-color: #c0392b;
}
#addPromoCodeBtn {
    margin-top: 10px;
    background-color: #000000ff;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
}
#addPromoCodeBtn:hover {
    background-color: #000000ff;
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
                <li><a href="Menumanage.php" style="text-decoration: underline solid #f70404; text-decoration-thickness: 3px; padding-left: 40px; background-color: rgba(255, 255, 255, 0.1);">Menus</a></li>
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
        <h1>Promotion Management</h1>
        <a href="promotionmanagement.php" class="back-button"><i class="fas fa-arrow-left me-2"></i>Back to Promotions</a>
    </div>

    <div class="form-card">
        <h1>Edit Promotion</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Promotion Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($promo['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (LKR)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= htmlspecialchars($promo['price']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" required><?= htmlspecialchars($promo['ingredients']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Promotion Image</label>
                <?php if (!empty($promo['image'])): ?>
                    <div>Current Image:</div>
                    <img src="<?= htmlspecialchars($promo['image']) ?>" alt="Promotion Image" class="image-preview mb-2">
                <?php else: ?>
                    <p class="text-muted">No current image.</p>
                <?php endif; ?>
                <label for="image" class="form-label mt-2">Replace Image</label><br>
                <input type="file" name="image" id="image" accept="image/jpeg, image/png" class="form-control">
            </div>
            <div class="mb-3">
    <label class="form-label">Promo Codes</label>
    <div id="promoCodesContainer">
        <?php if(!empty($promoCodes)): ?>
            <?php foreach($promoCodes as $index => $code): ?>
                <div class="promo-code-row mb-2">
                    <input type="text" name="promoCodes[<?= $index ?>][code]" placeholder="Code" value="<?= htmlspecialchars($code['code']) ?>" required>
                    <input type="number" name="promoCodes[<?= $index ?>][discount]" placeholder="Discount %" value="<?= htmlspecialchars($code['discount']) ?>" required>
                    <select name="promoCodes[<?= $index ?>][status]" required>
                        <option value="Active" <?= $code['status']=='Active'?'selected':'' ?>>Active</option>
                        <option value="Inactive" <?= $code['status']=='Inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                    <button type="button" class="remove-code-btn">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" id="addPromoCodeBtn">Add Another Code</button>
</div>
            <div class="d-flex justify-content-between pt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Promotion</button>
                <a href="promotionmanagement.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('addPromoCodeBtn').addEventListener('click', function() {
    const container = document.getElementById('promoCodesContainer');
    const index = container.children.length;
    
    const row = document.createElement('div');
    row.className = 'promo-code-row mb-2';
    row.innerHTML = `
        <input type="text" name="promoCodes[${index}][code]" placeholder="Code" required>
        <input type="number" name="promoCodes[${index}][discount]" placeholder="Discount %" required>
        <select name="promoCodes[${index}][status]" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
        <button type="button" class="remove-code-btn">Remove</button>
    `;
    container.appendChild(row);

    row.querySelector('.remove-code-btn').addEventListener('click', () => {
        row.remove();
    });
});

// Attach remove handlers to existing buttons
document.querySelectorAll('.remove-code-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.closest('.promo-code-row').remove();
    });
});
</script>

</body>
</html>
