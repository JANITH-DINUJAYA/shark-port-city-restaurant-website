<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_GET['partnerId'])) {
    echo json_encode(["success" => false, "message" => "Missing partnerId"]);
    exit;
}

$partnerId = $_GET['partnerId'];

require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$db = $factory->createDatabase();
$partner = $db->getReference('deliveryPartners/' . $partnerId)->getValue();

if (!$partner) {
    echo json_encode(["success" => false, "message" => "Partner not found"]);
    exit;
}

echo json_encode([
    "success" => true,
    "lat" => $partner['lat'] ?? null,
    "lon" => $partner['lon'] ?? null,
    "name" => $partner['name'] ?? '',
    "phone" => $partner['phone'] ?? ''
]);
