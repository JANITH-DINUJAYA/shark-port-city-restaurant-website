<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>Shark Port City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        :root {
  --primary-lime: #a4e637;
  --dark-red: #7b0000;
  --pure-black: #000000;
  --dark-bg: #0a0a0a;
  --card-bg: rgba(15, 15, 15, 0.9);
  --glass-border: rgba(164, 230, 55, 0.15);
  --text-primary: #ffffff;
  --text-secondary: #cccccc;
  --text-muted: #999999;
}
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body { background-color: #fff;
            
            color: rgb(255, 255, 255);
            margin: 0;
            background-size: cover;
            background-position: center;
            width: 100%;
            min-height: 100vh;
        }

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
            border-radius: 10px;
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

        .dropdown-content li a:hover {
            background: #222;
            text-decoration: underline;
            text-decoration-color: #f70404;
            text-decoration-thickness: 3px;
        }

        .dropdown:hover .dropdown-content {
            display: block;
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

        .hero-section {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60vh;
            text-align: center;
            color: #fff;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            z-index: 1;
            margin: 20px;
            border-radius: 20px;
            overflow: hidden;
        }

        .hero-content {
            max-width: 800px;
            padding: 1rem;
            animation: slideInUp 1.5s ease-out;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Stats Section - Modern Cards */
        .shori {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            padding: 40px 30px;
            background: linear-gradient(135deg, rgb(123, 0, 0), #1c1c1c);
            color: #f0f0f0;
            margin: 20px;
            border-radius: 20px;
        }

        .shori .columns {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px 25px;
            text-align: center;
            color: #f0f0f0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .shori .columns:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
        }

        .shori i {
            color: #ffcc00;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .shori .columns:hover i {
            transform: scale(1.2);
        }

        .shori h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #ffffff;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
        }

        .shori h3::after {
            content: "";
            position: absolute;
            width: 0%;
            height: 3px;
            background: #ffcc00;
            left: 0;
            bottom: -6px;
            transition: width 0.4s ease;
        }

        .shori .columns:hover h3::after {
            width: 100%;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffcc00;
            margin: 10px 0;
        }

        .shori p {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            line-height: 1.6;
            color: #dcdcdc;
        }

        /* Controls Section */
        .controls-section {
            background: white;
            padding: 30px;
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .menu-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .menu-header .header-link {
            display: inline-block;
            padding: 15px 30px;
            text-decoration: none;
            color: #555;
            font-size: 20px;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .menu-header .header-link.active {
            border-bottom-color: #e50000;
            color: #333;
        }

        .menu-header .header-link:hover {
            color: #e50000;
        }

        .filter-controls {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-container {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            color: #333;
        }

        .search-input:focus {
            outline: none;
            border-color: #e50000;
            box-shadow: 0 0 0 3px rgba(229, 0, 0, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
        }

        .filter-select {
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            background: white;
            color: #333;
            cursor: pointer;
            transition: border-color 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .filter-select:focus {
            outline: none;
            border-color: #e50000;
        }

        /* Buttons */
        .see-all-btn, .btn {
            display: inline-block;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 1rem;
            padding: 12px 25px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .see-all-btn:hover, .btn:hover {
            background-color: #e50000;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(229, 0, 0, 0.3);
        }

        .btn-success {
            background-color: #10b981;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #ddd;
            color: #666;
        }

        .btn-outline:hover {
            background: #f9fafb;
            border-color: #e50000;
            color: #e50000;
        }

        /* Orders Grid */
        .menu-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }

        .menu-item {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            color: #333;
            border-left: 5px solid transparent;
        }

        .menu-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .menu-item.pending {
            border-left-color: #ffcc00;
        }

        .menu-item.processing {
            border-left-color: #3b82f6;
        }

        .menu-item.ready {
            border-left-color: #10b981;
        }

        .menu-item.delivered {
            border-left-color: #6b7280;
        }

        .menu-item.cancelled {
            border-left-color: #ef4444;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .order-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-processing {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-ready {
            background: #d1fae5;
            color: #065f46;
        }

        .status-delivered {
            background: #f3f4f6;
            color: #374151;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .order-content {
            padding: 25px;
        }

        .order-details {
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 3px 0;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
        }

        .order-items-section {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .order-items-section h4 {
            color: #374151;
            margin-bottom: 10px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item-list {
            list-style: none;
            padding: 0;
        }

        .item-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-list li:last-child {
            border-bottom: none;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
            border-radius: 20px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            color: #333;
        }

        .modal h2 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 1.8rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #e50000;
            box-shadow: 0 0 0 3px rgba(229, 0, 0, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            background: white;
            margin: 20px;
            border-radius: 20px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Footer */
        footer {
            width: 100%;
            background-color: black;
            color: white;
            margin-top: 40px;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            gap: 30px;
        }

        .contact {
            text-align: center;
        }

        .contact h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: white;
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
            color: white;
            font-size: 25px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #a4e637;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 20px;
                margin: 10px;
                flex-direction: column;
                gap: 20px;
            }

            nav {
                width: 100%;
                justify-content: center;
            }

            nav ul {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .menu-items-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .shori {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .menu-header {
                flex-direction: column;
                gap: 10px;
            }
        }
        .toggle-container {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: center;
    margin-top: 20px;
    font-size: 1rem;
    color: #fff;
}

.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 28px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #10b981;
}

input:checked + .slider:before {
    transform: translateX(22px);
}

.slider.round {
    border-radius: 28px;
}

.slider.round:before {
    border-radius: 50%;
}

    </style>

</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="../images/Group 17.png" class="logo" alt="Shark Port">
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
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

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Shark Port City</h1>
            <p>Track all restaurant orders at Shark Port City Colombo</p>
            <div class="toggle-container">
    <span>Status:</span>
    <label class="switch">
        <input type="checkbox" id="orderStatusToggle">
        <span class="slider round"></span>
    </label>
    <span id="statusLabel">Inactive</span>
</div>
        </div>
    </div>

   
    <!-- Footer -->
    <footer>
        <div class="container">
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
    


<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database-compat.js"></script>

<script>
  // Firebase config
  const firebaseConfig = {
    apiKey: "AIzaSyCKjyIBmC3WqSvtB7ynYE4YEM-ryz-PtEg",
    authDomain: "shark-port-city.firebaseapp.com",
    databaseURL: "https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "shark-port-city",
    storageBucket: "shark-port-city.appspot.com",
    messagingSenderId: "469906666653",
    appId: "1:469906666653:web:94e866e387252ed21ed074"
  };
  firebase.initializeApp(firebaseConfig);
  const database = firebase.database();

  const toggle = document.getElementById('orderStatusToggle');
  const label = document.getElementById('statusLabel');

  // Get current logged-in partner ID from PHP session
  const partnerId = "<?php echo $_SESSION['user']['id'] ?? ''; ?>";

  if (!partnerId) {
    alert("No partner logged in");
  } else {
    const partnerStatusRef = database.ref('partners/' + partnerId + '/status');

    // Watch for status changes in realtime
    partnerStatusRef.on('value', snapshot => {
      const status = snapshot.val();

      if (status === "Busy") {
        // Driver is on delivery
        toggle.checked = false;
        toggle.disabled = true; // disable toggle while busy
        label.textContent = "Busy";
      } else if (status === "Active") {
        // Available for orders
        toggle.checked = true;
        toggle.disabled = false;
        label.textContent = "Active";
      } else {
        // Inactive
        toggle.checked = false;
        toggle.disabled = false;
        label.textContent = "Inactive";
      }
    });

    // Update status on toggle change (only Active/Inactive, not Busy)
    toggle.addEventListener('change', () => {
      const status = toggle.checked ? "Active" : "Inactive";
      partnerStatusRef.set(status);
    });
  }
</script>


</body>


</html>