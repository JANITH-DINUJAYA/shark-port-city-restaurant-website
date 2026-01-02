<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$profilePic = $input['profilePic'] ?? '';

if (!$profilePic) {
    echo json_encode(['success' => false, 'message' => 'No image URL provided']);
    exit;
}

$user = $_SESSION['user'];
$partnerId = $user['id'];

try {
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
        ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');
    $database = $factory->createDatabase();

    $database->getReference("partners/$partnerId")->update(['profilePic' => $profilePic]);
    $_SESSION['user']['profilePic'] = $profilePic; // update session too

    echo json_encode(['success' => true]);
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
