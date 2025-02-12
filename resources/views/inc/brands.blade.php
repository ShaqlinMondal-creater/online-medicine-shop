<div class=" mt-5 brands">
    <h3 class="text-center brand_head">Our Brands</h3>
    <div id="brandCarousel" class="carousel slide mt-3" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner" id="brandContainer">
            <!-- Brands will be loaded dynamically here -->
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#brandCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#brandCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/brands')
    .then(response => response.json())
    .then(brands => {
        let brandContainer = document.getElementById('brandContainer');
        brandContainer.innerHTML = ""; // Clear existing content

        let activeClass = "active"; // First slide should be active
        let groupedBrands = []; // Store grouped brands for each slide

        for (let i = 0; i < brands.length; i += 5) {
            let group = brands.slice(i, i + 5); // Group 3 brands per slide
            groupedBrands.push(group);
        }

        groupedBrands.forEach((group, index) => {
            let brandCards = group.map(brand => `
                <div class="col-md-3 text-center">
                    <div class="vv">
                        <img src="${brand.image}" class="img-fluid rounded" alt="${brand.name}" style="max-width: 100px;">
                    </div>
                    <h5 class="mt-2">${brand.name}</h5>
                </div>
            `).join('');

            let slide = `
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                    <div class="row justify-content-center">
                        ${brandCards}
                    </div>
                </div>
            `;

            brandContainer.innerHTML += slide;
        });

        // ✅ Enable automatic sliding
        let brandCarousel = new bootstrap.Carousel(document.getElementById('brandCarousel'), {
            interval: 3000, // Change slide every 3 seconds
            ride: "carousel"
        });
    })
    .catch(error => console.error('Error fetching brands:', error));
});
</script>
<style>
    .vv{
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 150px;
    }
    .vv img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>