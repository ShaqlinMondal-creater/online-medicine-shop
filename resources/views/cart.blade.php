@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="cart_have_data">Cart Details</h2>

    <!-- ✅ Cart Message (Success/Error) -->
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
            <!-- Cart Items Will Be Loaded Here Using AJAX -->
        </tbody>
    </table>

</div>


<!-- ✅ Empty Cart Message (Initially Hidden) -->
<div id="empty-cart-message">
    @include('inc/empty_cart')
</div>
    
<div class="container">
    <!-- ✅ Cart Summary -->
    <div class="text-end stt cart_have_data">
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

    <div class="cart_have_data">
        <div class="cart_page_button">
            <a href="javascript:void(0);" class="btn btn-danger" id="clear-cart">Clear Cart</a>
            <a href="javascript:void(0);" class="btn btn-success" id="proceed-to-checkout">Proceed to Checkout 👍</a>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            loadCart(); // ✅ Load cart when page loads

            // ✅ Function to Load Cart Items Using AJAX
            function loadCart() {
                $.ajax({
                    url: "/get-cart",
                    type: "GET",
                    success: function (cartData) {
                        let cartItems = $("#cart-items");
                        let cartContainer = $(".cart_have_data"); // ✅ Cart Wrapper
                        let emptyCartMessage = $("#empty-cart-message"); // ✅ Empty Cart Message
                        let subtotal = 0;
                        cartItems.html("");

                        // ✅ Check if cart is empty
                        if (!Array.isArray(cartData) || cartData.length === 0 || !cartData[0].products || Object.keys(cartData[0].products).length === 0) {
                            cartContainer.hide(); // ✅ Hide cart details
                            emptyCartMessage.show(); // ✅ Show empty cart message
                            $("#subtotal").text("₨. 0.00");
                            $("#gst").text("₨. 0.00");
                            $("#grand-total").text("₨. 0.00");
                            updateCartCount(0);
                            return;
                        }

                        let cart = cartData[0]; // ✅ Fetch current user's cart
                        let productsArray = Object.values(cart.products); // ✅ Convert products object to an array

                        // ✅ Show cart data and hide empty cart message
                        cartContainer.show();
                        emptyCartMessage.hide();

                        productsArray.forEach(product => {
                            let totalPrice = product.quantity * product.price;
                            subtotal += totalPrice;

                            let row = `<tr>
                                <td><img src="/products/${product.image}" width="50" onerror="this.onerror=null; this.src='/products/default.jpg';"></td>
                                <td>${product.name}</td>
                                <td>${product.sku}</td>
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

                        // ✅ Calculate GST and Grand Total
                        let gstTax = subtotal * 0.18;
                        let grandTotal = subtotal + gstTax;

                        $("#subtotal").text(`₨. ${subtotal.toFixed(2)}`);
                        $("#gst").text(`₨. ${gstTax.toFixed(2)}`);
                        $("#grand-total").text(`₨. ${grandTotal.toFixed(2)}`);

                        // ✅ Update Cart Count in Navbar
                        updateCartCount(productsArray.length);
                    },
                    error: function (xhr) {
                        console.error("Error fetching cart:", xhr.responseText);
                    }
                });
            }


            // ✅ Show Cart Message for Only 5 Seconds
            function showMessage(message, type = "success") {
                let messageBox = $("#cart-message");
                messageBox.removeClass("alert-success alert-danger").addClass(`alert alert-${type}`).text(message).fadeIn();
                setTimeout(() => {
                    messageBox.fadeOut();
                }, 5000); // Hide after 5 seconds
            }

            // ✅ Update Quantity Using AJAX
            $(document).on("click", ".update-qty", function () {
                let productId = $(this).data("id");
                let action = $(this).data("action");

                $.ajax({
                    url: "/update-cart/" + productId + "/" + action,
                    type: "GET",
                    success: function (response) {
                        showMessage(response.message);
                        loadCart(); // Reload cart after quantity update
                    },
                    error: function (xhr) {
                        showMessage("Error updating cart!", "danger");
                    }
                });
            });

            // ✅ Remove Item from Cart Using AJAX
            $(document).on("click", ".remove-from-cart", function () {
                let productId = $(this).data("id");

                $.ajax({
                    url: "/remove-from-cart/" + productId,
                    type: "GET",
                    success: function (response) {
                        showMessage(response.message);
                        loadCart(); // ✅ Reload cart after item is removed
                    },
                    error: function (xhr) {
                        showMessage("Error removing item!", "danger");
                    }
                });
            });

            // ✅ Clear Entire Cart Using AJAX
            $("#clear-cart").click(function () {
                $.ajax({
                    url: "/clear-cart",
                    type: "GET",
                    success: function (response) {
                        showMessage(response.message);
                        loadCart(); // Reload cart after clearing
                    },
                    error: function (xhr) {
                        showMessage("Error clearing cart!", "danger");
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("proceed-to-checkout").addEventListener("click", function (event) {
                event.preventDefault(); // ✅ Prevent immediate redirection
    
                fetch("/auth-log")
                    .then(response => response.json())
                    .then(data => {
                        if (!Array.isArray(data)) {
                            console.error("Invalid auth response:", data);
                            return;
                        }
    
                        let loggedInUser = data.find(user => user.auth_id === "true");
    
                        if (loggedInUser) {
                            window.location.href = "/checkout";
                        } else {
                            window.location.href = "/login"; // ✅ Redirect to login if not authenticated
                        }
                    })
                    .catch(error => console.error("Error checking authentication:", error));
            });
        });
    </script>
    
    
@endsection

<style>
    .style{
        display: flex;
        justify-content: space-between;
        width:66vw;
    }
    .stt{
        border-bottom: 2px solid grey;
    }
    .cart_page_button{
        display: flex;
        justify-content: space-between;
        margin: 15px;
    }
    .cart_have_data, .cart-message, .empty-cart-message{
        display:none;
    }
    empty-cart-message{
        display:none;
    }

</style>
