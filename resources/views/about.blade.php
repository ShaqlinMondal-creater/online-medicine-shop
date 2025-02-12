@extends('layouts.app')

@section('title', 'About Us')
<style>
    .about_page{
        margin-bottom: 100px;
    }
</style>
@section('content')
<div class="container about_page">
    <h2 class="text-center my-4">About Us</h2>

    <div class="row">
        <div class="col-md-6">
            <img src="/products/about1.jpg" class="img-fluid rounded" alt="About Us">
        </div>
        <div class="col-md-6">
            <h4>Who We Are</h4>
            <p>Online Medicine is dedicated to providing high-quality pharmaceutical products to customers worldwide. Our mission is to ensure easy access to medicines with fast and reliable delivery.</p>

            <h4>Our Vision</h4>
            <p>We aim to revolutionize the healthcare industry by making online medicine shopping simple, safe, and affordable.</p>

            <h4>Why Choose Us?</h4>
            <ul>
                <li>Authentic and high-quality medicines</li>
                <li>Fast and secure delivery</li>
                <li>24/7 customer support</li>
                <li>Easy returns and refunds</li>
            </ul>
        </div>
    </div>

    @include('inc.categories')

</div>
@endsection
