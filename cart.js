document.addEventListener('DOMContentLoaded', () => {
  const cartContent = document.getElementById('cart-content');
  const checkoutBtn = document.querySelector('.checkout-btn');
 const deliveryChargeDisplay = document.querySelector('.info-row p:nth-child(2)');


function getDeliveryCharge(subtotal) {
  if (subtotal >= 5000) return 0;        
  if (subtotal >= 2000) return 200;      
  return 300;                            
}
  // --- Load Cart ---
  function loadCart() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let subtotal = 0;

    // Clear existing content
    cartContent.innerHTML = '';

    if (cart.length === 0) {
      cartContent.innerHTML = `
        <div class="empty-cart-message">
          <p>Your Cart looks a little empty</p>
        </div>
      `;
      checkoutBtn.innerHTML = `Checkout <span class="price-value">Rs. 0.00</span>`;
      deliveryChargeDisplay.innerHTML = `Rs. 0.00`;
      return;
    }

    // Loop through each item in the cart and create the HTML to display it
    cart.forEach(item => {
      const itemElement = document.createElement('div');
      itemElement.classList.add('cart-item-row');
      itemElement.innerHTML = `
       <div class="cart-item">
    <img src="${item.image}" alt="${item.name}" class="cart-item-img">
    <div class="cart-item-details">
      <h5 class="cart-item-name">${item.name}</h5>
      <p class="cart-item-price">Rs. ${item.price.toFixed(2)}</p>
      <div class="item-quantity">
        <button class="quantity-btn" data-action="decrement" data-name="${item.name}">-</button>
        <span class="quantity-count">${item.quantity}</span>
        <button class="quantity-btn" data-action="increment" data-name="${item.name}">+</button>
      </div>
    </div>
  </div>
      `;
      cartContent.appendChild(itemElement);
      subtotal += item.price * item.quantity;
    });

    const deliveryCharge = getDeliveryCharge(subtotal);
const finalTotal = subtotal + deliveryCharge;

checkoutBtn.innerHTML = `Checkout <span class="price-value">Rs. ${finalTotal.toFixed(2)}</span>`;
deliveryChargeDisplay.innerHTML = `Rs. ${deliveryCharge.toFixed(2)}`;

  }

  // --- Quantity Control ( + / - ) ---
  cartContent.addEventListener('click', (event) => {
    if (event.target.classList.contains('quantity-btn')) {
      const button = event.target;
      const action = button.dataset.action;
      const itemName = button.dataset.name;

      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const itemToUpdate = cart.find(item => item.name === itemName);

      if (itemToUpdate) {
        if (action === 'increment') {
          itemToUpdate.quantity++;
        } else if (action === 'decrement') {
          if (itemToUpdate.quantity > 1) {
            itemToUpdate.quantity--;
          } else {
            // Remove item if quantity reaches 0
            cart = cart.filter(item => item.name !== itemName);
          }
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
      }
    }
  });

  // --- Tab Switching ---
  const tabs = document.querySelectorAll('.cart-header-link');
  const contents = document.querySelectorAll('.cart-content-section');
  const cartContainer = document.querySelector('.cart-container');

  tabs.forEach(tab => {
    tab.addEventListener('click', (event) => {
      event.preventDefault();

      tabs.forEach(item => item.classList.remove('active'));
      contents.forEach(content => content.classList.remove('active-content'));

      tab.classList.add('active');
      const targetId = tab.id.replace('-tab', '-content');
      document.getElementById(targetId).classList.add('active-content');

      if (tab.id === 'history-tab') {
        cartContainer.classList.add('history-active');
      } else {
        cartContainer.classList.remove('history-active');
      }
    });
  });

  // --- Checkout Button ---
  checkoutBtn.addEventListener('click', (event) => {
    event.preventDefault();
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length > 0) {
      window.location.href = 'checkout.html';
    } else {
      alert("Your cart is empty. Please add items to proceed.");
    }
  });

  // --- Initial Load ---
  loadCart();
  
});
