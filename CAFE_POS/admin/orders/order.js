var cart = [];

// Handle size button click
var sizeBtns = document.querySelectorAll(".size-btn");
for (var i = 0; i < sizeBtns.length; i++) {
    sizeBtns[i].addEventListener("click", function() {
        var card = this.closest(".drink-card");
        var name = card.getAttribute("data-name");
        var pricesJson = card.getAttribute("data-price");
        var prices = JSON.parse(pricesJson);
        var size = this.getAttribute("data-size");
        var price = prices[size];
        
        addToCart(name, size, price);
    });
}

function addToCart(name, size, price) {
    var found = false;
    
    for (var i = 0; i < cart.length; i++) {
        if (cart[i].name === name && cart[i].size === size) {
            cart[i].qty++;
            found = true;
            break;
        }
    }
    
    if (!found) {
        cart.push({
            name: name,
            size: size,
            price: price,
            qty: 1
        });
    }
    
    updateCartUI();
}

function updateCartUI() {
    var container = document.getElementById("order-items");
    container.innerHTML = "";
    
    var subtotal = 0;
    
    for (var i = 0; i < cart.length; i++) {
        var item = cart[i];
        subtotal = subtotal + (item.price * item.qty);
        
        var itemDiv = document.createElement("div");
        itemDiv.className = "order-item mb-3 p-2 rounded";
        
        itemDiv.innerHTML = 
            '<div class="d-flex justify-content-between">' +
                '<span class="fw-semibold">' + item.name + ' (' + item.size + ')</span>' +
                '<span>Php ' + (item.price * item.qty).toFixed(2) + '</span>' +
            '</div>' +
            '<div class="qty-control">' +
                '<button class="qty-btn" onclick="changeQty(' + i + ', -1)">-</button>' +
                '<input type="number" class="qty-input" min="1" value="' + item.qty + '" onchange="manualQty(' + i + ', this.value)">' +
                '<button class="qty-btn" onclick="changeQty(' + i + ', 1)">+</button>' +
            '</div>';
        
        container.appendChild(itemDiv);
    }
    
    updateTotals(subtotal);
}

function changeQty(index, change) {
    cart[index].qty = cart[index].qty + change;
    
    if (cart[index].qty <= 0) {
        cart.splice(index, 1);
    }
    
    updateCartUI();
}

function manualQty(index, value) {
    var qty = parseInt(value);
    
    if (isNaN(qty) || qty < 1) {
        qty = 1;
    }
    
    cart[index].qty = qty;
    updateCartUI();
}

function updateTotals(subtotal) {
    var itemCount = 0;
    
    for (var i = 0; i < cart.length; i++) {
        itemCount = itemCount + cart[i].qty;
    }
    
    document.getElementById("item-count").textContent = itemCount;
    document.getElementById("item-total").textContent = "Php " + subtotal.toFixed(2);
    document.getElementById("grand-total").textContent = "Php " + subtotal.toFixed(2);
}

// Handle form submission
document.getElementById("checkout-form").addEventListener("submit", function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert("Your cart is empty!");
        return;
    }
    
    document.getElementById("cart-data-input").value = JSON.stringify(cart);
    
    var remarks = document.querySelector(".remarks");
    if (remarks) {
        document.getElementById("remarks-input").value = remarks.value;
    }
});

// SEARCH FUNCTIONALITY
var searchInput = document.querySelector('input[name="search"]');

searchInput.addEventListener('input', function() {
    var searchTerm = this.value.toLowerCase().trim();
    var allCards = document.querySelectorAll('.drink-card');
    
    for (var i = 0; i < allCards.length; i++) {
        var card = allCards[i];
        var productName = card.getAttribute("data-name").toLowerCase();
        var cardColumn = card.closest('.col-md-4');
        
        if (searchTerm === '') {
            cardColumn.style.display = 'block';
        } else {
            if (productName.indexOf(searchTerm) !== -1) {
                cardColumn.style.display = 'block';
            } else {
                cardColumn.style.display = 'none';
            }
        }
    }
    
    checkForResults();
});

function checkForResults() {
    var sections = document.querySelectorAll('.category-section');
    
    for (var i = 0; i < sections.length; i++) {
        var section = sections[i];
        
        if (!section.classList.contains('d-none')) {
            var cardsInSection = section.querySelectorAll('.col-md-4');
            
            // Remove existing "no results" message
            var existingMsg = section.querySelector('.no-results-message');
            if (existingMsg) {
                existingMsg.remove();
            }
            
            // Check if all cards are hidden
            var allHidden = true;
            for (var j = 0; j < cardsInSection.length; j++) {
                if (cardsInSection[j].style.display !== 'none') {
                    allHidden = false;
                    break;
                }
            }
            
            if (allHidden && cardsInSection.length > 0) {
                var noResultsDiv = document.createElement('div');
                noResultsDiv.className = 'no-results-message col-12 text-center py-5';
                noResultsDiv.innerHTML = 
                    '<i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>' +
                    '<p class="mt-3 text-muted">No products found matching your search.</p>';
                
                section.querySelector('.row').appendChild(noResultsDiv);
            }
        }
    }
}