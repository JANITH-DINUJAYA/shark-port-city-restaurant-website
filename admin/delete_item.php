<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

session_start();

if (!isset($_GET['id'])) {
    die('No ID specified.');
}

$id = $_GET['id'];

// Firebase setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

try {
    $database->getReference('menu_items/' . $id)->remove(); // delete the item
    header('Location: Menumanage.php'); // redirect back to menu management page
    exit;
} catch (\Exception $e) {
    echo 'Error deleting item: ' . $e->getMessage();
}
