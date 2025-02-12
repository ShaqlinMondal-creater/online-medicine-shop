@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2 class="text-success">🎉 Order Placed Successfully! 🎉</h2>
    <p>Thank you for shopping with us. Your order has been placed successfully.</p>

    <div class="card mx-auto mt-4" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title">Order Details</h5>
            <p><strong>Order ID:</strong> <span id="order-id"></span></p>
            <p><strong>Payment Method:</strong> <span id="payment-method"></span></p>
            <p><strong>Shipping Type:</strong> <span id="shipping-type"></span></p>
            <p><strong>Delivery Address:</strong> <span id="address"></span></p>
            <hr>
            <h4>Total: <strong>₨. <span id="grand-total">0.00</span></strong></h4>
        </div>
    </div>

    <a href="/" class="btn btn-primary mt-3">🏠 Go to Home</a>
    <a href="/my-orders" class="btn btn-success mt-3">📦 View Orders</a>
</div>

<script>
    $(document).ready(function () {
        let orderData = localStorage.getItem('latestOrder');
        
        if (orderData) {
            orderData = JSON.parse(orderData);
            $("#order-id").text(orderData.order_id);
            $("#payment-method").text(orderData.payment_method);
            $("#shipping-type").text(orderData.shipping_type);
            $("#address").text(orderData.address);
            $("#grand-total").text(orderData.total.toFixed(2));

            // ✅ Clear local storage after showing the order summary
            localStorage.removeItem('latestOrder');
        } else {
            $("body").html("<h2 class='text-center text-danger'>No order found! 🚫</h2><p class='text-center'><a href='/'>Go back to Home</a></p>");
        }
    });
</script>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
