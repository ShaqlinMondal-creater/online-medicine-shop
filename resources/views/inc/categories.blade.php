<div class="container mt-5 categories_section">
    <h3 class="text-center">Popular Categories</h3>
    <div class="row mt-3" id="categoriesContainer">
        <!-- Categories will be loaded dynamically here -->
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/categories')
    .then(response => response.json())
    .then(categories => {
        let categoriesContainer = document.getElementById('categoriesContainer');
        categoriesContainer.innerHTML = ""; // Clear existing content

        categories.forEach(category => {
            let categoryCard = `
                <div class="col-md-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-img">
                            <img src="categories/${category.image}" class="card-img-top p-3" alt="${category.name}" style="max-height: 150px; object-fit: contain;">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title category_title">${category.name}</h5>
                            <a href="/shop?category=${category.id}" class="btn btn-primary">View Products</a>
                        </div>
                    </div>
                </div>
            `;
            categoriesContainer.innerHTML += categoryCard;
        });
    })
    .catch(error => console.error('Error fetching categories:', error));
});
</script>
