<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Include Font Awesome 5+ (CDN) -->
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css"
  />

    <!-- ✅ Link Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #authLinks{
            display: flex;
        }
        .div{
            display: flex;
            flex-direction: column;
            /* justify-content: space-between; */
            /* gap: 50px; */
            min-width: 100vw;
            min-height: 100vh;
        }
        footer{
            position: relative;
            bottom: 0%;
            width: 100%;

        }
    </style>
</head>
<body>
    <div class="div">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="/">ALPHA</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="/shop">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                        <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
        
                        <!-- ✅ Dynamic Authentication Links -->
                        <li class="nav-item" id="authLinks">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    
        {{-- <div class="container mt-4"> --}}
        <div class="contents">
            @yield('content')
        </div>
    
        @include('inc.footer')
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let token = localStorage.getItem('token');
            let userRole = localStorage.getItem('user_role');
            let authLinks = document.getElementById('authLinks');
    
            if (token) {
                if (userRole === 'admin') {
                    authLinks.innerHTML = `
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/dashboard"><i class="fas fa-user-shield"></i> Admin Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link logout-btn" href="javascript:void(0);" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    `;
                } else {
                    authLinks.innerHTML = `
                        <li class="nav-item">
                            <a class="nav-link" href="/cart">
                                <i class="fas fa-shopping-cart"></i> Cart <span id="cart-count" class="badge bg-danger">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link logout-btn" href="javascript:void(0);" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    `;
    
                    fetchCartCount(); // ✅ Fetch cart count when the page loads
                }
            } else {
                authLinks.innerHTML = `
                    <li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                `;
                updateCartCount(0); // ✅ Show "0" if user is not logged in
            }
        });
    
        // ✅ Fetch Cart Count Function (Now Counts Unique Products)
        function fetchCartCount() {
            fetch('/get-cart')
                .then(response => response.json())
                .then(cartData => {
                    let cartCount = cartData.length > 0 ? cartData[0].products.length : 0; // ✅ Count total unique products
                    updateCartCount(cartCount);
                })
                .catch(error => console.error('Error fetching cart count:', error));
        }
    
        // ✅ Function to Update Cart Count in Navbar
        function updateCartCount(count) {
            let cartCountSpan = document.getElementById('cart-count');
            if (cartCountSpan) {
                cartCountSpan.textContent = count;
            }
        }
    
        // ✅ Auto-update cart count when a product is added/removed
        function updateCart() {
            fetchCartCount(); // ✅ Refresh cart count without page reload
        }
    </script>
       
        
    <script>
        function logout() {
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
            fetch('/logout', {
                method: 'POST',
                credentials: 'include', // ✅ Ensure session cookies are included
                headers: {
                    'X-CSRF-TOKEN': csrfToken, // ✅ Include CSRF Token
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({}) // ✅ Empty body
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message); });
                }
                return response.json();
            })
            .then(data => {
                console.log(data.message); // ✅ Debugging message
    
                // ✅ Clear frontend localStorage
                localStorage.removeItem('token');
                localStorage.removeItem('user_role');
    
                // ✅ Redirect to login page
                window.location.replace('/');
            })
            .catch(error => {
                console.error('Logout failed:', error);
                alert('Error logging out: ' + error.message);
            });
        }
    </script>
    
    <script>
        function checkAuth() {
            fetch('/auth-log')
            .then(response => response.json())
            .then(data => {
                let authLinks = document.getElementById('authLinks');
                
                let loggedInUser = data.find(user => user.auth_id === "true");
    
                if (loggedInUser) {
                    authLinks.innerHTML = `
                        <li class="nav-item"><a class="nav-link" href="/cart">Cart</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" onclick="logout()">Logout</a></li>
                    `;
                } else {
                    authLinks.innerHTML = `<a class="nav-link" href="/login">Login</a>`;
                }
            })
            .catch(error => console.error('Error fetching auth status:', error));
        }
    
        document.addEventListener("DOMContentLoaded", checkAuth);
    </script>
    
        
    <!-- ✅ Add jQuery & Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
