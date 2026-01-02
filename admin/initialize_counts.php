<?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

// --- Firebase Setup ---
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();
$firestore = $factory->createFirestore();
$firestoreDb = $firestore->database();

// --- 1. Fetch Users from Firestore ---
$usersSnapshot = $firestoreDb->collection('users')->documents();

$total_members = 0;
$user_counts = ['admin' => 0, 'staff' => 0, 'client' => 0];

foreach ($usersSnapshot as $userDoc) {
    if ($userDoc->exists()) {
        $total_members++;
        $type = $userDoc->data()['userType'] ?? 'client';
        if (isset($user_counts[$type])) {
            $user_counts[$type]++;
        }
    }
}

// --- Save user stats to Realtime DB ---
$database->getReference('stats/users')->set([
    'total_members' => $total_members,
    'admin' => $user_counts['admin'],
    'staff' => $user_counts['staff'],
    'client' => $user_counts['client']
]);

// --- 2. Fetch Orders, Reservations, Promotions, Menu Items, Inquiries from Realtime DB ---
$orders = $database->getReference('orders')->getValue();
$reservations = $database->getReference('reservations')->getValue();
$promotions = $database->getReference('promotions')->getValue();
$menu_items = $database->getReference('menu_items')->getValue();
$inquiries = $database->getReference('inquiries')->getValue();

// --- Save counts to Realtime DB ---
$database->getReference('orders_count')->set(count($orders ?? []));
$database->getReference('reservations_count')->set(count($reservations ?? []));
$database->getReference('promotions_count')->set(count($promotions ?? []));
$database->getReference('menu_items_count')->set(count($menu_items ?? []));
$database->getReference('inquiries_count')->set(count($inquiries ?? []));

echo "All counts initialized successfully!";
