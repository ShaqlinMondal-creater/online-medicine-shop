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
    
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        checkAuth(); // ✅ Check authentication when the page loads
    });

    function checkAuth() {
        fetch('/auth-log')
            .then(response => response.json())
            .then(data => {
                let authLinks = document.getElementById('authLinks');
                authLinks.innerHTML = ''; // Clear existing content

                // ✅ Find the currently authenticated user (auth_token !== null)
                let loggedInUsers = Object.values(data).filter(user => user.auth_token !== null);

                if (loggedInUsers.length > 0) {
                    let user = loggedInUsers[0]; // ✅ Assuming only one logged-in user per session
                    let userName = user.user.name;
                    let userRole = user.user.role;
                    let authToken = user.auth_token; // ✅ Use auth_token

                    // ✅ Store user authentication data in localStorage
                    localStorage.setItem('auth_token', authToken);
                    localStorage.setItem('user_role', userRole);
                    localStorage.setItem('user', JSON.stringify(user.user));

                    if (userRole === 'admin') {
                        authLinks.innerHTML = `
                            <li class="nav-item"><a class="nav-link" href="/admin/dashboard"><i class="fas fa-user-shield"></i> Admin Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link logout-btn" href="javascript:void(0);" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        `;
                    } else {
                        authLinks.innerHTML = `
                            <li class="nav-item"><a class="nav-link">Hello, ${userName}</a></li>
                            <li class="nav-item"><a class="nav-link" href="/cart"><i class="fas fa-shopping-cart"></i> Cart <span id="cart-count" class="badge bg-danger">0</span></a></li>
                            <li class="nav-item"><a class="nav-link logout-btn" href="javascript:void(0);" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        `;
                        fetchCartCount(); // ✅ Fetch cart count when user is logged in
                    }
                } else {
                    // ✅ If no user is logged in, show the login link
                    authLinks.innerHTML = `<li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Login</a></li>`;
                    updateCartCount(0); // ✅ Reset cart count to 0
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_role');
                    localStorage.removeItem('user');
                }
            })
            .catch(error => console.error('Error fetching auth status:', error));
    }

    // ✅ Fetch Cart Count Function
    function fetchCartCount() {
        fetch('/get-cart')
            .then(response => response.json())
            .then(cartData => {
                let cartCount = cartData.length > 0 ? cartData[0].products.length : 0;
                updateCartCount(cartCount);
            })
            .catch(error => console.error('Error fetching cart count:', error));
    }

    // ✅ Update Cart Count in Navbar
    function updateCartCount(count) {
        let cartCountSpan = document.getElementById('cart-count');
        if (cartCountSpan) {
            cartCountSpan.textContent = count;
        }
    }

    // ✅ Logout Function
    function logout() {
        let user = JSON.parse(localStorage.getItem('user'));
        let userId = user ? user.id : null; 

        if (!userId) {
            console.error('User ID not found in localStorage.');
            return;
        }

        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId }) 
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message); });
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message);

            // ✅ Clear localStorage
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
            localStorage.removeItem('user');

            // ✅ Refresh UI and redirect
            checkAuth();
            window.location.replace('/login');
        })
        .catch(error => {
            console.error('Logout failed:', error);
            alert('Error logging out: ' + error.message);
        });
    }
</script>
    
<script>
    function logout() {
        let user = JSON.parse(localStorage.getItem('user'));
        let userId = user ? user.id : null; 

        if (!userId) {
            console.error('User ID not found in localStorage.');
            return;
        }

        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId }) 
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message); });
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message);

            // ✅ Clear localStorage
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
            localStorage.removeItem('user');

            // ✅ Refresh UI and redirect
            checkAuth();
            window.location.replace('/login');
        })
        .catch(error => {
            console.error('Logout failed:', error);
            alert('Error logging out: ' + error.message);
        });
    }
</script> --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        checkAuth(); // ✅ Check authentication when the page loads
    });

    function checkAuth() {
        fetch('/auth-log')
            .then(response => response.json())
            .then(data => {
                let authLinks = document.getElementById('authLinks');
                authLinks.innerHTML = ''; // Clear existing content

                // ✅ Retrieve stored auth_token and user_id from localStorage
                let storedAuthToken = localStorage.getItem('auth_token');
                let storedUser = JSON.parse(localStorage.getItem('user'));

                if (!storedAuthToken || !storedUser) {
                    // ✅ If no valid session, reset UI and localStorage
                    authLinks.innerHTML = `<li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Login</a></li>`;
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_role');
                    localStorage.removeItem('user');
                    return;
                }

                // ✅ Find the user session from auth.json with matching auth_token and user_id
                let activeUser = Object.values(data).find(user =>
                    user.auth_token === storedAuthToken &&
                    user.user.id === storedUser.id
                );

                if (activeUser) {
                    let userId = activeUser.user.id;
                    let userName = activeUser.user.name;
                    let userRole = activeUser.user.role;
                    let authToken = activeUser.auth_token;

                    // ✅ Update localStorage to ensure consistency
                    localStorage.setItem('auth_token', authToken);
                    localStorage.setItem('user_role', userRole);
                    localStorage.setItem('user', JSON.stringify(activeUser.user));

                    if (userRole === 'admin') {
                        authLinks.innerHTML = `
                            <li class="nav-item"><a class="nav-link" href="/admin/dashboard"><i class="fas fa-user-shield"></i> Admin Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link logout-btn" href="javascript:void(0);" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        `;
                    } else {
                        authLinks.innerHTML = `
                            <li class="nav-item"><a class="nav-link" href="/profile/${userId}">Hello, ${userName}</a></li>
                            <li class="nav-item"><a class="nav-link" href="/cart"><i class="fas fa-shopping-cart"></i> Cart <span id="cart-count" class="badge bg-danger">0</span></a></li>
                            <li class="nav-item"><a class="nav-link logout-btn" href="javascript:void(0);" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        `;
                        fetchCartCount(); // ✅ Fetch cart count when user is logged in
                    }
                } else {
                    // ✅ No valid session found, reset UI and clear localStorage
                    authLinks.innerHTML = `<li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Login</a></li>`;
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_role');
                    localStorage.removeItem('user');
                }
            })
            .catch(error => console.error('Error fetching auth status:', error));
    }

    // ✅ Fetch Cart Count Function
    // ✅ Fetch Unique Product Count in Cart
function fetchCartCount() {
    let user = JSON.parse(localStorage.getItem("user"));
    let guestId = localStorage.getItem("guest_id");
    let userId = user ? user.id : guestId; // Get user ID (logged-in or guest)

    if (!userId) {
        console.warn("No user ID found. Cart count set to 0.");
        updateCartCount(0);
        return;
    }

    console.log("🔍 Fetching Cart Count for User ID:", userId);

    fetch('/get-cart-count', {
        method: "GET",
        headers: { "X-User-ID": userId }
    })
    .then(response => response.json())
    .then(data => {
        let cartCount = data.cart_count || 0;
        console.log("🛒 Unique Products in Cart:", cartCount);
        updateCartCount(cartCount);
    })
    .catch(error => {
        console.error("Error fetching cart count:", error);
        updateCartCount(0); // ✅ Set count to 0 if an error occurs
    });
}

// ✅ Update Cart Count in UI
function updateCartCount(count) {
    let cartCountElement = document.getElementById("cart-count");

    if (!cartCountElement) {
        console.warn("Cart count element not found.");
        return;
    }

    if (count > 0) {
        cartCountElement.innerText = count;
        cartCountElement.style.display = "inline-block";
    } else {
        cartCountElement.innerText = "0"; // ✅ Show 0 if no products in cart
        cartCountElement.style.display = "inline-block";
    }
}

// ✅ Fetch cart count when the page loads
document.addEventListener("DOMContentLoaded", function() {
    fetchCartCount();
});


    // ✅ Logout Function
    function logout() {
        let user = JSON.parse(localStorage.getItem('user'));
        let userId = user ? user.id : null; 

        if (!userId) {
            console.error('User ID not found in localStorage.');
            return;
        }

        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId }) 
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message); });
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message);

            // ✅ Clear localStorage
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
            localStorage.removeItem('user');

            // ✅ Refresh UI and redirect
            checkAuth();
            window.location.replace('/login');

        })
        .catch(error => {
            console.error('Logout failed:', error);
            alert('Error logging out: ' + error.message);
        });
    }
</script>

    
    
        
    <!-- ✅ Add jQuery & Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
