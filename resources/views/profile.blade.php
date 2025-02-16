@extends('layouts.app')

@section('content')
<div class="container profile_page">
    <h2 class="mt-4">{{ $user['name'] }} Profile</h2>

    <div class="card shadow-sm p-4">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="/{{ $user['image'] ?? 'users/default.jpg' }}" class="img-thumbnail" alt="{{ $user['name'] }}">
                <h4 class="mt-3">{{ $user['name'] }}</h4>
                <p><strong>Email:</strong> {{ $user['email'] }}</p>
                <p><strong>Mobile:</strong> {{ $user['phone'] }}</p>
                <p><strong>Role:</strong> {{ ucfirst($user['role']) }}</p>
                <a href="/logout" class="btn btn-danger">Logout</a>
            </div>

            <div class="col-md-8 right-box">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#account">Account Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders">Orders</a>
                    </li>
                </ul>

                <div class="tab-content p-3">
                    <!-- Account Details Tab -->
                    <div class="tab-pane fade show active" id="account">
                        <h5>Account Information</h5>
                        <p><strong>Name:</strong> {{ $user['name'] }}</p>
                        <p><strong>Email:</strong> {{ $user['email'] }}</p>
                        <p><strong>Phone:</strong> {{ $user['phone'] }}</p>
                        <p><strong>Role:</strong> {{ ucfirst($user['role']) }}</p>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders">
                        <h5>Order History</h5>
                        <div id="order-list">
                            <p>Loading orders...</p> <!-- Orders will load dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let userId = localStorage.getItem("user_id");
        console.log("Fetching orders for User ID:", userId);

        if (!userId) {
            alert("⚠️ User ID missing! Redirecting to login...");
            window.location.href = "/login";
            return;
        }

        // Fetch Orders for the User
        fetch(`/orders/${userId}`)
            .then(response => response.json())
            .then(data => {
                let orderList = document.getElementById("order-list");
                orderList.innerHTML = ""; // Clear previous content

                if (data.length === 0) {
                    orderList.innerHTML = "<p>No orders found.</p>";
                    return;
                }

                data.forEach(order => {
                    let orderCard = `
                        <div class="card shadow-sm mb-3 p-3">
                            <h6><strong>Order ID:</strong> ${order.order_id}</h6>
                            <p><strong>Date:</strong> ${order.date}</p>
                            <p><strong>Total:</strong> ₹${order.grand_total.toFixed(2)}</p>
                            <p><strong>Status:</strong> ${order.status}</p>
                            <a href="/order-details/${order.order_id}" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    `;
                    orderList.innerHTML += orderCard;
                });
            })
            .catch(error => {
                console.error("Error fetching orders:", error);
                document.getElementById("order-list").innerHTML = "<p>Error loading orders.</p>";
            });
    });
</script>
<style>
    .profile_page .nav-tabs{
        background: cadetblue;
        border-radius: 30px;
        display: flex;
        gap: 5px;
    }
    .profile_page .nav-item{
        margin: 5px 5px;
    }
    .profile_page .nav-link{
        border-radius: 30px;
        font-size: larger;
        font-family: monospace;
        font-weight: 700;
    }
    .right-box{
        background: radial-gradient(#86b7fe, transparent);
        padding: 5px 0px;
    }
</style>
@endsection
