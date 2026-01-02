<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['lat']) || !isset($input['lon'])) {
    echo json_encode(['success' => false, 'message' => 'Missing coordinates']);
    exit;
}

$partnerId = $_SESSION['user']['id'];
$lat = floatval($input['lat']);
$lon = floatval($input['lon']);

require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Update partner location
$database->getReference("partners/$partnerId")->update([
    'lat' => $lat,
    'lon' => $lon,
    'timestamp' => time()
]);

echo json_encode(['success' => true]);
