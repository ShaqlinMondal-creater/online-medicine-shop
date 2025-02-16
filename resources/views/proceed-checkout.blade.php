@extends('layouts.app')

@section('title', 'Proceed to Checkout')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <h2>Checkout</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(isset($userCart) && isset($userCart['cart_id']))
        <p><strong>Cart ID:</strong> {{ $userCart['cart_id'] }}</p>
        <input type="hidden" id="cart_id" name="cart_id" value="{{ $userCart['cart_id'] }}">
    @else
        <p class="text-danger">Cart not found! Ensure you have a valid cart before proceeding.</p>
    @endif

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4"><i class="fas fa-shipping-fast me-2"></i> Shipping Information</h4>

                    <form id="checkout-form">
                        @csrf
                        <input type="hidden" id="cart_id" name="cart_id" value="{{ $userCart['cart_id'] ?? '' }}">

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" class="form-control"
                                   value="{{ $loggedInUser['name'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control"
                                   value="{{ $loggedInUser['email'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" id="mobile" class="form-control"
                                   value="{{ $loggedInUser['phone'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" class="form-control"
                                   placeholder="Street, City, ZIP" required>
                        </div>

                        <!-- Shipping Options -->
                        <div class="mb-3">
                            <label for="shipping-option" class="form-label">Shipping Option</label>
                            <select id="shipping-option" class="form-control" required>
                                <option value="">- Select -</option>
                                <option value="dtdc" data-fee="60">DTDC Shipping (₨60.00 per 5Kg)</option>
                                <option value="shiprocket" data-fee="50">Ship Rocket (₨50.00)</option>
                                <option value="free_shipping" data-fee="10">Free Shipping (₨10.00)</option>
                            </select>
                        </div>

                        <!-- Payment Options -->
                        <div class="mb-3">
                            <label for="payment-option" class="form-label">Payment Option</label>
                            <select id="payment-option" class="form-control" required>
                                <option value="">- Select -</option>
                                <option value="upi" data-fee="8">UPI Payment (₨8.00)</option>
                                <option value="cod" data-fee="40">Cash On Delivery (₨40.00)</option>
                                <option value="credit_card" data-fee="0">Credit / Debit Card (Free)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Order Summary</h4>

                    @if(isset($userCart) && isset($userCart['products']))
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userCart['products'] as $product)
                                        <tr>
                                            <td><strong>{{ $product['name'] }}</strong> ({{ $product['sku'] ?? 'N/A' }})</td>
                                            <td>{{ $product['quantity'] }}</td>
                                            <td>₨{{ number_format($product['price'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>₨<span id="subtotal">{{ number_format($userCart['Total'], 2) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>GST (18%):</span>
                            <span>₨<span id="gst">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Shipping:</span>
                            <span>₨<span id="shipping-fee">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Payment Fee:</span>
                            <span>₨<span id="payment-fee">0.00</span></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span>₨<span id="grand-total">0.00</span></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>    

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function updateTotal() {
            let subtotal = parseFloat($("#subtotal").text()) || 0;
            let gst = subtotal * 0.18; // 18% GST
            let shippingFee = parseFloat($("#shipping-option option:selected").data("fee")) || 0;
            let paymentFee = parseFloat($("#payment-option option:selected").data("fee")) || 0;
            
            let grandTotal = subtotal + gst + shippingFee + paymentFee;

            // ✅ Update UI
            $("#gst").text(gst.toFixed(2));
            $("#shipping-fee").text(shippingFee.toFixed(2));
            $("#payment-fee").text(paymentFee.toFixed(2));
            $("#grand-total").text(grandTotal.toFixed(2));
        }

        // ✅ Initial calculation on page load
        updateTotal();

        // ✅ Update total whenever an option changes
        $("#shipping-option, #payment-option").on("change", function() {
            updateTotal();
        });

        // ✅ Place Order Handler
        // $("#checkout-form").submit(function(e) {
        //     e.preventDefault();

        //     let orderData = {
        //         user_id: "{{ $loggedInUser['id'] ?? '' }}",
        //         cart_id: $("#cart_id").val(),
        //         name: $("#name").val(),
        //         email: $("#email").val(),
        //         mobile: $("#mobile").val(),
        //         address: $("#address").val(),
        //         shipping: $("#shipping-option").val(),
        //         payment: $("#payment-option").val(),
        //         subtotal: $("#subtotal").text(),
        //         gst: $("#gst").text(),
        //         shipping_fee: $("#shipping-fee").text(),
        //         payment_fee: $("#payment-fee").text(),
        //         grand_total: $("#grand-total").text(),
        //         products: @json($userCart['products'] ?? [])
        //     };

        //     console.log("🚀 Placing Order:", orderData);

        //     $.ajax({
        //         url: "/place-order",
        //         type: "POST",
        //         data: JSON.stringify(orderData),
        //         contentType: "application/json",
        //         headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        //         success: function(response) {
        //             alert("✅ Order placed successfully! Order ID: " + response.order_id);
        //             window.location.href = response.redirect_url;
        //         },
        //         error: function(xhr) {
        //             console.log("❌ Order error:", xhr.responseText);
        //             alert("Something went wrong! Please try again.");
        //         }
        //     });
        // });

        $("#checkout-form").submit(function(e) {
    e.preventDefault();

    let orderData = {
        user_id: localStorage.getItem("user_id"),
        cart_id: localStorage.getItem("cart_id"),
        name: $("#name").val(),
        email: $("#email").val(),
        mobile: $("#mobile").val(),
        address: $("#address").val(),
        shipping: $("#shipping-option").val(),
        payment: $("#payment-option").val(),
        subtotal: $("#subtotal").text(),
        gst: $("#gst").text(),
        shipping_fee: $("#shipping-fee").text(),
        payment_fee: $("#payment-fee").text(),
        grand_total: $("#grand-total").text(),
        products: @json($userCart['products'] ?? [])
    };

    console.log("🚀 Placing Order:", orderData);

    $.ajax({
        url: "{{ route('place.order') }}",
        type: "POST",
        data: JSON.stringify(orderData),
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        success: function(response) {
            alert("✅ Order placed successfully! Order ID: " + response.order_id);

            // ✅ Store Order ID in local storage
            localStorage.setItem("order_id", response.order_id);
            
            // ✅ Clear local storage after order placement
            localStorage.removeItem("cart_id");

            window.location.href = response.redirect_url; // Redirect to success page
        },
        error: function(xhr) {
            console.log("❌ Order error:", xhr.responseText);
            alert("Something went wrong! Please try again.");
        }
    });
});

    });
</script>

@endsection
