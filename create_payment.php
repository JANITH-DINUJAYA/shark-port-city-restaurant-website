<?php
session_start();

// CONFIG (replace with your sandbox Merchant ID & Merchant Secret)
$merchant_id     = '1232170';    
$merchant_secret = 'MTI4NDI0NTQxNjMzNzA3ODA2MDMzNDQyMzk4NDM0MTg4NTAwMjE2Mg=='; 

// Order details
$order_id = $_POST['order_id'] ?? 'ORDER-' . time();
$amount   = $_POST['amount'] ?? 0;
$currency = 'USD';
$amount_formatted = number_format($amount, 2, '.', '');

// Generate PayHere hash
$hash = strtoupper(
    md5(
        $merchant_id .
        $order_id .
        $amount_formatted .
        $currency .
        strtoupper(md5($merchant_secret))
    )
);

// URLs (update with your domain)
$return_url   = 'https://capitular-jocelynn-hamulate.ngrok-free.dev/Shark_port_city/confirmreservation.php';
$cancel_url   = 'https://capitular-jocelynn-hamulate.ngrok-free.dev/Shark_port_city/reservation.html';
$notify_url   = 'https://capitular-jocelynn-hamulate.ngrok-free.dev/Shark_port_city/payhere_notify.php';

// Get reservation details from session
$reservation = $_SESSION['reservation_details'] ?? [];
require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

// Initialize Firebase
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json') // <-- update this
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/'); // <-- update this

$database = $factory->createDatabase();

// Prepare data to save
$reservation_data = [
    'order_id' => $order_id,
    'name' => $reservation['name'] ?? 'Guest',
    'email' => $reservation['email'] ?? '',
    'phone' => $reservation['phone'] ?? '',
    'tableType' => $reservation['tableType'] ?? '',
    'date' => $reservation['date'] ?? '',
    'time' => $reservation['time'] ?? '',
    'guests' => $reservation['guests'] ?? 1,
    'amount' => $amount_formatted,
    'currency' => $currency,
    'status' => 'pending', // you can update this after payment
    'created_at' => date('Y-m-d H:i:s')
];

// Save to Firebase under a node named by order_id
$database->getReference('reservations/' . $order_id)
         ->set($reservation_data);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shark Port City - PayHere Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <style>
        body { background:#f8f9fa; font-family:'Poppins', sans-serif; }
        .payment-summary-container {
            max-width: 600px; margin:50px auto; padding:40px;
            background-color:#fff; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }
        h1 { text-align:center; margin-bottom:25px; color:#333; }
        .summary-item { display:flex; justify-content:space-between; padding:8px 0; font-size:1.1rem; }
        .total-price { font-size:1.5rem; font-weight:bold; color:rgb(123,0,0); }
        .btn-payhere {
            display:block; width:100%; padding:15px;
            background:rgb(123,0,0); color:#fff; font-size:1.2rem;
            border:none; border-radius:5px; font-weight:bold;
            cursor:pointer; transition:0.3s;
        }
        .btn-payhere:hover { background:#8B0000; }
    </style>
</head>
<body>
<div class="payment-summary-container">
    <h1>Confirm & Pay</h1>

    <div class="summary-details">
        <div class="summary-item"><span>Name:</span><span><?= htmlspecialchars($reservation['name'] ?? 'Guest') ?></span></div>
        <div class="summary-item"><span>Email:</span><span><?= htmlspecialchars($reservation['email'] ?? 'test@example.com') ?></span></div>
        <div class="summary-item"><span>Date & Time:</span><span><?= htmlspecialchars(($reservation['date'] ?? '') . ' ' . ($reservation['time'] ?? '')) ?></span></div>
        <div class="summary-item"><span>Guests:</span><span><?= htmlspecialchars($reservation['guests'] ?? '1') ?></span></div>
        <div class="summary-item"><span>Table Type:</span><span><?= htmlspecialchars($reservation['tableType'] ?? 'Standard') ?></span></div>
    </div>

    <hr>

    <div class="payment-details">
        <div class="summary-item"><span>Amount (USD):</span><span>$<?= number_format($amount, 2) ?></span></div>
        <div class="summary-item total-price"><span>Total:</span><span>$<?= number_format($amount, 2) ?></span></div>
    </div>

    <form method="post" action="https://sandbox.payhere.lk/pay/checkout">
        <!-- Required PayHere fields -->
        <input type="hidden" name="merchant_id" value="<?= htmlspecialchars($merchant_id) ?>">
        <input type="hidden" name="return_url" value="<?= htmlspecialchars($return_url) ?>">
        <input type="hidden" name="cancel_url" value="<?= htmlspecialchars($cancel_url) ?>">
        <input type="hidden" name="notify_url" value="<?= htmlspecialchars($notify_url) ?>">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
        <input type="hidden" name="items" value="Reservation Payment">
        <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
        <input type="hidden" name="amount" value="<?= htmlspecialchars($amount_formatted) ?>">
        <input type="hidden" name="hash" value="<?= $hash ?>">

        <!-- Customer details -->
        <input type="hidden" name="first_name" value="<?= htmlspecialchars($reservation['name'] ?? 'Guest') ?>">
        <input type="hidden" name="last_name" value="Customer">
        <input type="hidden" name="email" value="<?= htmlspecialchars($reservation['email'] ?? 'test@example.com') ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($reservation['phone'] ?? '0771234567') ?>">
        <input type="hidden" name="address" value="No.1, Galle Road">
        <input type="hidden" name="city" value="Colombo">
        <input type="hidden" name="country" value="Sri Lanka">

        <button type="submit" class="btn-payhere">Pay with PayHere</button>
    </form>
</div>
</body>
</html>
