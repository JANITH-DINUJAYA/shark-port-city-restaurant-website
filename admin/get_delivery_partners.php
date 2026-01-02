<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

$partners = $database->getReference('partners')->getValue(); // make sure you use 'partners', not 'deliveryPartners'
$activePartners = [];

if($partners) {
    foreach($partners as $id => $info){
        if(isset($info['status']) && $info['status'] === 'Active'){
            $activePartners[] = [
                'id' => $id,
                'name' => $info['full_name']
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($activePartners);
