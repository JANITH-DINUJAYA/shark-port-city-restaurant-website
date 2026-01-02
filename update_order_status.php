<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['orderId']) || empty($data['status'])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$db = $factory->createDatabase();
$db->getReference('orders/' . $data['orderId'])
   ->update(['status' => $data['status']]);

echo json_encode(["success" => true, "message" => "Status updated"]);
