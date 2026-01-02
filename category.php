<?php
require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$category = $_GET['category'] ?? '';

if (!$category) {
    die("Category not specified.");
}

// Firebase setup
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Fetch all menu items
// Using orderByChild and equalTo for a more efficient query if menu_items is large, 
// though the original method works for smaller datasets and your current filtering logic.
// For performance, the recommended approach is:
/*
try {
    $snapshot = $database->getReference('menu_items')
                         ->orderByChild('category')
                         ->equalTo($category)
                         ->getSnapshot();
    $categoryItems = $snapshot->getValue() ?? [];
} catch (\Exception $e) {
    $categoryItems = [];
    // Log error if needed
}
*/
// Sticking to your original fetch-and-filter logic to minimize changes to your core logic:
$menuItems = $database->getReference('menu_items')->getValue();

// Filter items by category
$categoryItems = [];
if ($menuItems) {
    // Note: The category field from Firebase must exactly match the URL parameter.
    foreach ($menuItems as $id => $item) {
        if (($item['category'] ?? '') === $category) {
            $categoryItems[$id] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($category) ?>  Shark Port City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Base styles */
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 30px; }
        
        /* New Menu Structure Styles */
        .menu-content { 
            /* You can add any overall container styles here */
            padding: 20px 0;
        }
        
        .promo-items {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px; /
        }

        .menu-item {
            width: 250px; 
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            /* Note: Your original CSS had a 10s transition. I've reduced it for a more standard effect. */
            transition: transform 0.10s ease, box-shadow 0.3s ease; 
        }
        
        .menu-item:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .menu-item img {
            width: 100%;
            height: 200px; 
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease, opacity 0.5s ease; /* Transition for image effects */
        }
        
        .menu-item:hover img {
            transform: scale(1.05); /* Slightly less aggressive zoom than 1.1 */
            opacity: 0.8; /* Slightly less opaque than 0.5 */
        }

        .menu-item h4 {
            font-family:sans-serif;
            font-size: 1.3rem;
            color: #333;
            margin: 15px 0;
            font-weight: 500;
        }
        
        .menu-item .price {
            font-size: 1.1rem;
            color: #333;
             /* Green color for price */
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .menu-item .see-all-btn {
            display: inline-block;
  background-color: black;
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-size: 0.8rem;
  padding: 10px 20px;
  margin: 0 0 20px 0;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
        }
        
        .menu-item .see-all-btn:hover {
            background-color: #ae0b0b;
        }
        /* Modal Custom Styles */
#menuItemModal .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    overflow: hidden;
}

#menuItemModal .modal-header {
    background: #111;
    color: #fff;
    border-bottom: none;
    padding: 15px 20px;
}

#menuItemModal .modal-title {
    font-weight: 600;
    font-size: 1.3rem;
}

#menuItemModal .btn-close {
    filter: invert(1); /* makes close button white */
}

#menuItemModal .modal-body {
    padding: 25px 20px;
}

#menuItemModal #menuItemImage {
    border-radius: 12px;
    margin-bottom: 15px;
    transition: transform 0.3s ease;
}

#menuItemModal #menuItemImage:hover {
    transform: scale(1.03);
}

#menuItemModal #menuItemIngredients {
    font-size: 0.95rem;
    color: #555;
    margin-bottom: 10px;
}

#menuItemModal #menuItemPrice {
    font-size: 1.2rem;
    color: #000000ff;
}

#menuItemModal .modal-footer {
    border-top: none;
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 0 0 15px 15px;
}

#menuItemModal .btn-dark {
    border-radius: 8px;
    padding: 8px 18px;
}

    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
function addToCart(buttonElement) {
    const itemElement = buttonElement.closest('.menu-item');
    const name = itemElement.querySelector('h4').getAttribute('data-name');
    const price = parseFloat(itemElement.querySelector('.price').getAttribute('data-price'));
      const image = itemElement.querySelector('img').getAttribute('src'); 
    // Create a cart item object
    const newItem = {
        name: name,
        price: price,
        image: image,
        quantity: 1
    };

    // Get existing cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Check if item already exists in cart
    const existingItem = cart.find(cartItem => cartItem.name === newItem.name);

    if (existingItem) {
        existingItem.quantity += 1; // Increase quantity
    } else {
        cart.push(newItem);
    }

    // Save updated cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));

    // Optional: alert user
   alert(`${newItem.name} added to cart!`);
}

// Optional: Function to display cart contents (for debugging)
function showCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    console.log('Cart:', cart);
}
function showDetails(itemElement) {
    const title = itemElement.getAttribute('data-title');
    const image = itemElement.getAttribute('data-image');
    const price = itemElement.getAttribute('data-price');
    const ingredients = itemElement.getAttribute('data-ingredients');

    document.getElementById('menuItemTitle').innerText = title;
    document.getElementById('menuItemImage').src = image;
    document.getElementById('menuItemPrice').innerText = "Rs. " + price;
    document.getElementById('menuItemIngredients').innerText = ingredients;

    const modal = new bootstrap.Modal(document.getElementById('menuItemModal'));
    modal.show();
}

</script>

</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4"><?= htmlspecialchars($category) ?> Menu</h1>

        <?php if(!empty($categoryItems)): ?>
            <div class="menu-content" id="category-content">
                <div class="promo-items">
                  <?php foreach($categoryItems as $item): ?>
    <div class="menu-item"
         onclick="showDetails(this)"
         data-title="<?= htmlspecialchars($item['title'] ?? 'Unknown Item') ?>"
         data-image="<?= htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/250x200?text=No+Image') ?>"
         data-price="<?= number_format($item['price'] ?? 0, 2) ?>"
         data-ingredients="<?= htmlspecialchars($item['ingredients'] ?? 'No ingredients listed') ?>">
         
        <img src="<?= htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/250x200?text=No+Image') ?>" 
             alt="<?= htmlspecialchars($item['title'] ?? 'Menu Item') ?>">

        <h4 data-name="<?= htmlspecialchars($item['title'] ?? 'Unknown Item') ?>">
            <?= htmlspecialchars($item['title'] ?? 'Unknown Item') ?>
        </h4>

        <p class="price" data-price="<?= number_format($item['price'] ?? 0, 0, '.', '') ?>">
            Rs. <?= number_format($item['price'] ?? 0, 2) ?>
        </p>

        <!-- StopPropagation ensures modal doesn’t open when clicking this -->
        <a href="#" class="see-all-btn"
           onclick="addToCart(this); event.stopPropagation(); return false;">
           Add To Cart
        </a>
    </div>
<?php endforeach; ?>

                </div>
            </div>
            <?php else: ?>
            <p class="text-center">No items found in this category.</p>
        <?php endif; ?>
    </div>
    <!-- Menu Item Modal -->
<div class="modal fade" id="menuItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="menuItemTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body text-center">
        <img id="menuItemImage" src="" alt="" class="img-fluid rounded mb-3" style="max-height:250px;object-fit:cover;">
        <p id="menuItemIngredients"></p>
        <h6 id="menuItemPrice" class="fw-bold"></h6>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

</body>
</html>