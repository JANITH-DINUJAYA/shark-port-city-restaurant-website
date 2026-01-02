<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // PHPMailer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = $_POST['subject'] ?? '';
    $message = $_POST['description'] ?? '';
    $attachment = $_FILES['attachment'] ?? null;

    // --- 1. Fetch emails from Firebase Realtime Database ---
    $firebaseUrl = 'https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/notifications.json';
    $jsonData = file_get_contents($firebaseUrl); // GET request
    $data = json_decode($jsonData, true);

    if (!$data) {
        die("No users found in Firebase.");
    }

    // Collect emails
    $emails = [];
    foreach ($data as $userId => $info) {
        if (isset($info['email'])) {
            $emails[] = $info['email'];
        }
    }

    if (empty($emails)) {
        die("No emails found in Firebase notifications.");
    }

    // --- 2. Setup PHPMailer ---
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ddda1235784@gmail.com';
        $mail->Password   = 'rsky lofv gjju kqgm'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('ddda1235784@gmail.com', 'Shark Port City');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Attachment
        if ($attachment && $attachment['error'] === UPLOAD_ERR_OK) {
            $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
        }

        // --- 3. Send email to all users ---
        foreach ($emails as $email) {
            $mail->addAddress($email);
            $mail->send();
            $mail->clearAddresses();
        }

        echo  "<script>alert('✅ Emails sent successfully to all subscribers!'); window.location.href = window.location.href;</script>";
    } catch (Exception $e) {
       echo "<script>alert('❌ Email could not be sent. Mailer Error: {$error}'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email to Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
     :root {
            --primary-dark: #2c3e50;
            --secondary-light: #f4f7f6;
            --text-color: #333;
            --white: #ffffff;
            --accent-green: #2ecc71;
            --online-status: #34c759;
        }

        body {
            background-color: var(--secondary-light);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        nav {
            background-color: #000;
            width: 350px;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            left: 0;
            height: 100vh;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            width: 150px;
        }

        .profile-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            color: var(--white);
        }

        .online-indicator {
            width: 10px;
            height: 10px;
            background-color: var(--online-status);
            border-radius: 50%;
            animation: pulse-online 1.5s infinite;
        }

        @keyframes pulse-online {
            0% { box-shadow: 0 0 0 0 rgba(52,199,89,0.7); }
            70% { box-shadow: 0 0 0 10px rgba(52,199,89,0); }
            100% { box-shadow: 0 0 0 0 rgba(52,199,89,0); }
        }

        nav ul {
            list-style: none;
            padding: 0;
            width: 100%;
            margin: 0;
        }

        nav ul li {
            position: relative;
        }

        nav ul li a {
            display: block;
            padding: 20px 30px;
            color: var(--white);
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        nav ul li a:hover {
            background-color: rgba(255,255,255,0.1);
            padding-left: 40px;
            text-decoration: underline solid #f70404;
            text-decoration-thickness: 3px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #000;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 100;
            border-radius: 0 0 8px 8px;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content li a:hover {
            background-color: #1a1a1a;
            padding-left: 45px;
        }

        .main-content {
            flex-grow: 1;
            padding: 50px;
             /* offset sidebar */
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        .container {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px;
        }

        h2 {
            color: var(--primary-dark);
            margin-bottom: 30px;
            text-align: center;
        }

        .form-control:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(46,204,113,0.2);
        }

        .send-button {
            background-color: #f70404;
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .send-button:hover {
            background-color: #f70404;
        }

        .form-text {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
  <nav>
            <div class="logo">
                <img src="../images/Group 17.png" alt="Shark Port City Logo" />
            </div>
            <div class="profile-status">
                <span class="online-indicator"></span>
                Online
            </div>
            <ul>
                <li><a href="home.php">Dashboard</a></li>
                <li class="dropdown">
                    <a href="#">User Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="customer.html">Customer</a></li>
                        <li><a href="partner.html">Delivery Partners</a></li>
                        <li><a href="staff.html">Staff</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Content Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="banners.html">Banners</a></li>
                        <li><a href="photogallery.html">Photo Galleries</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Dish Management ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="Menumanage.php">Menus</a></li>
                        <li><a href="promotionmanagement.php">Promotions</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#">Order & Reservation Management▾</a>
                    <ul class="dropdown-content">
                        <li><a href="ordermanage.html">Orders</a></li>
                        <li><a href="reservationmanage.html">Reservations</a></li>
                    </ul>
                </li>
                <li><a href="inquiries.html">Inquiries</a></li>
                 <li><a href="send-email.php">Notifications</a></li> 
                <li><a href="login.html">Log Out</a></li>
            </ul>
        </nav>
 <div class="main-content">
        <div class="container">
            <h2>Send Email to Users</h2>
            <form action="send-email.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter email subject" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description / Message</label>
                    <textarea class="form-control" id="description" name="description" rows="6" placeholder="Enter your message" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="attachment" class="form-label">Attachment (Optional)</label>
                    <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx">
                    <div class="form-text">Supported formats: images, PDF, DOC</div>
                </div>

                <button type="submit" class="send-button">Send Email</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
