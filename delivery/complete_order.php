<?php
header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate partnerId
if (!isset($data['partnerId']) || empty($data['partnerId'])) {
    echo json_encode(['success' => false, 'message' => 'Partner ID missing or invalid']);
    exit;
}

$partnerId = $data['partnerId'];

// Load Firebase
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

try {
    // Get orders assigned to this partner
    $ordersRef = $database->getReference('orders')
                         ->orderByChild('partnerId')
                         ->equalTo($partnerId)
                         ->getValue();

    if (!$ordersRef) {
        echo json_encode(['success' => false, 'message' => 'No active orders found for this partner']);
        exit;
    }

    $updatedOrder = null;

    // Update only the first active order
    foreach ($ordersRef as $key => $order) {
        if (!isset($order['status']) || strtolower($order['status']) !== 'completed') {
            // Mark order as completed
            $database->getReference('orders/'.$key)
                     ->update([
                         'status' => 'completed',
                         'partnerId' => null
                     ]);

            // ✅ Reset partner status to Active
            $database->getReference('partners/'.$partnerId.'/status')->set("Active");

            $updatedOrder = [
                'orderId' => $key,
                'customer' => $order['customer'] ?? null
            ];
            break; // Stop after updating the first active order
        }
    }

    if ($updatedOrder) {
        echo json_encode(['success' => true, 'message' => 'Order marked as completed', 'order' => $updatedOrder]);
    } else {
        echo json_encode(['success' => false, 'message' => 'All orders already completed']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Firebase error: '.$e->getMessage()]);
}
