<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Firestore;
use Google\Cloud\Firestore\FirestoreClient;
// Initialize Firebase
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app');

$auth = $factory->createAuth();          // For managing users
$firestore = $factory->createFirestore(); // For Firestore DB
$db = $firestore->database();

class FirebaseConfig {
    public static function getDatabase() {
        $factory = (new Factory)
            ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
            ->withDatabaseUri('https://your-project-id.firebaseio.com/');
        return $factory->createDatabase();
    }
}
// Example: Get a user by UID
try {
    $user = $auth->getUser('USER_UID');
    echo "User email: ".$user->email;
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    echo "User not found";
}
