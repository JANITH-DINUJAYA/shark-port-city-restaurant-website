<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_GET['customerId'])) {
    echo json_encode(["success" => false, "message" => "Missing customerId"]);
    exit;
}

$customerId = $_GET['customerId'];

require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$db = $factory->createDatabase();
$customer = $db->getReference('customers/' . $customerId)->getValue();

if (!$customer) {
    echo json_encode(["success" => false, "message" => "Customer not found"]);
    exit;
}

echo json_encode([
    "success" => true,
    "lat" => $customer['location']['lat'] ?? null,
    "lon" => $customer['location']['lon'] ?? null
]);
