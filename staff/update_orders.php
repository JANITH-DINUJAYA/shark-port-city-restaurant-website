<?php
error_reporting(E_ERROR | E_PARSE);

require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Firebase connection
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$orderId = $data['orderId'] ?? null;
$action  = $data['action'] ?? null;

if (!$orderId || !$action) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

try {
    $orderRef = $database->getReference("orders/$orderId");
    $order = $orderRef->getValue();

    if (!$order) throw new Exception("Order not found");

    if ($action === "cancel") {
        // Update order status
        $orderRef->getChild('status')->set("Cancelled");

        // Send email to customer
        $customer = $order['customer'] ?? [];
        $customerEmail = $customer['email'] ?? null;
        $customerName  = $customer['name'] ?? "Customer";

        if ($customerEmail) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = "smtp.gmail.com";
                $mail->SMTPAuth   = true;
                $mail->Username   = "ddda1235784@gmail.com";
                $mail->Password   = "rsky lofv gjju kqgm";
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom("ddda1235784@gmail.com", "Shark Port City");
                $mail->addAddress($customerEmail, $customerName);

                $mail->isHTML(true);
                $mail->Subject = "Order #$orderId Cancelled";
                $mail->Body    = "
                    <p>Dear {$customerName},</p>
                    <p>We regret to inform you that your order <strong>#$orderId</strong> has been <span style='color:red;font-weight:bold;'>cancelled</span>.</p>
                    <p>If you have any questions, please contact our support team.</p>
                    <br>
                    <p>Best regards,<br>Shark Port City Team</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }
        }

    } elseif ($action === "assign") {
    $deliveryPartnerId = $data['deliveryPartnerId'] ?? null;
    if (!$deliveryPartnerId) throw new Exception("Delivery Partner ID is required");

    // Update order
    $orderRef->getChild('status')->set("Assigned to Delivery");
    
    // Set partnerId (so driver panel can fetch it)
    $orderRef->getChild('partnerId')->set($deliveryPartnerId);

    // Optionally, store partner name/email in order
    $partner = $database->getReference("partners/$deliveryPartnerId")->getValue();
    if ($partner) {
        $orderRef->getChild('partnerName')->set($partner['full_name'] ?? "");
        $orderRef->getChild('partnerEmail')->set($partner['email'] ?? "");
    }

    // Update partner status to Busy
    $database->getReference("partners/$deliveryPartnerId/status")->set("Busy");


    } elseif ($action === "complete") {
        // Mark order as completed
        $orderRef->getChild('status')->set("Completed");

        // Free the assigned partner if any
        $assignedPartner = $order['assignedTo'] ?? null;
        if ($assignedPartner) {
            $database->getReference("partners/$assignedPartner/status")->set("Active");
        }
    } else {
        throw new Exception("Unknown action: $action");
    }

    echo json_encode(["success" => true, "message" => "Order updated successfully"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
