<?php
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

session_start();

if (!isset($_GET['id'])) {
    die('No promotion ID specified.');
}

$id = $_GET['id'];

// Firebase setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

try {
    // Delete the promotion
    $database->getReference('promotions/' . $id)->remove();

    // Redirect back to promotion management page
    header('Location: promotionmanagement.php');
    exit;
} catch (\Exception $e) {
    echo 'Error deleting promotion: ' . $e->getMessage();
}
?>
