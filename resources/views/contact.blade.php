@extends('layouts.app')

@section('title', 'Contact Us')
<style>
    .contact_page{
        margin-bottom: 100px;
    }
</style>
@section('content')
<div class="container contact_page">
    <h2 class="text-center my-4">Contact Us</h2>

    <div class="row">
        <div class="col-md-6">
            <h4>Get in Touch</h4>
            <p>If you have any questions, feel free to contact us using the form below.</p>

            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter your name">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" rows="4" placeholder="Enter your message"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>

        <div class="col-md-6">
            <h4>Contact Information</h4>
            <p><strong>Email:</strong> support@onlinemedicine.com</p>
            <p><strong>Phone:</strong> +1 234 567 890</p>
            <p><strong>Address:</strong> 123 Main Street, New York, NY 10001</p>
        </div>
    </div>
</div>
@endsection
