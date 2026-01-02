<?php
session_start();

require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Get order_id from PayHere return
$order_id = $_GET['order_id'] ?? $_POST['order_id'] ?? null;

if (!$order_id) {
    die("Invalid request - missing order ID.");
}

// Fetch reservation data from Firebase
$reservation = $database->getReference('reservations/' . $order_id)->getValue();

if (!$reservation) {
    die("Reservation not found for this order.");
}

// Generate Confirmation ID (or reuse order_id)
$confirmationID = strtoupper($order_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shark Port City Colombo - Reservation Confirmed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        * { margin:0; font-family: 'Poppins', sans-serif; }
        body { background: #fff; color: #333; margin: 0; width: 100%; }
        .confirmation-container { max-width: 800px; margin: 50px auto; padding: 40px; background-color: #e8f5e9; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        .confirmation-container h1 { color: #2e7d32; margin-bottom: 10px; font-size: 2.5rem; }
        .confirmation-container h2 { color: #43a047; margin-bottom: 25px; }
        .details-box { background-color: #fff; border: 1px solid #c8e6c9; padding: 25px; border-radius: 8px; text-align: left; margin-top: 30px; }
        .details-box p { font-size: 1.1rem; line-height: 1.8; margin: 0; padding: 5px 0; }
        .details-box p strong { display: inline-block; width: 150px; color: #333; }
         .btn-confirm-payment {
            display: block;
            width: 100%;
            padding: 15px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            background-color: rgb(123, 0, 0);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-confirm-payment:hover {
            background-color: #8B0000;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h1><i class="fa-solid fa-circle-check" style="color: #4CAF50;"></i> Reservation Confirmed!</h1>
        <h2>Thank you for your reservation. We look forward to seeing you.</h2>
      <div class="details-box">
    <p><strong>Confirmation ID:</strong> <?= htmlspecialchars($confirmationID) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($reservation['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($reservation['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($reservation['phone']) ?></p>
    <p><strong>Table Type:</strong> <?= htmlspecialchars($reservation['tableType']) ?></p>
    <p><strong>Date & Time:</strong> <?= htmlspecialchars($reservation['date'] . ' at ' . $reservation['time']) ?></p>
    <p><strong>Number of Guests:</strong> <?= htmlspecialchars($reservation['guests']) ?></p>
    <p><strong>Amount:</strong> <?= htmlspecialchars($reservation['amount'] . ' ' . $reservation['currency']) ?></p>
</div>


        <p style="margin-top: 25px; color: #555;">A confirmation email has been sent to your email address with all the details.</p>
          <a href="index.php" class="btn-confirm-payment" id="confirmBtn">Go Back To Homepage</a>
    </div>
</body>
</html>
