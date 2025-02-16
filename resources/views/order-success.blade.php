{{-- @extends('layouts.app')

@section('content')

<div class="order-page-wrapper py-5">
    <div class="container">
        <div class="text-center">
            <h2 class="text-success mt-4 fw-bold">
                <i class="fas fa-check-circle"></i> Order Placed Successfully!
            </h2>
            <p class="lead">Thank you for your purchase. Your order has been placed successfully.</p>
        </div>

        <!-- Card containing order details -->
        <div class="card shadow-sm mx-auto mt-4" style="max-width: 900px;">
            <div class="card-body p-4">
                @if(session('order_details'))
                    @php
                        $order = session('order_details');
                    @endphp
                <div class="head_order">
                    <h3 class="card-title mb-3 s_size">Order Details</h3>
                    <h2 class="card-title mb-3 s_size"><strong>Order ID:</strong> {{ $order['order_id'] }}</h2>
                </div>
                <hr class="mb-4">

                

                    <div class="order_success">

                        <!-- Basic Order Info (Address & Products) -->
                        <div class="basic_details row g-3">
                            <!-- Address Details -->
                            <div class="address_details col-md-7">
                                <div class="p-3 details-bg rounded">
                                    <h5 class="mb-3">
                                        <i class="bi bi-geo-alt-fill me-1"></i> Address
                                    </h5>
                                    <p><strong>Name:</strong> {{ $order['name'] }}</p>
                                    <p><strong>Email:</strong> {{ $order['email'] }}</p>
                                    <p><strong>Mobile:</strong> {{ $order['mobile'] }}</p>
                                    <p><strong>Address:</strong> {{ $order['address'] }}</p>
                                    <p><strong>Shipping:</strong> {{ ucfirst($order['shipping']) }}</p>
                                    <p><strong>Payment Method:</strong> {{ ucfirst($order['payment']) }}</p>
                                </div>
                            </div>

                            <!-- Ordered Products -->
                            <div class="order_summary p-3 rounded shadow-sm col-md-5">
                                <h5 class="mb-3">
                                    <i class="bi bi-receipt me-1"></i> Order Summary
                                </h5>
                                <div class="mb-1 oo">
                                    <p><strong>Subtotal:</strong></p>
                                    <p>₹ {{ number_format($order['subtotal'], 2) }} /-</p>
                                </div>
                                <div class="mb-1 oo">
                                    <p><strong>Tax (18%):</strong></p> 
                                    <p>₹ {{ number_format($order['tax'], 2) }} /-</p>
                                </div>
                                <div class="mb-1 oo">
                                    <p><strong>Shipping Fee:</strong> </p>
                                    <p>₹ {{ number_format($order['shipping_fee'], 2) }} /-</p>
                                </div>
                                <div class="mb-5 oo">
                                    <p><strong>Payment Fee:</strong> </p>
                                    <p>₹ {{ number_format($order['payment_fee'], 2) }} /-</p>
                                </div>
                                <div class="fs-5 fw-bold bt-2 oo">
                                    <p> 
                                        <strong>Grand Total:</strong>
                                    <p>
                                        ₹ {{ number_format($order['grand_total'], 2) }} /-
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- End .basic_details -->

                        <hr class="my-4">

                        <!-- Order Summary -->
                        <div class="product_details p-3 rounded shadow-sm">
                            <div class="p-3 details-bg rounded">
                                <h5 class="mb-3">
                                    <i class="bi bi-bag-check-fill me-1"></i> Ordered Products
                                </h5>
                                <ul class="list-group">
                                    @foreach($order['products'] as $product)
                                        <li class="list-group-item border-0 border-bottom">
                                            <div class="row align-items-center">
                                                <!-- Product Image -->
                                                <div class="col-auto">
                                                    <img 
                                                        src="/products/{{ $product['image'] }}" 
                                                        alt="{{ $product['name'] }}"
                                                        class="img-thumbnail"
                                                        style="max-width: 80px;"
                                                    >
                                                </div>
                                                <!-- Product Details -->
                                                <div class="col text-start">
                                                    <strong>Product Name:</strong> {{ $product['name'] }} <br>
                                                    <strong>Product SKU:</strong> {{ $product['sku'] }} <br>
                                                    <strong>Quantity:</strong> {{ $product['quantity'] }} <br>
                                                    <strong>Price:</strong> ₨{{ number_format($product['price'], 2) }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>                            
                        </div>
                    </div>
                @else
                    <!-- No Order Data Found -->
                    <p class="text-danger">Order details not found!</p>
                @endif

                <div class="text-center">
                    <a href="{{ url('/') }}" class="btn btn-primary mt-4">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .head_order{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .s_size{
        font-size: 36px !important;
    }
    .oo{
        display: flex;
        justify-content: space-between;
        align-items: center; 
        font-size: 20px;       
    }
    .oo p{
        font-family: monospace;
    }
    /* 
    Wrap the whole page in a subtle gradient background
    You can adjust the gradient colors to fit your theme.
    */
    .order-page-wrapper {
        background: linear-gradient(to right, #fff3cd, #adb5bd);
    }

    /* Overall wrapper for order details to handle layout spacing */
    .order_success {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* A soft, light background for the address and product details */
    .details-bg {
        background-color: #fdfdfd;
        border: 1px solid #ececec;
    }

    /* Basic text styling for address details and product list */
    .address_details p,
    .product_details p {
        margin: 0.25rem 0;
        font-family: 'Open Sans', sans-serif;
    }

    /* Styling the summary box with a subtle shadow */
    .order_summary {
        background-color: #fff;
    }

    /* Example: You can customize Bootstrap Icons by loading them in your layout:
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" />
    */
</style> --}}

@extends('layouts.app')

@section('content')

<div class="order-page-wrapper py-5">
    <div class="container">
        <div class="text-center">
            <h2 class="text-success mt-4 fw-bold">
                <i class="fas fa-check-circle"></i> Order Placed Successfully!
            </h2>
            <p class="lead">Thank you for your purchase. Your order has been placed successfully.</p>
        </div>

        <!-- ✅ Order Details Card -->
        <div class="card shadow-sm mx-auto mt-4" style="max-width: 900px;">
            <div class="card-body p-4">
                <div id="order-details">
                    <p class="text-center">Loading order details...</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ url('/') }}" class="btn btn-primary mt-4">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let orderId = localStorage.getItem("order_id");

        console.log("🔍 Checking Order Success Page → Order ID:", orderId);

        // ✅ Redirect if order ID is missing
        if (!orderId) {
            alert("⚠️ No order details found! Redirecting to homepage...");
            window.location.href = "/"; 
            return;
        }

        // ✅ Fetch order details
        $.ajax({
            url: "/get-order",
            type: "GET",
            data: { order_id: orderId },
            success: function(response) {
                console.log("✅ Fetched Order Details:", response);

                if (!response || !response.order_id) {
                    $("#order-details").html("<p class='text-danger'>Order not found!</p>");
                    return;
                }

                let order = response;
                let productRows = order.products.map(product => `
                    <tr>
                        <td><img src="/products/${product.image}" width="50"></td>
                        <td>${product.name}</td>
                        <td>${product.sku}</td>
                        <td>₨. ${product.price}.00</td>
                        <td>${product.quantity}</td>
                        <td>₨. ${(product.price * product.quantity).toFixed(2)}</td>
                    </tr>
                `).join('');

                let orderHtml = `
                    <h3 class="card-title mb-3">Order Details</h3>
                    <h2 class="card-title mb-3"><strong>Order ID:</strong> ${order.order_id}</h2>
                    <hr class="mb-4">

                    <div class="row g-3">
                        <div class="col-md-7">
                            <h5><i class="bi bi-geo-alt-fill me-1"></i> Shipping Address</h5>
                            <p><strong>Name:</strong> ${order.name}</p>
                            <p><strong>Email:</strong> ${order.email}</p>
                            <p><strong>Mobile:</strong> ${order.mobile}</p>
                            <p><strong>Address:</strong> ${order.address}</p>
                            <p><strong>Shipping:</strong> ${order.shipping_method}</p>
                            <p><strong>Payment Method:</strong> ${order.payment_method}</p>
                        </div>
                        <div class="col-md-5">
                            <h5><i class="bi bi-receipt me-1"></i> Order Summary</h5>
                            <p><strong>Subtotal:</strong> ₨. ${order.subtotal.toFixed(2)}</p>
                            <p><strong>GST (18%):</strong> ₨. ${order.gst.toFixed(2)}</p>
                            <p><strong>Shipping Fee:</strong> ₨. ${order.shipping_fee.toFixed(2)}</p>
                            <p><strong>Payment Fee:</strong> ₨. ${order.payment_fee.toFixed(2)}</p>
                            <h5 class="fw-bold"><strong>Grand Total:</strong> ₨. ${order.grand_total.toFixed(2)}</h5>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h5><i class="bi bi-bag-check-fill me-1"></i> Ordered Products</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>${productRows}</tbody>
                    </table>
                `;

                $("#order-details").html(orderHtml);
            },
            error: function(xhr) {
                console.error("Error fetching order:", xhr.responseText);
            }
        });
    });
</script>

@endsection


