<?php
session_start();

// Redirect if the request is not a POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: reservation.html");
    exit;
}

// Check if the 'table_type' key exists in the $_POST array before using it
if (isset($_POST['table_type'])) {
    $tableType = htmlspecialchars($_POST['table_type']);
} else {
    // Handle the case where the key is not set, maybe set a default value or redirect
    $tableType = 'Normal'; // Set a default value
}

// Use the null coalescing operator (??) for other variables to prevent similar warnings
$fullName = htmlspecialchars($_POST['full-name'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$phone = htmlspecialchars($_POST['phone'] ?? '');
$guests = intval($_POST['guests'] ?? 0);
$date = htmlspecialchars($_POST['date'] ?? '');
$time = htmlspecialchars($_POST['time'] ?? '');

$_SESSION['reservation_details'] = [
    'name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'guests' => $guests,
    'date' => $date,
    'time' => $time,
    'tableType' => $tableType
];

$tableFee = 0;

switch ($tableType) {
    case 'Normal':
        $tableFee = 20;
        break;
    case 'Family':
        $tableFee = 40;
        break;
    case 'VIP':
        $tableFee = 50;
        break;
}



$serviceCharge = 5; // A fixed service charge
$totalAmount =  $tableFee + $serviceCharge;

// --- End of placeholder ---
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shark Port City Colombo - Payment Summary</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <script src="https://kit.fontawesome.com/1165876da6.js" crossorigin="anonymous"></script>

    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-database.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

      
*{
    margin: 0;
	
	font-family: 'poppins',sans-serif;
	
}
body{
	background: rgba(255, 255, 255, 1);
	color: rgba(0, 0, 0, 1);
	margin: 0;

	background-size: cover;
	background-position: center;
	
	width: 100%;
    height: 130vh;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px 40px;
    background-color: #000000; 
    border-radius: 50px;
    margin: 10px 20px; /* Adds space around the header */
   
}

.logo {
    width: 80px; /* Adjust size to fit your logo */
    height: auto;
}

nav {
    display: flex;
    align-items: center;
    gap: 40px; /* Space between the menu links and the Accounts button */
}

nav ul {
    list-style: none;
    display: flex;
    gap: 25px; /* Space between navigation links */
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
    color: #f70404; /* Lighter green on hover for a subtle effect */
}

/* This is the new button for "Accounts" */
.accounts-btn {
    background-color: #f70404; /* Green button color */
    color: #ffffff; /* Dark text color for contrast */
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 20px; /* Rounded button corners */
    font-weight: 600;
    transition: background-color 0.3s;
    white-space: nowrap; /* Prevents text from wrapping on smaller screens */
}

.accounts-btn:hover {
    background-color: #f70404; /* Slightly darker green on hover */
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
    color: #fff; /* Icons color changed to white */
    font-size: 22px;
    transition: color 0.3s ease, transform 0.3s ease;
}

.ca a:hover {
    color: #f70404; /* Accent color on hover */
    transform: scale(1.1); /* Subtle hover animation */
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

        footer {
            width: 100%;
            background-color: black;
            margin-top: 50px;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-bottom: 10px;
            color: #fff;
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
        }

        .social-icons a:hover {
            color: #ccc;
        }

        .payment-summary-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .payment-summary-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .summary-details, .payment-details {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .saved-payment-method {
            margin-bottom: 20px;
        }
        
        .saved-payment-method h2 {
            margin-top: 0;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .saved-payment-method p {
            font-size: 1.1em;
            margin: 5px 0;
            color: #777;
        }
        
        .saved-payment-method .card-info {
            font-weight: 600;
            color: #333;
        }

        .summary-details h2, .payment-details h2 {
            margin-top: 0;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .summary-item, .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 1.1rem;
        }

        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: rgb(123, 0, 0);
        }
        
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
        .ca a {
  position: relative; /* make this parent relative */
  color: #fff; 
  font-size: 22px;
  transition: color 0.3s ease;
}

.cart-count {
  position: absolute;
  top: -8px;   /* adjust these values to fit */
  right: -8px;  /* adjust these values to fit */
  background: #dc0f0f;
  color: #fff;
  font-size: 12px;
  font-weight: bold;
  padding: 3px 7px;
  border-radius: 50%;
  min-width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 2px rgba(0,0,0,0.3);
  z-index: 10;
  font-family: 'Poppins', sans-serif;
  animation: none; /* remove animation */
}
    </style>
</head>
<body>
   
    <div class="header">
    <img src="images/Group 17.png" class="logo">
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li class="dropdown">
                <a href="#">The Menu ▾</a>
                <ul class="dropdown-content">
                    <li><a href="category.php?category=Appertizers">Appetizer</a></li>
                    <li><a href="category.php?category=Soups">Soups & Salads</a></li>
                    <li><a href="category.php?category=Rice%20%26%20Noodles">Rice & Salads</a></li>
                    <li><a href="category.php?category=Seafood">Seafood Delight</a></li>
                    <li><a href="category.php?category=Grilled_Specials">Meat Selection</a></li>
                    <li><a href="category.php?category=Sizzlers">Sizzlers</a></li>
                    <li><a href="category.php?category=Veggie Delight">Veggie Delights</a></li>
                    <li><a href="category.php?category=Pizza%20%26%20Pasta">Pizza & Pasta</a></li>
                    <li><a href="category.php?category=Snacks">Snacks</a></li>
                    <li><a href="category.php?category=Desserts">Desserts</a></li>
                    <li><a href="category.php?category=Beverages">Beverages</a></li>
                  
                     <li><a href="category.php?category=Chef_Specials">
                        <li><a href="category.php?category=Chef_Specials">Chef's Special</a></li>
                </ul>
            </li>
            <li><a href="location.html">Our Location</a></li>
            <li><a href="reservation.html">Reservation</a></li>
             <li><a href="tracking.html">Delivery Track</a></li>
            <li><a href="contact.html">Contact</a></li>
            
            <div class="ca">
              <a href="cart.html"><i class="fa-solid fa-cart-shopping"></i>
               <span id="cart-count" class="cart-count">0</span></a>
      <a href="setting.html"><i class="fa-solid fa-gear"></i></a></div>
        </ul>
        <a href="login.html" class="accounts-btn">Accounts</a>
    </nav>
</div>

    <div class="payment-summary-container">
        <h1>Review & Confirm</h1>

        <div class="summary-details">
            <h2>Reservation Details</h2>
            <div class="summary-item">
                <span>Name:</span>
                <span><?php echo $fullName; ?></span>
            </div>
            <div class="summary-item">
                <span>Date & Time:</span>
                <span><?php echo $date . ' at ' . $time; ?></span>
            </div>
            <div class="summary-item">
                <span>Table Type:</span>
                <span><?php echo $tableType; ?> Table</span>
            </div>
            <div class="summary-item">
                <span>Number of Guests:</span>
                <span><?php echo $guests; ?></span>
            </div>
        </div>
        
        <div class="payment-details">
            <h2>Payment Details</h2>
            
            <div class="summary-item">
                <span>Table Fee (<?php echo $tableType; ?>):</span>
                <span>$<?php echo number_format($tableFee, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Service Charge:</span>
                <span>$<?php echo number_format($serviceCharge, 2); ?></span>
            </div>
            <hr>
            <div class="payment-item">
                <span class="total-price">Total Amount Due:</span>
                <span class="total-price">$<?php echo number_format($totalAmount, 2); ?></span>
            </div>
        </div>

        
      <form method="post" action="create_payment.php">
    <input type="hidden" name="amount" value="<?php echo $totalAmount; ?>">
    <input type="hidden" name="order_id" value="<?php echo 'ORDER-' . time(); ?>">
    <button type="submit" class="btn-confirm-payment">Confirm Reservation</button>
</form>


    </div>

    <footer>
        <div class="container">
            <div class="contact">
                <img src="images/Group 17.png" class="logo">
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
    
    </script>
</body>
</html>