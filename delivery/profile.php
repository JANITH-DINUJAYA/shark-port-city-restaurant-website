<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

if (!isset($_SESSION['user'])) {
    echo "<script>alert('You must log in first!'); window.location='login.html';</script>";
    exit;
}

$user = $_SESSION['user'];
$partnerId = $user['id'];

$factory = (new Factory)
    ->withServiceAccount(__DIR__.'/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

$response = ["success" => false, "message" => "Unknown error"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'updateName':
                $newName = $_POST['fullName'] ?? '';
                if ($newName) {
                    $database->getReference("partners/$partnerId")->update(['full_name' => $newName]);
                    $_SESSION['user']['full_name'] = $newName;
                    $response = ["success" => true, "message" => "Name updated"];
                }
                break;

            case 'updateEmail':
                $newEmail = $_POST['email'] ?? '';
                if ($newEmail) {
                    $database->getReference("partners/$partnerId")->update(['email' => $newEmail]);
                    $_SESSION['user']['email'] = $newEmail;
                    $response = ["success" => true, "message" => "Email updated"];
                }
                break;

            case 'updatePassword':
                $newPassword = $_POST['password'] ?? '';
                if ($newPassword) {
                    $database->getReference("partners/$partnerId")->update(['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
                    $response = ["success" => true, "message" => "Password updated"];
                }
                break;

            case 'deleteAccount':
                $database->getReference("partners/$partnerId")->remove();
                session_destroy();
                $response = ["success" => true, "message" => "Account deleted"];
                echo "<script>alert('Account deleted!'); window.location='login.html';</script>";
                exit;
        }
    } catch (\Exception $e) {
        $response = ["success" => false, "message" => $e->getMessage()];
    }

    echo "<script>alert('{$response['message']}'); window.location='edit-profiles.php';</script>";
}
?>
