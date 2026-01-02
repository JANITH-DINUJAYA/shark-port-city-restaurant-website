<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photoFile'])) {
    $apiKey = 'bbfda5a6eaea6c85b9c3125b4c8cc463'; // ImgBB API Key
    $firebaseUrl = 'https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/photos.json'; // Correct Firebase URL with .json

    $photo = $_FILES['photoFile']['tmp_name'];
    $caption = $_POST['photoCaption'] ?? '';

    if (!file_exists($photo)) {
        echo json_encode(['success' => false, 'message' => 'Temporary file not found']);
        exit;
    }

    // Encode the image for ImgBB
    $imageData = base64_encode(file_get_contents($photo));

    // Upload to ImgBB
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.imgbb.com/1/upload?key=$apiKey");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'image' => $imageData,
        'name' => pathinfo($_FILES['photoFile']['name'], PATHINFO_FILENAME)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        echo json_encode(['success' => false, 'message' => 'Curl error (ImgBB): ' . curl_error($ch)]);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (!$data || !isset($data['data']['url'])) {
        $errorMsg = $data['error']['message'] ?? 'ImgBB upload failed';
        echo json_encode(['success' => false, 'message' => $errorMsg]);
        exit;
    }

    $imgUrl = $data['data']['url'];

    // Save to Firebase
    $firebaseData = json_encode([
        'url' => $imgUrl,
        'caption' => $caption,
        'timestamp' => time()
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_POST, 1); // POST creates a new unique node
    curl_setopt($ch, CURLOPT_POSTFIELDS, $firebaseData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Keep SSL verification enabled
    $firebaseResponse = curl_exec($ch);

    if(curl_errno($ch)) {
        echo json_encode(['success' => false, 'message' => 'Firebase curl error: ' . curl_error($ch)]);
        curl_close($ch);
        exit;
    }

    $firebaseResult = json_decode($firebaseResponse, true);
    curl_close($ch);

    if ($firebaseResult && isset($firebaseResult['name'])) {
        // Firebase returns a unique key in 'name'
        echo json_encode(['success' => true, 'url' => $imgUrl, 'caption' => $caption]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save image to Firebase. Response: ' . $firebaseResponse]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}