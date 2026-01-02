<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$partnerId = $_SESSION['user']['id'];

require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

try {
    $partner = $database->getReference("partners/$partnerId")->getValue();

    if (!$partner || !isset($partner['lat'], $partner['lon'])) {
        echo json_encode(['success' => false, 'message' => 'Location not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'lat' => $partner['lat'],
        'lon' => $partner['lon'],
        'status' => $partner['status'] ?? 'Unknown'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
