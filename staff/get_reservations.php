<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

header('Content-Type: application/json');

$factory = (new Factory)
    ->withServiceAccount(__DIR__.'/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

try {
    $snapshot = $database->getReference('reservations')->getValue(); // returns array or null
    if (!$snapshot) $snapshot = [];

    echo json_encode($snapshot); // directly return object with keys
} catch (\Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
