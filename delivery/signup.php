<?php
$firebase_url = "https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/partners.json";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $data = [
        "full_name" => $name,
        "email" => $email,
        "password" => $hashed_password,
        "status" => "Inactive", // Default status for every new partner
        "lat" => 0,           
        "lon" => 0 
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebase_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        echo "<script>alert('Signup successful! Please login.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Signup failed. Try again.'); window.location='signup.html';</script>";
    }
}
?>
