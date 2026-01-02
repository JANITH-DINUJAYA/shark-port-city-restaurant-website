<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo "<script>alert('You must log in first!'); window.location='login.html';</script>";
    exit;
}

$user = $_SESSION['user'];
$partnerId = $user['id'];
$fullName = htmlspecialchars($user['full_name']);
$profilePic = !empty($user['profilePic']) ? $user['profilePic'] : "images/default-avatar.png";
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shark Port City Colombo - My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <img src="../images/Group 17.png" class="logo" alt="Shark Port">
        <nav>
            <ul>
                <li><a href="delivery-history.php">Home</a></li>
                <li><a href="ourlocation.html">Our Location</a></li>
                <li><a href="map.html">Order Map</a></li>
                <li><a href="contac.html">Contact</a></li>
                
                <li><a href="chatbot.html">Support Center</a></li>
                           <div class="ca">
      <a href="setting.php"><i class="fa-solid fa-gear"></i></a></div>
            </ul>
            <a href="login.html" class="accounts-btn">Accounts</a>
        </nav>
    </div>

    <main class="main-content">
        <div class="page-title">
            <h1>Welcome to Your Profile!</h1>
            <p>Manage your account settings and preferences here.</p>
        </div>

        <div class="profile-container">
            <div class="profile-info">
               <div class="profile-upload">
    <img id="profilePic" src="<?= $profilePic ?>" alt="Profile Picture">
    <div class="user-details">
        <h3 id="userNameDisplay"><?= $fullName ?></h3>
    </div>
    <label for="profileImageInput">Choose Image</label>
    <input type="file" id="profileImageInput" accept="image/*">
    <button id="uploadBtn" style="margin-top:10px;">Upload</button>
</div>

</form>

            </div>
            
      <div class="profile-actions">
                <a href="edit-profiles.php" class="profile-button">Edit Profile</a>
                <a href="vehicle.html" class="profile-button">Add Vehicle Details</a>
                <a href="login.html" class="profile-button logout-button">Log Out</a>
            </div>
        </div>
        
      
    </main>

    <footer>
        <div class="footer-container">
            <div class="contact">
                <img src="../images/Group 17.png" class="logo">
                <h2>Shark Port City</h2>
                <p>Email: Info@Greenlife.com</p>
                <p>Phone: +077 344 05 04</p>
                <p>Address: Beach Plaza, Port City, Colombo 01</p>
                <p>© Shark Port city. All rights reserved</p>
            </div>
            <div class="footer-content">
                <ul class="social-icons">
                    <li><a href=""><i class="fab fa-facebook"></i></a></li>
                    <li><a href=""><i class="fab fa-twitter"></i></a></li>
                    <li><a href=""><i class="fab fa-instagram"></i></a></li>
                    <li><a href=""><i class="fab fa-whatsapp"></i></a></li>
                </ul>
            </div>
        </div>
    </footer>

    <style>
     @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

/* Root Colors */
:root {
    --primary-bg-color: #f0f2f5;
    --card-bg-color: #ffffff;
    --text-color: #333;
    --header-bg-color: #0d0d0d;
    --header-text-color: #ffffff;
    --accent-color: #a4e637;
    --button-bg-color: #000;
    --online-status: #34c759;
}

/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
.notification-toggle{
    margin-left: 100px;
}
body {
    background:white;
    color: var(--text-color);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
}
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
  margin-right: 10px;
}

.switch input { display: none; }

.slider {
  position: absolute;
  cursor: pointer;
  background-color: #ccc;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transition: 0.4s;
  border-radius: 24px;
}

.slider::before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 2px;
  bottom: 2px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #f70404;
}

input:checked + .slider::before {
  transform: translateX(26px);
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px 40px;
    background-color: var(--header-bg-color);
    border-radius: 50px;
    margin: 10px 20px;
    position: relative;
    z-index: 10;
}

.logo {
    width: 80px;
    height: auto;
}

nav {
    display: flex;
    align-items: center;
    gap: 40px;
}

nav ul {
    list-style: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
}

nav ul li a {
    color: var(--header-text-color);
    text-decoration: none;
    font-size: 16px;
    font-weight: 400;
    transition: color 0.3s;
}

nav ul li a:hover {
    color: #f70404;
}

/* Accounts Button */
.accounts-btn {
    background-color: #f70404;
    color: #1a362a;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-weight: 600;
    transition: background-color 0.3s;
    white-space: nowrap;
}

.accounts-btn:hover {
    background-color: #f70404;
}

/* Dropdown Menu */
.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #000;
    padding: 10px 0;
    min-width: 200px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.dropdown-content li a {
    display: block;
    padding: 8px 15px;
    color: #fff;
    text-decoration: none;
    transition: background 0.3s;
}

.dropdown:hover .dropdown-content {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-content li a:hover {
    background: #222;
    text-decoration: underline;
    text-decoration-color: var(--accent-color);
    text-decoration-thickness: 3px;
}

/* Cart & Settings Icons */
.ca {
    margin-left: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.ca a {
    color: #fff;
    font-size: 22px;
    transition: color 0.3s ease, transform 0.3s ease;
}

.ca a:hover {
    color: #f70404;
    transform: scale(1.1);
}

/* Main Content */
.main-content {
    flex-grow: 1;
    max-width: 900px;
    margin: 30px auto;
    padding: 0 20px;
}

.page-title {
    text-align: center;
    margin-bottom: 40px;
}

.page-title h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 5px;
}

.page-title p {
    font-size: 1.1rem;
    color: #666;
}

/* Profile Card */
.profile-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px;
    background-color: var(--card-bg-color);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.profile-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 30px;
}
.profile-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px; /* better spacing */
  margin-top: 20px;
  background: #f9f9f9; /* subtle background for the upload area */
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 300px;
}

.profile-upload img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #f70404;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.profile-upload img:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.profile-upload input[type="file"] {
  display: none; /* hide default input */
}

.profile-upload label {
  display: inline-block;
  padding: 10px 20px;
  background-color: #000;
  color: #fff;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.profile-upload label:hover {
  background-color: #f70404;
  color: #ffffffff;
}

.profile-upload button {
  padding: 10px 25px;
  background-color: #000;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.profile-upload button:hover {
  background-color: #f70404;
  color: #ffffffff;
  transform: translateY(-2px);
}
.user-details h3 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #575757;
    position: relative;
    display: inline-block;
}

/* Online Status Dot */
.user-details h3::after {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
    background: var(--online-status);
    border-radius: 50%;
    bottom: 0;
    right: -15px;
    border: 2px solid #fff;
}

.profile-actions {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Buttons */
.profile-button {
    display: block;
    padding: 14px 20px;
    background-color: var(--button-bg-color);
    color: var(--header-text-color);
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.profile-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    transition: 0.5s;
}

.profile-button:hover::before {
    left: 0;
}

.profile-button:hover {
    background-color: #f70404;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.logout-button {
    background-color: #c70000;
}

.logout-button:hover {
    background-color: #ff3333;
    color: white;
}


footer {
    margin-top: auto;
    width: 100%;
    background-color: var(--header-bg-color);
    color: var(--header-text-color);
    padding: 40px 20px;
}

.footer-container {
    max-width: 900px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding-bottom: 10px;
    gap: 30px;
}

.contact {
    text-align: center;
}

.contact h2 {
    font-size: 28px;
    margin-bottom: 15px;
    color: var(--header-text-color);
}

.contact p {
    font-size: 14px;
    color: #ccc;
}

.footer-content {
    text-align: center;
}

.social-icons {
    list-style: none;
    display: flex;
    justify-content: center;
    gap: 20px;
}

.social-icons a {
    color: var(--header-text-color);
    font-size: 25px;
    text-decoration: none;
    transition: color 0.3s ease;
}

.social-icons a:hover {
    color: var(--accent-color);
}

/* Responsive */
@media (max-width: 768px) {
    nav ul {
        gap: 20px;
    }

    .main-content {
        margin: 20px auto;
    }

    .profile-container {
        padding: 30px;
    }

    .page-title h1 {
        font-size: 2rem;
    }
}

@media (max-width: 576px) {
    nav ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }

    .profile-container {
        padding: 20px;
    }

    .profile-button {
        font-size: 14px;
    }

    .profile-actions {
        gap: 10px;
    }
}
    </style>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>

<script>
const profileImageInput = document.getElementById("profileImageInput");
const uploadBtn = document.getElementById("uploadBtn");
const profilePic = document.getElementById("profilePic");

// Replace with your ImgBB API key
const imgbbApiKey = "bbfda5a6eaea6c85b9c3125b4c8cc463";

uploadBtn.addEventListener("click", () => {
    const file = profileImageInput.files[0];
    if (!file) {
        alert("Please choose an image first!");
        return;
    }

    const formData = new FormData();
    formData.append("image", file);

    fetch(`https://api.imgbb.com/1/upload?key=${imgbbApiKey}`, {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            const imageUrl = result.data.url;
            profilePic.src = imageUrl;

            // Update in Firebase via PHP
            fetch('updatte_profile_pic.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ profilePic: imageUrl })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Profile picture updated successfully!");
                } else {
                    alert("Firebase update failed: " + data.message);
                }
            });
        } else {
            alert("Image upload failed!");
        }
    })
    .catch(err => alert("Upload error: " + err.message));
});
</script>



</body>
</html>