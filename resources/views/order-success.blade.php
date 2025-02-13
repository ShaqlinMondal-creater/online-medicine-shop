@extends('layouts.app')

@section('content')

<div class="container text-center">
    <h2 class="text-success mt-4">
        <i class="fas fa-check-circle"></i> Order Placed Successfully!
    </h2>
    <p class="lead">Thank you for your purchase. Your order has been placed successfully.</p>

    <!-- Card containing order details -->
    <div class="card shadow-sm mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="card-title">Order Details</h4>
            <hr>

            @if(session('order_details'))
                @php
                    $order = session('order_details');
                @endphp

                <!-- Basic Order Info -->
                <p><strong>Order ID:</strong> {{ $order['order_id'] }}</p>
                <p><strong>Name:</strong> {{ $order['name'] }}</p>
                <p><strong>Email:</strong> {{ $order['email'] }}</p>
                <p><strong>Mobile:</strong> {{ $order['mobile'] }}</p>
                <p><strong>Address:</strong> {{ $order['address'] }}</p>
                <p><strong>Shipping:</strong> {{ ucfirst($order['shipping']) }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($order['payment']) }}</p>

                <hr>
                <!-- Order Summary -->
                <h5>Order Summary</h5>
                <p><strong>Subtotal:</strong> ₨{{ number_format($order['subtotal'], 2) }}</p>
                <p><strong>Tax (18%):</strong> ₨{{ number_format($order['tax'], 2) }}</p>
                <p><strong>Shipping Fee:</strong> ₨{{ number_format($order['shipping_fee'], 2) }}</p>
                <p><strong>Payment Fee:</strong> ₨{{ number_format($order['payment_fee'], 2) }}</p>
                <h4>
                    <strong>Grand Total:</strong> ₨{{ number_format($order['grand_total'], 2) }}
                </h4>

                <hr>
                <!-- Ordered Products -->
                <h5>Ordered Products</h5>
                <ul class="list-group">
                    @foreach($order['products'] as $product)
                        <li class="list-group-item">
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
            @else
                <!-- No Order Data Found -->
                <p class="text-danger">Order details not found!</p>
            @endif

            <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

@endsection
