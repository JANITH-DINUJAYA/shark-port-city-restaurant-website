<?php
require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$promoId = $_GET['promoId'] ?? '';
$enteredCode = $_GET['code'] ?? '';

header('Content-Type: application/json');

if (!$promoId || !$enteredCode) {
    echo json_encode(['validCode' => null]);
    exit;
}

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

$promo = $database->getReference('promotions/' . $promoId)->getValue();

$validCode = null;

if (!empty($promo['codes'])) {
    foreach ($promo['codes'] as $code) {
        if (isset($code['code']) && strcasecmp(trim($code['code']), trim($enteredCode)) === 0) {
            $validCode = $code;
            break;
        }
    }
}

echo json_encode(['validCode' => $validCode]);
