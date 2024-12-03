@extends('layouts.orders_table')

@section('title', 'Requests')

@section('content')
<div class="main-container">
    <div class="sidebar">

    @include('sidebars.rabea-sidebar')
    </div>
    <div class="main-content">
        @include('profile.partials.Rabea.requests_table', ['orders' => $orders])
    </div>
    </div>
@endsection

@section('scripts')
<script>
    function changeBulkStatus(status) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one order to change its status.');
            return;
        }

        const orderIds = Array.from(checkboxes).map(checkbox => {
            return checkbox.closest('tr').dataset.orderId;
        });

        document.getElementById('bulk-status-input').value = status;
        document.getElementById('selected-orders-input').value = JSON.stringify(orderIds);
        document.getElementById('bulk-status-form').submit();
    }
</script>
@endsection