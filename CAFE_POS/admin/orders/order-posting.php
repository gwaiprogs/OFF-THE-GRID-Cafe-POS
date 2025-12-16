<?php
session_start();

// Get cart data from SESSION
$cartData = isset($_SESSION['cart_data']) ? $_SESSION['cart_data'] : [];
$remarks = isset($_SESSION['remarks']) ? $_SESSION['remarks'] : '';
$subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;

// If cart is empty, redirect back
if (empty($cartData)) {
    header("Location: order-page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary - Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/order-posting-style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar shadow-sm px-4 d-flex justify-content-between">
    <span class="navbar-brand fw-bold fs-4">OFF-THE-GRID</span>

    <a href="order-page.php" class="btn btn-outline-light">
        Back to Order Page
    </a>
</nav>

<div class="payment-container">
  
    <div class="logo-header">
        <img src="../../photos/OTG-GREEN-2.png" alt="OFF-THE-GRID Logo" style="max-width: 150px; height: auto;">
        <h2 class="mb-0 mt-2">Order Summary & Payment</h2>
    </div>

    <!-- Order Summary Table -->
    <div class="p-4">
        <h4 class="fw-bold mb-4">Order Details</h4>
        
        <table class="table order-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Size</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartData as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['size']); ?></span></td>
                    <td class="text-center"><?php echo $item['qty']; ?></td>
                    <td class="text-end">₱<?php echo number_format($item['price'], 2); ?></td>
                    <td class="text-end">₱<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="total-row">
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">₱<?php echo number_format($subtotal, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Methods -->
    <div class="payment-section">
        <h4 class="fw-bold mb-4">Select Payment Method</h4>

        <!-- Cash Payment -->
        <div class="payment-method" data-method="cash">
            <label class="d-flex align-items-center mb-0 w-100">
                <input type="radio" name="payment" value="cash">
                <i class="bi bi-cash-coin text-success"></i>
                <span class="fs-5">Cash Payment</span>
            </label>
        </div>

        <!-- Cash Details -->
        <div class="cash-details" id="cash-details">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Bill Amount:</label>
                    <input type="number" class="form-control form-control-lg" id="bill-amount" placeholder="Enter amount" step="0.01" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Total Due:</label>
                    <input type="text" class="form-control form-control-lg" value="₱<?php echo number_format($subtotal, 2); ?>" readonly>
                </div>
            </div>
            <div class="alert alert-info" id="change-display" style="display: none;">
                <h5 class="mb-0">Change: <span id="change-amount" class="float-end">₱0.00</span></h5>
            </div>
        </div>

        <!-- GCash/QRPH Payment -->
        <div class="payment-method" data-method="qr">
            <label class="d-flex align-items-center mb-0 w-100">
                <input type="radio" name="payment" value="qr">
                <i class="bi bi-qr-code text-primary"></i>
                <span class="fs-5">GCash / QRPH</span>
            </label>
        </div>

        <!-- QR Details -->
        <div class="qr-details" id="qr-details">
            <div class="text-center">
                <h5 class="mb-3">Scan QR Code to Pay</h5>
                <img src="../../photos/gcash.jpg" class="qr-code-img" alt="QR Code">
                <p class="mt-3 text-muted">Amount: ₱<?php echo number_format($subtotal, 2); ?></p>
                <p class="text-info"><i class="bi bi-info-circle"></i> This is reflected at the CARD POS MACHINE</p>
            </div>
        </div>

        <!-- Card Payment -->
        <div class="payment-method" data-method="card">
            <label class="d-flex align-items-center mb-0 w-100">
                <input type="radio" name="payment" value="card">
                <i class="bi bi-credit-card text-warning"></i>
                <span class="fs-5">Card Payment</span>
            </label>
        </div>

        <!-- Card Details -->
        <div class="card-details" id="card-details" style="display: none;">
            <div class="text-center">
                <h5 class="mb-3">Check Machine POS Information</h5>
                <p class="mt-3 text-info"><i class="bi bi-info-circle"></i> This is reflected at the CARD POS MACHINE.</p>
            </div>
        </div>

        <!-- Print Receipt Button -->
        <div class="text-center mt-4">
            <button class="btn print-btn" id="print-receipt-btn" disabled>
                <i class="bi bi-printer-fill me-2"></i>Print Receipt
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const totalAmount = <?php echo $subtotal; ?>;

// Handle payment method selection
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.cash-details, .qr-details, .card-details').forEach(d => d.style.display = 'none');
        
        const paymentType = this.dataset.method;
        if (paymentType === 'cash') {
            document.getElementById('cash-details').style.display = 'block';
            document.getElementById('print-receipt-btn').disabled = true;
        } else if (paymentType === 'qr') {
            document.getElementById('qr-details').style.display = 'block';
            document.getElementById('print-receipt-btn').disabled = false;
        } else if (paymentType === 'card') {
            document.getElementById('card-details').style.display = 'block';
            document.getElementById('print-receipt-btn').disabled = false;
        }
    });
});

// Calculate change for cash payment
document.getElementById('bill-amount').addEventListener('input', function() {
    const billAmount = parseFloat(this.value) || 0;
    const change = billAmount - totalAmount;
    
    const changeDisplay = document.getElementById('change-display');
    const changeAmount = document.getElementById('change-amount');
    
    if (billAmount >= totalAmount) {
        changeDisplay.style.display = 'block';
        changeAmount.textContent = '₱' + change.toFixed(2);
        document.getElementById('print-receipt-btn').disabled = false;
    } else {
        changeDisplay.style.display = 'none';
        document.getElementById('print-receipt-btn').disabled = true;
    }
});

// NEW: Print receipt and save to database
document.getElementById('print-receipt-btn').addEventListener('click', function() {
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const billAmount = document.getElementById('bill-amount').value || 0;
    
    // Calculate change
    let changeAmount = 0;
    if (paymentMethod === 'cash') {
        changeAmount = parseFloat(billAmount) - totalAmount;
    }
    
    // Create hidden form to submit payment data
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'save-payment.php';
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = 'payment_method';
    methodInput.value = paymentMethod;
    form.appendChild(methodInput);
    
    const billInput = document.createElement('input');
    billInput.type = 'hidden';
    billInput.name = 'bill_amount';
    billInput.value = billAmount;
    form.appendChild(billInput);
    
    const changeInput = document.createElement('input');
    changeInput.type = 'hidden';
    changeInput.name = 'change_amount';
    changeInput.value = changeAmount;
    form.appendChild(changeInput);
    
    document.body.appendChild(form);
    form.submit();
});
</script>

</body>
</html>