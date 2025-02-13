@extends('layouts.app')

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
                                   value="{{ $loggedInUser['user']['name'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control"
                                   value="{{ $loggedInUser['user']['email'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" id="mobile" class="form-control"
                                   value="{{ $loggedInUser['user']['phone'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" class="form-control"
                                   placeholder="Street, City, ZIP" required>
                        </div>

                        <div class="mb-3">
                            <label for="shipping" class="form-label">Shipping Type</label>
                            <select id="shipping" class="form-control" required>
                                <option value="">-Select-</option>
                                <option value="standard" data-fee="30">Standard Shipping (₨30.00)</option>
                                <option value="dtdc" data-fee="70">DTDC Shipping (₨70.00 per Kg)</option>
                                <option value="shiprocket" data-fee="90">Ship Rocket Shipping (₨90.00 per 5Kg)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment Option</label>
                            <select id="payment" class="form-control" required>
                                <option value="">-Select-</option>
                                <option value="cod" data-fee="20">Cash On Delivery (₨20.00)</option>
                                <option value="upi" data-fee="2">UPI (₨2.00)</option>
                                <option value="card" data-fee="0">Credit / Debit Card (Free)</option>
                                <option value="wallet" data-fee="0">Wallet (Free)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>

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
                                            <td><strong>{{ $product['product_id'] }}</strong></td>
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
                            <span>Tax (18%):</span>
                            <span>₨<span id="tax">0.00</span></span>
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
            let subtotal = parseFloat($("#subtotal").text());
            let tax = subtotal * 0.18;
            let shippingFee = parseFloat($("#shipping option:selected").data("fee")) || 0;
            let paymentFee = parseFloat($("#payment option:selected").data("fee")) || 0;
            let grandTotal = subtotal + tax + shippingFee + paymentFee;

            $("#tax").text(tax.toFixed(2));
            $("#shipping-fee").text(shippingFee.toFixed(2));
            $("#payment-fee").text(paymentFee.toFixed(2));
            $("#grand-total").text(grandTotal.toFixed(2));
        }

        $("#shipping, #payment").change(updateTotal);

        $("#checkout-form").submit(function(e) {
            e.preventDefault();

            let orderData = {
                user_id: "{{ $loggedInUser['user']['id'] ?? '' }}",
                cart_id: $("#cart_id").val(),
                name: $("#name").val(),
                email: $("#email").val(),
                mobile: $("#mobile").val(),
                address: $("#address").val(),
                shipping: $("#shipping").val(),
                payment: $("#payment").val(),
                subtotal: $("#subtotal").text(),
                tax: $("#tax").text(),
                shipping_fee: $("#shipping-fee").text(),
                payment_fee: $("#payment-fee").text(),
                grand_total: $("#grand-total").text(),
                products: @json($userCart['products'] ?? [])
            };

            $.ajax({
                url: "{{ route('place.order') }}",
                type: "POST",
                data: JSON.stringify(orderData),
                contentType: "application/json",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function(response) {
                    alert("Order placed successfully! Order ID: " + response.order_id);
                    window.location.href = "/order-success?order_id=" + response.order_id;
                }
            });

        });

        updateTotal();
    });
</script>

@endsection
