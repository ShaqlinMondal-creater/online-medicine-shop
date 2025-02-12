@extends('layouts.app')

@section('title', 'Admin Dashboard')
<style>
footer{
        position: fixed !important;
        bottom: 0%;
        width: 100%;

    }
</style>
@section('content')
<div class="container">
    <h2 class="text-center">Admin Dashboard</h2>
    <button onclick="logout()" class="btn btn-danger">Logout</button>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary" onclick="openTablePage('users')">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h3 id="userCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success" onclick="openTablePage('products')">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <h3 id="productCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning" onclick="openTablePage('orders')">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <h3 id="orderCount">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('/api/admin/stats', {
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('userCount').innerText = data.users || 0;
            document.getElementById('productCount').innerText = data.products || 0;
            document.getElementById('orderCount').innerText = data.orders || 0;
        })
        .catch(error => {
            console.error('Error fetching stats:', error);
        });
    });

    function openTablePage(type) {
        window.location.href = `/admin/all-data?type=${type}`;
    }
</script>
@endsection
