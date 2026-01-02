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
$vehicleNumber = $input['vehicle_number'] ?? '';
$licenseUrl = $input['license'] ?? '';
$insuranceUrl = $input['insurance'] ?? '';
$revenueUrl = $input['revenue'] ?? '';

if (!$licenseUrl || !$insuranceUrl || !$revenueUrl) {
    echo json_encode(['success' => false, 'message' => 'All files are required']);
    exit;
}

$user = $_SESSION['user'];
$partnerId = $user['id'];

try {
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
        ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');
    $database = $factory->createDatabase();

    $vehicleData = [
        'vehicle_number' => $vehicleNumber,
        'license' => $licenseUrl,
        'insurance' => $insuranceUrl,
        'revenue' => $revenueUrl,
        'added_at' => date('Y-m-d H:i:s')
    ];

    $database->getReference("partners/$partnerId/vehicle")->set($vehicleData);

    echo json_encode(['success' => true]);
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
