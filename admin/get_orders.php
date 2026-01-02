<?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

// Firebase connection
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Get all orders
$orders = $database->getReference('orders')->getValue();

if (!$orders) {
    echo "<tr><td colspan='8'>No orders found</td></tr>";
    exit;
}

foreach ($orders as $orderId => $order) {
    $customer = $order['customer'] ?? [];
    $status = $order['status'] ?? 'Unknown';
    $statusClass = strtolower($status);

    echo "<tr>";
    echo "<td>#{$orderId}</td>";
    echo "<td>{$customer['name']}</td>";
    echo "<td>{$customer['phone']}</td>";

    // Items list
    if (!empty($order['items'])) {
        $itemsText = [];
        foreach ($order['items'] as $itemName => $itemDetails) {
            $qty = $itemDetails['quantity'] ?? 1;
            $itemsText[] = "{$qty}x {$itemName}";
        }
        echo "<td>" . implode(", ", $itemsText) . "</td>";
    } else {
        echo "<td>-</td>";
    }

    echo "<td>LKR {$order['total']}</td>";
    echo "<td><span class='status {$statusClass}'>{$status}</span></td>";

    // Actions
    $cancelBtn = ($status == "Processing" || $status == "Pending") 
        ? "<button class='cancel-btn' data-id='{$orderId}'>Cancel</button>" 
        : "<button class='cancel-btn' disabled>Cancel</button>";

    echo "<td>
            {$cancelBtn}
            <button class='view-btn' data-id='{$orderId}'>View Details</button>
          </td>";

    // Assign Delivery (only if pending/processing)
    if ($status == "Processing" || $status == "Pending") {
        echo "<td><button class='assign-btn' data-id='{$orderId}'>Assign Delivery</button></td>";
    } else {
        echo "<td>-</td>";
    }

    echo "</tr>";
}
