@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Production Orders</h4>
                    <a href="{{ route('production.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Order
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Model</th>
                                    <th>Total Quantity</th>
                                    <th>Not Finished</th>
                                    <th>Progress</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productionOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>
                                            <strong>{{ $order->model }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $order->quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $order->not_finished }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $progress = $order->quantity > 0 
                                                    ? round((($order->quantity - $order->not_finished) / $order->quantity) * 100, 2)
                                                    : 0;
                                                $progressClass = $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $progressClass }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $progress }}%"
                                                     aria-valuenow="{{ $progress }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ $progress }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $order->order_date->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('production.edit', $order) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('production.destroy', $order) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this production order?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No production orders found.</p>
                                                <a href="{{ route('production.create') }}" class="btn btn-primary">
                                                    Create First Order
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($productionOrders->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $productionOrders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 