<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$driverId = $_SESSION['user']['id']; // Firebase key of the logged-in driver

// Firebase orders URL
$ordersUrl = "https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/orders.json";

// Fetch orders from Firebase
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ordersUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$orders = json_decode($response, true);

$assignedOrder = null;

if ($orders) {
    foreach ($orders as $key => $order) {
        // Check if this order is assigned to the logged-in driver
        if (isset($order['assigned_driver']) && $order['assigned_driver'] === $driverId) {
            $assignedOrder = $order;
            $assignedOrder['id'] = $key; // Include order key
            break;
        }
    }
}

if ($assignedOrder) {
    echo json_encode(['success' => true, 'order' => $assignedOrder]);
} else {
    echo json_encode(['success' => false, 'error' => 'No order assigned']);
}
?>
