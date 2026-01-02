<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "<script>alert('You must log in first!'); window.location='login.html';</script>";
    exit;
}

// Get logged-in user data
$user = $_SESSION['user'];
$partnerId = $user['id'];
$fullName = htmlspecialchars($user['full_name']);
$email = htmlspecialchars($user['email']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shark Port City Colombo - Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>

    <style>
        /* Your existing CSS styles go here */
        * { margin: 0; font-family: 'Poppins', sans-serif; }
        body { background: #fff; color: #333; width: 100%; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 40px;
            background-color: #000000; 
            border-radius: 50px;
            margin: 10px 20px;
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
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #f70404;
        }

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

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: black;
            padding: 10px 0;
            min-width: 200px;
            z-index: 1000;
        }

        .dropdown-content li {
            list-style: none;
            margin: 0;
        }

        .dropdown-content li a {
            display: block;
            padding: 8px 15px;
            color: white;
            text-decoration: none;
        }

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

        .dropdown-content li a:hover {
            background: #222;
            text-decoration: underline;
            text-decoration-color: greenyellow;
            text-decoration-thickness: 3px;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        footer { width: 100%; background-color: black; margin-top: 50px; }
        .container { display: flex; flex-direction: column; justify-content: center; align-items: center; padding-bottom: 10px; color: #fff; gap: 30px; }
        .contact { text-align: center; }
        .contact h2 { font-size: 28px; margin-bottom: 15px; color: white; }
        .footer-content { text-align: center; }
        .social-icons { list-style: none; display: flex; justify-content: center; gap: 20px; }
        .social-icons a { color: white; font-size: 25px; text-decoration: none; }
        .social-icons a:hover { color: #ccc; }

        .edit-profile-container { max-width: 600px; margin: 50px auto; padding: 30px; background-color: #f8f8f8; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .edit-profile-container h1 { text-align: center; margin-bottom: 30px; font-size: 2.5rem; font-weight: 700; }
        .profile-section { background-color: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .profile-section h2 { font-size: 1.5rem; font-weight: 600; margin-bottom: 15px; color: rgb(123,0,0); border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn-update { padding: 10px 25px; font-weight: bold; color: white; background-color: rgb(123,0,0); border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-update:hover { background-color: #8B0000; }
        .btn-delete { background-color: #dc3545; margin-top: 20px; }
        .btn-delete:hover { background-color: #c82333; }
        
        /* New style for the reauth modal */
        #reauth-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1001;
            display: none; /* Initially hidden */
        }
        #reauth-modal-content {
            background:#fff;
            padding:25px;
            border-radius:10px;
            text-align:center;
        }
    </style>
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


 <main class="edit-profile-container">

    <h1>Edit Profile</h1>

    <!-- Change Name -->
    <div class="profile-section">
        <h2>Change Name</h2>
        <form method="post" action="profile.php">
            <input type="hidden" name="action" value="updateName">
            <div class="form-group">
                <label for="fullName">New Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?= $fullName ?>" required>
            </div>
            <button type="submit" class="btn-update">Save Changes</button>
        </form>
    </div>

    <!-- Change Email -->
    <div class="profile-section">
        <h2>Change Email</h2>
        <form method="post" action="profile.php">
            <input type="hidden" name="action" value="updateEmail">
            <div class="form-group">
                <label for="email">New Email</label>
                <input type="email" id="email" name="email" value="<?= $email ?>" required>
            </div>
            <button type="submit" class="btn-update">Update Email</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="profile-section">
        <h2>Change Password</h2>
        <form method="post" action="profile.php">
            <input type="hidden" name="action" value="updatePassword">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-update">Change Password</button>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="profile-section">
        <h2>Delete Account</h2>
        <form method="post" action="profile.php">
            <input type="hidden" name="action" value="deleteAccount">
            <button type="submit" class="btn-update btn-delete">Delete Account</button>
        </form>
    </div>

</main>

    <footer>
        <div class="container">
            <div class="contact">
                <img src="../images/Group 17.png" class="logo" alt="Shark Port City Logo">
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

    <div id="reauth-modal">
        <div id="reauth-modal-content">
            <p>For security, please re-enter your password.</p>
            <input type="password" id="reauth-password" placeholder="Password">
            <button id="reauth-submit" class="btn-update">Confirm</button>
            <button id="reauth-cancel" class="btn-update" style="background:#6c757d;">Cancel</button>
        </div>
    </div>

<!-- Add this in your <head> or before your custom JS -->
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>

<script>
  // --- Firebase config ---
  const firebaseConfig = {
    apiKey: "AIzaSyCKjyIBmC3WqSvtB7ynYE4YEM-ryz-PtEg",
    authDomain: "shark-port-city.firebaseapp.com",
    projectId: "shark-port-city",
    storageBucket: "shark-port-city.appspot.com",
    messagingSenderId: "469906666653",
    appId: "1:469906666653:web:94e866e387252ed21ed074",
    databaseURL: "https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/"
  };

  // Initialize Firebase
  const app = firebase.initializeApp(firebaseConfig);
  const db = firebase.database();

  // DOM Elements
  const fullNameInput = document.getElementById("full-name");
  const updateNameForm = document.getElementById("updateNameForm");
  const updatePasswordForm = document.getElementById("updatePasswordForm");
  const updateEmailForm = document.getElementById("updateEmailForm");
  const deleteAccountBtn = document.getElementById("deleteAccountBtn");

  // --- LOGIN SIMULATION ---
  // Partner ID stored in localStorage during login


  // --- LOAD PROFILE ---
  partnerRef.once("value").then(snapshot => {
    const data = snapshot.val();
    if (!data) {
      alert("Partner not found!");
      window.location.href = "login.html";
      return;
    }
    fullNameInput.value = data.full_name || '';
    document.getElementById("new-email").value = data.email || '';
  });

  // --- UPDATE NAME ---
  updateNameForm.addEventListener("submit", async e => {
    e.preventDefault();
    const newName = fullNameInput.value.trim();
    if (!newName) return;
    partnerRef.update({ full_name: newName })
      .then(() => alert("Name updated successfully!"))
      .catch(err => alert("Error updating name: " + err.message));
  });

  // --- UPDATE PASSWORD ---
  updatePasswordForm.addEventListener("submit", async e => {
    e.preventDefault();
    const newPassword = document.getElementById("new-password").value.trim();
    if (!newPassword) return;
    partnerRef.update({ password: newPassword })
      .then(() => alert("Password updated successfully!"))
      .catch(err => alert("Error updating password: " + err.message));
  });

  // --- UPDATE EMAIL ---
  updateEmailForm.addEventListener("submit", async e => {
    e.preventDefault();
    const newEmail = document.getElementById("new-email").value.trim();
    if (!newEmail) return;
    partnerRef.update({ email: newEmail })
      .then(() => alert("Email updated successfully!"))
      .catch(err => alert("Error updating email: " + err.message));
  });

  // --- DELETE ACCOUNT ---
  deleteAccountBtn.addEventListener("click", async () => {
    if (!confirm("Are you sure you want to delete your account? This action is permanent!")) return;
    partnerRef.remove()
      .then(() => {
        localStorage.removeItem("partnerId");
        alert("Account deleted successfully!");
        window.location.href = "delivery-history.php.html";
      })
      .catch(err => alert("Error deleting account: " + err.message));
  });
</script>



</body>
</html>