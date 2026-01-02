<?php
// payhere_notify.php - Put this at the notify_url you provided in the form.
// This file should be publicly reachable and should NOT depend on a browser session.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$merchant_id      = $_POST['merchant_id'] ?? '';
$order_id         = $_POST['order_id'] ?? '';
$payhere_amount   = $_POST['payhere_amount'] ?? '';
$payhere_currency = $_POST['payhere_currency'] ?? '';
$status_code      = $_POST['status_code'] ?? '';
$md5sig           = $_POST['md5sig'] ?? '';

// Your merchant secret (keep this safe & server-side)
$merchant_secret = ' MTI4NDI0NTQxNjMzNzA3ODA2MDMzNDQyMzk4NDM0MTg4NTAwMjE2Mg=='; // Replace with your merchant secret

$local_md5sig = strtoupper(
    md5(
        $merchant_id .
        $order_id .
        $payhere_amount .
        $payhere_currency .
        $status_code .
        strtoupper(md5($merchant_secret))
    )
);

// Debug logging (optional) - helpful while testing (write to file; do not leak secrets)
file_put_contents(__DIR__.'/payhere_notify.log', date('c') . " | Received notify: status={$status_code}, md5sig={$md5sig}\n", FILE_APPEND);

if ($local_md5sig === $md5sig && $status_code == '2') {
    // Payment verified & successful.
    // TODO: update your DB order record: mark order_id as paid, store payment_id, amount etc.
    // Example: save $_POST['payment_id'] and other params.
    // IMPORTANT: only treat as successful after verification.
    http_response_code(200);
    // Respond nothing / or 200. (PayHere doesn't expect HTML).
    exit;
} else {
    // Verification failed or payment not successful
    http_response_code(400);
    exit;
}
