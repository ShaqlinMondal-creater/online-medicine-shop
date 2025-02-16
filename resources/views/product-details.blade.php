@extends('layouts.app')

@section('title', $product['name'])

<style>
.detail {
    margin-bottom: 180px;
}
</style>

@section('content')
<div class="container detail">
    <div class="row mt-5">
        <div class="col-md-6 detail_image">
            <img src="/products/{{ $product['image'] ?? 'default.jpg' }}" class="img-fluid rounded" alt="{{ $product['name'] }}">
        </div>
        <div class="col-md-6">
            <div class="right">
                <h2>{{ $product['name'] }}</h2>
                <h4>SKU: {{ $product['sku'] }}</h4>
                <p><strong>Brands:</strong> {{ $product['brand'] }}</p>
                <p><strong>Category:</strong> {{ $product['category'] }}</p>
                <p><strong>Description:</strong> {{ $product['description'] ?? 'No description available' }}</p>
                <div class="price">
                    <p>₨. <strong>{{ $product['price'] }}.00</strong> /-</p>
                </div>
                <button class="btn btn-primary add-to-cart add" data-id="{{ $product['id'] }}">Add to Cart</button>
                <button class="btn btn-warning buy-now buy" data-id="{{ $product['id'] }}">Buy Now</button>
            </div>
            <div class="offer_stock">
                <p class="stock mt-3"><strong>Stock:</strong> {{ $product['stock'] }} <span class="alert">Available!</span></p>
                <div class="discount">
                    UPTO {{ $product['discount'] }} % off
                </div>
            </div>
        </div>
    </div>

    <br><br>
    <h3 class="mt-5 text-center">Related Products</h3>
    <div id="relatedProductsCarousel" class="carousel slide mt-3" data-bs-ride="carousel">
        <div class="carousel-inner" id="relatedProductsContainer">
            <!-- Related Products will be loaded dynamically here -->
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#relatedProductsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#relatedProductsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
@endsection

<!-- ✅ jQuery CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let user = JSON.parse(localStorage.getItem("user"));
        let cartId = localStorage.getItem("cart_id");
        let guestId = localStorage.getItem("guest_id");
        let guestCartId = localStorage.getItem("guest_cart_id");

        if (user) {
            if (!cartId) {
                cartId = null; // The backend will generate it
                localStorage.setItem("cart_id", cartId);
            }
        } else {
            if (!guestId) {
                guestId = generateGuestId();
                localStorage.setItem("guest_id", guestId);
            }
            if (!guestCartId) {
                guestCartId = null; // The backend will generate it
                localStorage.setItem("guest_cart_id", guestCartId);
            }
        }

        function generateGuestId() {
            return "guest_" + Date.now() + "_" + Math.random().toString(36).substr(2, 6);
        }

        function getAuthHeaders() {
            let headers = { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": csrfToken 
            };
            if (cartId) {
                headers["X-Cart-ID"] = cartId;
                headers["X-User-ID"] = user.id;
            } else if (guestCartId) {
                headers["X-Cart-ID"] = guestCartId;
                headers["X-User-ID"] = guestId;
            }
            return headers;
        }

        $(".add-to-cart").click(function () {
            let productId = $(this).attr("data-id");

            $.ajax({
                url: "/add-to-cart/" + productId,
                type: "POST",
                headers: getAuthHeaders(),
                success: function (response) {
                    alert(response.message);

                    // ✅ Store `cart_id` in localStorage for users and guests
                    if (user && response.cart_id) {
                        localStorage.setItem("cart_id", response.cart_id);
                    } else if (!user && response.cart_id) {
                        localStorage.setItem("guest_cart_id", response.cart_id);
                    }
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseText);
                }
            });
        });

        $(".buy-now").click(function () {
            let productId = $(this).data("id");

            $.ajax({
                url: "/add-to-cart/" + productId,
                type: "POST",
                headers: getAuthHeaders(),
                success: function (response) {
                    if (response.message) {
                        console.log(response.message);
                        window.location.href = "/cart";
                    } else {
                        alert("Error adding product to cart!");
                    }
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseText);
                }
            });
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentProductId = window.location.pathname.split("/").pop();

        fetch('/api/products')
        .then(response => response.json())
        .then(products => {
            let relatedProductsContainer = document.getElementById('relatedProductsContainer');
            relatedProductsContainer.innerHTML = ""; // Clear existing content

            // ✅ Get related products (excluding current product)
            let relatedProducts = products.filter(p => p.id != currentProductId).slice(0, 10);

            let groupedProducts = []; // Store grouped products for each slide

            for (let i = 0; i < relatedProducts.length; i += 4) {
                let group = relatedProducts.slice(i, i + 4);
                groupedProducts.push(group);
            }

            groupedProducts.forEach((group, index) => {
                let productCards = group.map(product => `
                    <div class="col-md-3">
                        <div class="card shadow">
                            <div class="card-img">
                                <img src="/products/${product.image || 'default.png'}" class="card-img-top" alt="${product.name}">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <div class="details">  
                                    <p class="card-brand">${product.brand}</p>
                                    <p class="card-text">₨. ${product.price}.00</p>
                                </div>
                                <a href="/product/${product.id}" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                `).join('');

                let slide = `
                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                        <div class="row justify-content-center">
                            ${productCards}
                        </div>
                    </div>
                `;

                relatedProductsContainer.innerHTML += slide;
            });
        })
        .catch(error => console.error('Error fetching related products:', error));
    });
</script>
