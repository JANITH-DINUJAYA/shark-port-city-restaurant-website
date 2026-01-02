<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_GET['orderId'])) {
    echo json_encode(["success" => false, "message" => "Missing orderId"]);
    exit;
}

$orderId = $_GET['orderId'];

require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$db = $factory->createDatabase();
$order = $db->getReference('orders/' . $orderId)->getValue();

if (!$order) {
    echo json_encode(["success" => false, "message" => "Order not found"]);
    exit;
}

// partnerId is stored directly under the order
$partnerId = $order['partnerId'] ?? null;
$partner = null;

if ($partnerId) {
    $partner = $db->getReference('deliveryPartners/' . $partnerId)->getValue();
}

echo json_encode([
    "success" => true,
    "status" => $order['status'] ?? 'Unknown',
    "customer" => $order['customer'] ?? null,
    "partner" => $partner,
    "partnerId" => $partnerId
]);
