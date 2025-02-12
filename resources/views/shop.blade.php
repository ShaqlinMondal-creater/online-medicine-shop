@extends('layouts.app')

@section('title', 'Shop')

@section('content')
<!-- ✅ Navbar -->

<div class="container">
    <h2 class="text-center my-4"><u>Shop Now</u></h2>

    <div class="row" id="productContainer">
        <!-- Products will be loaded here dynamically -->
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('/api/products')
        .then(response => response.json())
        .then(products => {
            let productContainer = document.getElementById('productContainer');
            productContainer.innerHTML = ""; // Clear previous content

            
            products.forEach((product, index) => {
                let truncatedName = product.name.length > 25 ? product.name.substring(0, 20) + '...' : product.name;

                let productCard = `
                    <div class="col-md-3 mb-4">
                        <div class="card shadow">
                            <div class="card-img">
                                <img src="/products/${product.image || 'default.jpg'}" class="card-img-top" alt="${product.name}">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${truncatedName}</h5>
                                <div class="details">  
                                    <p class="card-brand">${product.brand}</p>
                                    <p class="card-text">₨. ${product.price} .00</p>
                                </div>
                                <div class="btnns">
                                    <button class="btn btn-primary w-100 add" onclick="addToCart(${index})">Add to Cart</button>
                                    <button class="btn btn-warning w-100 buy" onclick="buyNow(${index})">Buy Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                productContainer.innerHTML += productCard;
            });
        })
        .catch(error => console.error('Error fetching products:', error));
    });

    function addToCart(index) {
        alert("Product added to cart! (Index: " + index + ")");
        // Logic to add the product to cart will be implemented here
    }

    function buyNow(index) {
        alert("Proceeding to checkout! (Index: " + index + ")");
        // Logic to handle buying process will be implemented here
    }
</script>

@endsection
