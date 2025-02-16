@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<div class="container">
    <h2 class="cart_have_data">Your Shopping Cart</h2>

    <!-- ✅ Cart Message -->
    <div id="cart-message" class="alert" style="display: none;"></div>

    <table class="table cart_have_data">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="cart-items">
            <!-- ✅ Cart Items Will Be Loaded Here -->
        </tbody>
    </table>

    <!-- ✅ Empty Cart Message -->
    <div id="empty-cart-message" style="display: none;">
        @include('inc/empty_cart')
        {{-- <h4>Your cart is empty.</h4>
        <a href="/" class="btn btn-primary">Continue Shopping</a> --}}
    </div>

    <!-- ✅ Cart Summary -->
    <div class="text-end cart_have_data">
        <p class="style">
            <span>Subtotal: </span> 
            <span id="subtotal">₨. 0.00</span>
        </p>
        <p class="style">
            <span>GST (18%): </span> 
            <span id="gst">₨. 0.00</span>
        </p>
        <h5 class="style">
            <span>Grand Total: </span>
            <strong><span id="grand-total">₨. 0.00</span></strong>
        </h5>
    </div>

    <!-- ✅ Cart Buttons -->
    <div class="cart_have_data">
        <a href="javascript:void(0);" class="btn btn-danger" id="clear-cart">Clear Cart</a>
        <a href="javascript:void(0);" class="btn btn-success" id="proceed-to-checkout">Proceed to Checkout 👍</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        loadCart(); // ✅ Load cart when page loads

        function getUserId() {
            let user = JSON.parse(localStorage.getItem("user"));
            let guestId = localStorage.getItem("guest_id");
            return user ? String(user.id) : guestId;
        }

        function loadCart() {
            let currentUserId = getUserId();
            console.log("Fetching cart for User ID:", currentUserId);

            $.ajax({
                url: "/get-cart",
                type: "GET",
                headers: { "X-User-ID": currentUserId },
                success: function (cartData) {
                    let cartItems = $("#cart-items");
                    let cartContainer = $(".cart_have_data");
                    let emptyCartMessage = $("#empty-cart-message");
                    let subtotal = 0;
                    cartItems.html("");

                    console.log("Fetched Cart Data:", cartData);

                    let matchedCart = cartData.find(cart => String(cart.user_id) === currentUserId);

                    if (!matchedCart || !matchedCart.products || matchedCart.products.length === 0) {
                        cartContainer.hide();
                        emptyCartMessage.show();
                        $("#subtotal, #gst, #grand-total").text("₨. 0.00");
                        return;
                    }

                    localStorage.setItem("cart_id", matchedCart.cart_id);
                    cartContainer.show();
                    emptyCartMessage.hide();

                    matchedCart.products.forEach(product => {
                        let totalPrice = product.quantity * product.price;
                        subtotal += totalPrice;

                        let row = `<tr>
                            <td><img src="/products/${product.image || 'default.jpg'}" width="50"></td>
                            <td>${product.name}</td>
                            <td>${product.sku || 'N/A'}</td>
                            <td>₨. ${product.price}.00</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary update-qty" data-id="${product.product_id}" data-action="increase">+</button>
                                <span class="mx-2">${product.quantity}</span>
                                <button class="btn btn-sm btn-outline-primary update-qty" data-id="${product.product_id}" data-action="decrease">-</button>
                            </td>
                            <td>₨. ${totalPrice}.00</td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm remove-from-cart" data-id="${product.product_id}">Remove</a>
                            </td>
                        </tr>`;
                        cartItems.append(row);
                    });

                    let gstTax = subtotal * 0.18;
                    let grandTotal = subtotal + gstTax;

                    $("#subtotal").text(`₨. ${subtotal.toFixed(2)}`);
                    $("#gst").text(`₨. ${gstTax.toFixed(2)}`);
                    $("#grand-total").text(`₨. ${grandTotal.toFixed(2)}`);
                },
                error: function (xhr) {
                    console.error("Error fetching cart:", xhr.responseText);
                }
            });
        }

        $(document).on("click", ".remove-from-cart", function () {
            let productId = $(this).data("id");
            let userId = getUserId();

            $.ajax({
                url: "/remove-from-cart/" + productId,
                type: "GET",
                headers: { "X-User-ID": userId },
                success: function (response) {
                    alert(response.message);
                    loadCart();
                },
                error: function (xhr) {
                    console.error("Error removing item:", xhr.responseText);
                }
            });
        });

        $(document).on("click", ".update-qty", function () {
            let productId = $(this).data("id");
            let action = $(this).data("action");
            let userId = getUserId();

            $.ajax({
                url: "/update-cart/" + productId + "/" + action,
                type: "GET",
                headers: { "X-User-ID": userId },
                success: function (response) {
                    alert(response.message);
                    loadCart();
                },
                error: function (xhr) {
                    console.error("Error updating cart:", xhr.responseText);
                }
            });
        });

        $("#clear-cart").click(function () {
            let userId = getUserId();

            $.ajax({
                url: "/clear-cart",
                type: "GET",
                headers: { "X-User-ID": userId },
                success: function (response) {
                    alert(response.message);
                    loadCart();
                },
                error: function (xhr) {
                    console.error("Error clearing cart:", xhr.responseText);
                }
            });
        });
        
        $("#proceed-to-checkout").click(function (event) {
        event.preventDefault();

        let cartId = localStorage.getItem("cart_id");
        let userId = localStorage.getItem("user_id");

        console.log("🔍 Proceeding to checkout → Cart ID:", cartId, "User ID:", userId);

        if (!cartId || cartId === "null") {
            alert("⚠️ No Cart ID found! Redirecting to cart...");
            window.location.href = "/cart";
            return;
        }

        if (!userId || userId.startsWith("guest_")) {
            alert("⚠️ You need to register before proceeding.");
            window.location.href = "/register";
            return;
        }

        window.location.href = `/proceed-checkout?cart_id=${cartId}`;
    });

              
    });
</script>

<script>
    // ✅ Proceed to Checkout
        // $("#proceed-to-checkout").click(function () {
        //     let user = JSON.parse(localStorage.getItem("user"));
        //     let guestId = localStorage.getItem("guest_id");
        //     let currentUserId = user ? user.id : guestId;

        //     $.ajax({
        //         url: "/auth-log",
        //         type: "GET",
        //         success: function (authData) {
        //             let loggedInUser = authData.find(user => user.user_id === currentUserId);
        //             if (loggedInUser) {
        //                 window.location.href = "/checkout";
        //             } else {
        //                 window.location.href = "/login";
        //             }
        //         },
        //         error: function (xhr) {
        //             console.error("Error checking authentication:", xhr.responseText);
        //         }
        //     });
        // });  
</script>











@endsection
