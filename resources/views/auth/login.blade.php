@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header text-center bg-primary text-white">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <form id="loginForm">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="mt-3 text-center">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let email = document.getElementById('email').value;
        let password = document.getElementById('password').value;
    
        fetch('/api/login', { // ✅ Always use /api/login to check users.json
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('user_role', data.user.role);
    
                if (data.user.role === 'admin') { // ✅ Now checking from users.json
                    window.location.href = '/admin/dashboard'; // Redirect Admin
                } else {
                    window.location.href = '/index'; // Redirect User to Index Page
                }
            } else {
                alert("Login failed! Please check your credentials.");
            }
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
    
    
@endsection
