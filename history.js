// history.js
document.addEventListener("DOMContentLoaded", () => {
    const historyContent = document.getElementById("history-content");
    const historyTab = document.getElementById("history-tab");
    const cartTab = document.getElementById("cart-tab");
    const container = document.querySelector(".cart-container");

    async function loadOrderHistory(user) {
        if (!user) {
            historyContent.innerHTML = `<p class="empty-order-message">Please log in to see your order history.</p>`;
            return;
        }

        try {
            const snapshot = await db.ref("orders").once("value");
            const orders = snapshot.val();

            if (!orders) {
                historyContent.innerHTML = `<p class="empty-order-message">You have no orders yet.</p>`;
                return;
            }

            const userOrders = Object.entries(orders)
                .filter(([id, order]) => order.userId === user.uid)
                .sort((a, b) => new Date(b[1].timestamp) - new Date(a[1].timestamp));

            if (userOrders.length === 0) {
                historyContent.innerHTML = `<p class="empty-order-message">You have no orders yet.</p>`;
                return;
            }

            historyContent.innerHTML = "";

            userOrders.forEach(([id, order]) => {
                const itemsObject = order.items || {};
                const itemsList = Object.values(itemsObject)
                    .map(item => `<li>${item.name} x ${item.quantity} - Rs.${item.price}</li>`)
                    .join("");

                const orderCard = document.createElement("div");
                orderCard.classList.add("order-card");
                orderCard.innerHTML = `
                    <div class="order-header">
                        <h4>Order ID: ${id}</h4>
                        <p><strong>Status:</strong> ${order.status || "Pending"}</p>
                        <p><strong>Date:</strong> ${new Date(order.timestamp).toLocaleString()}</p>
                    </div>
                    <ul class="order-items">${itemsList}</ul>
                    <div class="order-summary">
                        <p><strong>Subtotal:</strong> Rs.${Number(order.subtotal || 0).toFixed(2)}</p>
                        <p><strong>Delivery:</strong> Rs.${Number(order.deliveryCharge || 0).toFixed(2)}</p>
                        <p><strong>Total:</strong> Rs.${Number(order.total || 0).toFixed(2)}</p>
                    </div>
                `;
                historyContent.appendChild(orderCard);
            });
        } catch (error) {
            console.error("Error loading order history:", error);
            historyContent.innerHTML = `<p class="empty-order-message">Failed to load order history. Please try again.</p>`;
        }
    }

    auth.onAuthStateChanged(user => {
        if (user) loadOrderHistory(user);
    });

    cartTab.addEventListener("click", () => {
        cartTab.classList.add("active");
        historyTab.classList.remove("active");
        historyContent.classList.remove("active-content");
        container.classList.remove("history-active");
    });

    historyTab.addEventListener("click", () => {
        historyTab.classList.add("active");
        cartTab.classList.remove("active");
        historyContent.classList.add("active-content");
        container.classList.add("history-active");
        const user = auth.currentUser;
        if (user) loadOrderHistory(user);
    });
});
