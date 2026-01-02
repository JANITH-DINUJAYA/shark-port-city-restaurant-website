<?php
require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase_service_account.json')
    ->withDatabaseUri('https://shark-port-city-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $factory->createDatabase();

// Fetch promotions
$promoRef = $database->getReference('promotions');
$promotions = $promoRef->getValue();

// Fetch promo codes
$codesRef = $database->getReference('promoCodes');
$promoCodes = $codesRef->getValue();

if ($promotions):
    foreach ($promotions as $id => $promo): ?>
        <div class="menu-item promo-card" data-promo-id="<?= $id ?>">
            <img src="<?= htmlspecialchars($promo['image']) ?>" alt="<?= htmlspecialchars($promo['title']) ?>">
            <h4 data-name="<?= htmlspecialchars($promo['title']) ?>">
                <?= htmlspecialchars($promo['title']) ?>
            </h4>
           <p class="price" 
   data-price="<?= htmlspecialchars($promo['price']) ?>" 
   data-discounted-price="<?= htmlspecialchars($promo['price']) ?>">
    Rs. <?= number_format($promo['price'], 2) ?>
</p>

            <div class="promo-actions">
                <input type="text" class="promo-input form-control" placeholder="Enter Promo Code" style="margin-bottom:8px;">
                <button class="btn btn-primary apply-btn">Apply</button>
                <button class="btn btn-success add-cart-btn" disabled onclick="addToCart(this)">Add To Cart</button>
            </div>
        </div>
<?php
    endforeach;
else: ?>
    <p>No promotions available at the moment.</p>
<?php endif; ?>
<html>
    <style>
        .promo-actions .apply-btn,
.promo-actions .add-cart-btn {
    display: inline-block;
    background-color: black;       /* Button color */
    color: white;                 /* Text color */
    text-decoration: none;
    border-radius: 10px;          /* Rounded corners */
    font-size: 0.8rem;            /* Font size */
    padding: 10px 20px;           /* Padding inside button */
    margin-right: 10px;           /* Space between buttons */
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover effect */
.promo-actions .apply-btn:hover,
.promo-actions .add-cart-btn:hover {
    background-color: #ae0b0b;   /* Dark red on hover */
    transform: translateY(-2px); /* Slight lift on hover */
}

/* Disable state */
.promo-actions .add-cart-btn:disabled {
    background-color: #555;      /* Greyed out */
    cursor: not-allowed;
}
</style>
    </html>