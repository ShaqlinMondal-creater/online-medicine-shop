@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <h2>Checkout</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
<style>
    .card {
        border-radius: 8px;
        border: none; /* Removes default card border */
    }

    .card-title {
        font-weight: 600;
        color: #444;
    }

    .list-group-item {
        border: none; /* Removes default list item border (optional) */
        border-bottom: 1px solid #ececec; /* Soft bottom border */
    }

</style>
    <!-- Row Container -->
    <div class="row g-4">
        <!-- ✅ Left Side: Shipping & Payment Details -->
        <div class="col-md-8">
            <!-- Card Wrapper -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Section Title with Icon -->
                    <h4 class="card-title mb-4">
                        <i class="fas fa-shipping-fast me-2"></i> Shipping Information
                    </h4>
    
                    <form id="checkout-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        
                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="name" 
                                    class="form-control" 
                                    value="{{ $loggedInUser['user']['name'] }}" 
                                    placeholder="Enter your full name"
                                    required
                                >
                            </div>
                        </div>
    
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>
                                <input 
                                    type="email" 
                                    id="email" 
                                    class="form-control" 
                                    value="{{ $loggedInUser['user']['email'] }}" 
                                    placeholder="Enter your email"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-telephone-fill"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="mobile" 
                                    class="form-control" 
                                    value="{{ $loggedInUser['user']['phone'] }}" 
                                    placeholder="Enter your Mobile Number"
                                    required
                                >
                            </div>
                        </div>
    
                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="address" 
                                    class="form-control" 
                                    placeholder="Street, City, ZIP" 
                                    required
                                >
                            </div>
                        </div>
    
                        <!-- Shipping Type Selection -->
                        <div class="mb-3">
                            <label for="shipping" class="form-label">Shipping Type</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-truck"></i>
                                </span>
                                <select id="shipping" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="standard" data-fee="30">
                                        Standard Shipping (₨30.00)
                                    </option>
                                    <option value="dtdc" data-fee="70">
                                        DTDC Shipping (₨70.00 per Kg)
                                    </option>
                                    <option value="shiprocket" data-fee="90">
                                        Ship Rocket Shipping (₨90.00 per 5Kg)
                                    </option>
                                </select>
                            </div>
                        </div>
    
                        <!-- Payment Method Selection -->
                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment Option</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="	bi bi-credit-card"></i>
                                </span>
                                <select id="payment" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="cod" data-fee="20">
                                        Cash On Delivery (₨20.00)
                                    </option>
                                    <option value="upi" data-fee="2">
                                        UPI (₨2.00)
                                    </option>
                                    <option value="card" data-fee="0">
                                        Credit / Debit Card (Free)
                                    </option>
                                    <option value="wallet" data-fee="0">
                                        Wallet (Free)
                                    </option>
                                </select>
                            </div>
                        </div>
    
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success w-100">
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    
        <!-- ✅ Right Side: Order Summary (Printed-Slip Style) -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Order Summary</h4>
    
                    @if($userCart && isset($userCart['products']))
                        <!-- Table to display products in a "receipt-like" manner -->
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 50%">Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userCart['products'] as $product)
                                        <tr>
                                            <td>
                                                <img src="/products/{{ $product['image'] }}" width="50" class="rounded me-2">
                                                <strong>{{ $product['name'] }}</strong><br>
                                                <small>SKU: {{ $product['sku'] }}</small>
                                            </td>
                                            <td>{{ $product['quantity'] }}</td>
                                            <td>₨{{ $product['price'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
    
                        <!-- Subtotal -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Subtotal:</span>
                            <span>₨<span id="subtotal">{{ $userCart['Total'] }}</span></span>
                        </div>
    
                        <!-- GST Calculation (18%) -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span>GST (18%):</span>
                            <span>₨<span id="gst">{{ round($userCart['Total'] * 0.18, 2) }}</span></span>
                        </div>
    
                        <!-- Shipping Fee -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Shipping Fee:</span>
                            <span>₨<span id="shipping-fee">0.00</span></span>
                        </div>
    
                        <!-- Payment Fee -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Payment Fee:</span>
                            <span>₨<span id="payment-fee">0.00</span></span>
                        </div>
    
                        <hr class="my-2">
    
                        <!-- Grand Total -->
                        <div class="d-flex justify-content-between align-items-center fw-bold">
                            <span>Total:</span>
                            <span>₨<span id="grand-total">{{ round($userCart['Total'] * 1.18, 2) }}</span></span>
                        </div>
                    @else
                        <p>Your cart is empty!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    

</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("checkout-form").addEventListener("submit", function (event) {
            event.preventDefault();

            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let shippingSelect = document.getElementById("shipping");
            let paymentSelect = document.getElementById("payment");

            let orderData = {
                name: document.getElementById("name").value.trim(),
                email: document.getElementById("email").value.trim(),
                mobile: document.getElementById("mobile").value.trim(),
                address: document.getElementById("address").value.trim(),
                shipping: shippingSelect.value,
                shipping_fee: shippingSelect.value ? parseFloat(shippingSelect.selectedOptions[0].getAttribute("data-fee")) : 0,
                payment: paymentSelect.value,
                payment_fee: paymentSelect.value ? parseFloat(paymentSelect.selectedOptions[0].getAttribute("data-fee")) : 0,
                total: parseFloat(document.getElementById("grand-total").innerText)
            };

            console.log("🔹 Sending Order Data:", JSON.stringify(orderData)); // ✅ Debugging

            fetch('/place-order', {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                credentials: "include", // ✅ Ensures cookies are sent (important for CSRF protection)
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                console.log("🔹 Response:", data); // ✅ Debugging

                if (data.order_id) {
                    alert(data.message);
                    window.location.href = "/order-success?order_id=" + data.order_id;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("🚨 Error placing order:", error));
        });
    });
</script>










@endsection
<!-- Include Font Awesome 5+ (CDN) -->

{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        let subtotal = parseFloat(document.getElementById("subtotal").innerText);
        let gst = parseFloat(document.getElementById("gst").innerText);
        let shippingFee = 0;
        let paymentFee = 0;
        let grandTotal = subtotal + gst;

        // ✅ Function to Update Total Price
        function updateTotal() {
            grandTotal = subtotal + gst + shippingFee + paymentFee;
            document.getElementById("shipping-fee").innerText = shippingFee.toFixed(2);
            document.getElementById("payment-fee").innerText = paymentFee.toFixed(2);
            document.getElementById("grand-total").innerText = grandTotal.toFixed(2);
        }

        // ✅ Handle Shipping Selection
        document.getElementById("shipping").addEventListener("change", function () {
            shippingFee = parseFloat(this.options[this.selectedIndex].getAttribute("data-fee"));
            updateTotal();
        });

        // ✅ Handle Payment Selection
        document.getElementById("payment").addEventListener("change", function () {
            paymentFee = parseFloat(this.options[this.selectedIndex].getAttribute("data-fee"));
            updateTotal();
        });

        // ✅ Handle Form Submission
        document.getElementById("checkout-form").addEventListener("submit", function (event) {
            event.preventDefault();

            let orderData = {
                name: document.getElementById("name").value,
                email: document.getElementById("email").value,
                mobile: document.getElementById("mobile").value,
                address: document.getElementById("address").value,
                shipping: document.getElementById("shipping").value,
                shipping_fee: shippingFee,
                payment: document.getElementById("payment").value,
                payment_fee: paymentFee,
                total: grandTotal
            };

            fetch('/place-order', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.href = "/order-success?order_id=" + data.order_id;
            })
            .catch(error => console.error("Error placing order:", error));
        });
    });
</script> --}}