<?php
// Firebase Realtime Database URL
$databaseURL = "https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/";

// Inquiries
$inquiries = file_get_contents("$databaseURL/inquiries.json");
$feedbacks = file_get_contents("$databaseURL/feedbacks.json");

if ($inquiries === FALSE || $feedbacks === FALSE) {
    echo json_encode(["error" => "Unable to fetch data"]);
    exit;
}

header("Content-Type: application/json");
echo json_encode([
    "inquiries" => json_decode($inquiries, true),
    "feedbacks" => json_decode($feedbacks, true)
]);
