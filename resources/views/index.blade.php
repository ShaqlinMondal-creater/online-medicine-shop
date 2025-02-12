@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- ✅ Navbar -->


<!-- ✅ Welcome Message -->
<div class="container text-center mt-4 sticky_text">
    <h2>Welcome to Our ALPHA Medicine Store</h2>
    <p>Shop for your healthcare needs easily.</p>
</div>
<br>
@include('inc.slider')

@include('inc.featured-products')
<br>
@include('inc.brands')

    
@endsection
