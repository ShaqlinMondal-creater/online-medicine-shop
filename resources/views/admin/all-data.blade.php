@extends('layouts.app')

@section('title', 'All Data')

@section('content')
<div class="container">
    <h2 class="text-center">{{ ucfirst($type) }} List</h2>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                @if($type === 'users')
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                @elseif($type === 'products')
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                @elseif($type === 'orders')
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                @endif
                <th>Action</th> <!-- ✅ New Action Column -->
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
                <tr>
                    @if($type === 'users')
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['email'] }}</td>
                        <td>{{ $item['phone'] }}</td>
                        <td>{{ $item['role'] }}</td>
                    @elseif($type === 'products')
                        <td>{{ $item['name'] }}</td>
                        <td>${{ $item['price'] }}</td>
                        <td>{{ $item['stock'] }}</td>
                    @elseif($type === 'orders')
                        <td>{{ $item['order_id'] }}</td>
                        <td>{{ $item['customer_name'] }}</td>
                        <td>${{ $item['total_amount'] }}</td>
                    @endif
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEditModal({{ $key }})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteEntry({{ $key }})">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="/admin/dashboard" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<!-- ✅ Modal for Editing Products -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editIndex">

                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" id="editName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" id="editPrice" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" id="editStock" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function openEditModal(index) {
            fetch('/admin/get-products')
            .then(response => response.json())
            .then(data => {
                let product = data[index];
                document.getElementById('editIndex').value = index;
                document.getElementById('editName').value = product.name || "";
                document.getElementById('editPrice').value = product.price || "";
                document.getElementById('editStock').value = product.stock || "";
    
                $('#editModal').modal('show'); // ✅ Open Modal
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Failed to load product data!");
            });
        }
    
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let index = document.getElementById('editIndex').value;
            let updatedProduct = {
                name: document.getElementById('editName').value,
                price: parseFloat(document.getElementById('editPrice').value),
                stock: parseInt(document.getElementById('editStock').value)
            };
    
            fetch(`/admin/update-product?index=${index}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updatedProduct)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Failed to update product!");
            });
        });
    
        function deleteEntry(index) {
            if (confirm("Are you sure you want to delete this product?")) {
                fetch(`/admin/delete-product?index=${index}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Failed to delete product!");
                });
            }
        }
    
        window.openEditModal = openEditModal;
        window.deleteEntry = deleteEntry;
    });
</script>
    
    

@endsection
