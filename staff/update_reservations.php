<?php
header('Content-Type: application/json');
require __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$reservationId = $input['reservationId'] ?? null;
$action = $input['action'] ?? null;
$email = $input['email'] ?? null;
$date = $input['date'] ?? null;
$time = $input['time'] ?? null;

if(!$reservationId || !$action || !$email){
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Firebase setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__.'/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');
$database = $factory->createDatabase();

try {
    $reservationsRef = $database->getReference('reservations/' . $reservationId);
    $reservation = $reservationsRef->getValue();

    if(!$reservation){
        echo json_encode(['success'=>false, 'error'=>'Reservation not found']);
        exit;
    }

    if($action == 'rescheduled'){
        if(!$date || !$time){
            echo json_encode(['success'=>false, 'error'=>'Missing date or time for reschedule']);
            exit;
        }
        $reservationsRef->update([
            'date' => $date,
            'time' => $time,
            'status' => 'rescheduled'
        ]);
        $msgAction = "rescheduled to $date $time";
    } else {
        $reservationsRef->update(['status' => $action]);
        $msgAction = $action;
    }

    // Send Email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ddda1235784@gmail.com';
    $mail->Password = 'rsky lofv gjju kqgm';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ddda1235784@gmail.com', 'Shark Port City');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Your Reservation Status";
    $mail->Body = "Hello,<br>Your reservation has been <b>$msgAction</b>.<br>Thank you!";

    $mail->send();

    echo json_encode(['success'=>true, 'message'=>"Reservation $msgAction"]);
} catch(Exception $e){
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
