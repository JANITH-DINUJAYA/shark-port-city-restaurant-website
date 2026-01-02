<?php
header('Content-Type: application/json');
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['type']) || !isset($input['id']) || !isset($input['response'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$type = $input['type']; // "inquiry" or "feedback"
$id = $input['id'];
$responseMessage = $input['response'];

try {
    // Initialize Firebase
    $factory = (new Factory)->withServiceAccount(__DIR__.'/../firebase_service_account.json')
                             ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');
    $database = $factory->createDatabase();

    // Determine collection
    $collection = ($type === 'feedback') ? 'feedbacks' : 'inquiries';
    $ref = $database->getReference("{$collection}/{$id}");
    $data = $ref->getValue();

    if (!$data || !isset($data['email'])) {
        echo json_encode(['success' => false, 'error' => 'User email not found']);
        exit;
    }

    $userEmail = $data['email'];

    // Save response in Firebase
    $ref->getChild('response')->set([
        'message' => $responseMessage,
        'respondedAt' => date('c')
    ]);

    // Send email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ddda1235784@gmail.com'; // your Gmail
    $mail->Password = 'rsky lofv gjju kqgm';   // 16-char App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ddda1235784@gmail.com', 'Shark Port City');
    $mail->addAddress($userEmail);
    $mail->isHTML(true);

    // Email subject and body
    if ($type === 'feedback') {
        $mail->Subject = "Response to your feedback";
        $mail->Body    = "<p>Hello,</p><p>Here is the response to your feedback:</p><p><b>$responseMessage</b></p>";
    } else {
        $mail->Subject = "Response to your inquiry: " . ($data['subject'] ?? 'No Subject');
        $mail->Body    = "<p>Hello,</p><p>Here is the response to your inquiry:</p><p><b>$responseMessage</b></p>";
    }

    $mail->send();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
// Save response in Firebase
$ref->getChild('response')->set([
    'message' => $responseMessage,
    'respondedAt' => date('c')
]);

// Mark as responded
$ref->getChild('responded')->set(true);
