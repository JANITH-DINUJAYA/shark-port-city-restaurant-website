<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

session_start();

// Firebase RTDB setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? 0;
    $ingredients = $_POST['ingredients'] ?? '';

    // Upload to ImgBB
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $imageData = base64_encode(file_get_contents($fileTmpPath));

        $apiKey = 'bbfda5a6eaea6c85b9c3125b4c8cc463'; // replace with valid key

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
    } else {
        die('No image uploaded.');
    }

    // Push new promotion to Firebase
    $newPromotion = [
        'title' => $title,
        'price' => floatval($price),
        'ingredients' => $ingredients,
        'image' => $imageUrl,
        'created_at' => time()
    ];

    $ref = $database->getReference('promotions')->push($newPromotion);

    if ($ref) {
        header('Location: promotionmanagement.php'); // redirect back to promotion page
        exit;
    } else {
        die('Failed to add promotion.');
    }
}
?>
