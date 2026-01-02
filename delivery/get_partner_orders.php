<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$partnerId = $_SESSION['user']['id']; // saved during login

require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

$orders = $database->getReference("orders")->getValue();
$assignedOrders = [];

if ($orders) {
    foreach ($orders as $id => $order) {
        if (isset($order['partnerId']) && $order['partnerId'] == $partnerId) {
 $assignedOrders[] = [
    "id" => $id,
    "customer" => [
        "name" => $order['customer']['name'] ?? "Unknown",
        "phone" => $order['customer']['phone'] ?? "",
        "lat" => !empty($order['customer']['address']['lat']) ? floatval($order['customer']['address']['lat']) : null,
        "lon" => !empty($order['customer']['address']['lon']) ? floatval($order['customer']['address']['lon']) : null,
        "address" => $order['customer']['address'] ?? "",
    ],
    "partnerId" => $order['partnerId'] ?? null,
    "items" => $order['items'] ?? [],
    "total" => $order['total'] ?? 0,
    "status" => $order['status'] ?? ""
];


        }
    }
}

header('Content-Type: application/json');
echo json_encode($assignedOrders);
