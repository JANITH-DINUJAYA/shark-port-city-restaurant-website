<?php
session_start();

// Firebase Realtime Database URL (delivery node)
$firebase_url = "https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/partners.json";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch delivery users
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebase_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode JSON
    $data = json_decode($response, true);

    if (!$data) {
        echo "<script>alert('No data found in Firebase.'); window.location='login.html';</script>";
        exit;
    }

    $userFound = false;

    foreach ($data as $key => $user) {
        if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
            // Compare hashed password
            if (password_verify($password, $user['password'])) {
                $userFound = true;
                 $user['id'] = $key; 
                $_SESSION['user'] = $user;
                echo "<script>alert('Login successful!'); window.location='delivery-history.php';</script>";
                exit;
            }
        }
    }

    if (!$userFound) {
        echo "<script>alert('Invalid email or password.'); window.location='login.html';</script>";
    }
}
?>
