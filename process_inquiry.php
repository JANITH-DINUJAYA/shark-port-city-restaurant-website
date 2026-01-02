<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

session_start();

// Path to Firebase service account
$serviceAccountPath = __DIR__ . '/firebase_service_account.json';

if (!file_exists($serviceAccountPath)) {
    die("Error: Firebase service account key not found at " . $serviceAccountPath);
}

try {
    $factory = (new Factory)
        ->withServiceAccount($serviceAccountPath)
        ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app');

    // ✅ Get Realtime Database directly
    $database = $factory->createDatabase();

} catch (Exception $e) {
    die("Firebase initialization error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($name && $email && $message) {
        $inquiryData = [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'timestamp' => (new DateTime())->format('c')
        ];

        header('Content-Type: application/json');
try {
    $database->getReference('inquiries')->push($inquiryData);
    echo json_encode(['status' => 'success', 'message' => 'Your inquiry has been sent successfully 🚀']);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: '.$e->getMessage()]);
}
exit;

}
}