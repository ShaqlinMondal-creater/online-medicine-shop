<div class="container mt-5 feature_products">
    <h2 class="text-center feature_head">Featured Products</h2>
    <div class="row" id="featuredProductsContainer">
        <!-- Products will be loaded dynamically here -->
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('/api/products')
        .then(response => response.json())
        .then(products => {
            let featuredProductsContainer = document.getElementById('featuredProductsContainer');
            featuredProductsContainer.innerHTML = ""; // Clear previous content

            // ✅ Show only the first 8 products
            let displayedProducts = products.slice(0, 10);
            
            displayedProducts.forEach((product, index) => {
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
                                    <p class="card-text">₨. ${product.price}.00</p>
                                </div>
                                
                                <a href="/product/${index}" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                `;
                featuredProductsContainer.innerHTML += productCard;
            });
        })
        .catch(error => console.error('Error fetching products:', error));
    });
</script>
