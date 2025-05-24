<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>From Shop</th>
                <th>To Shop</th>
                <th>Type</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td><strong>#{{ $request->id }}</strong></td>
                <td>
                    @if($request->goldItem)
                        <div>
                            <span class="badge bg-primary">{{ $request->goldItem->serial_number }}</span><br>
                            <small class="text-muted">{{ $request->goldItem->model }}</small>
                        </div>
                    @else
                        <span class="badge bg-secondary">Pound Transfer</span>
                    @endif
                </td>
                <td><span class="badge bg-light text-dark">{{ $request->from_shop_name }}</span></td>
                <td><span class="badge bg-light text-dark">{{ $request->to_shop_name }}</span></td>
                <td>
                    <span class="badge bg-info">{{ ucfirst($request->type) }}</span>
                </td>
                <td>
                    <span class="badge {{ 
                        $request->status == 'pending' ? 'bg-warning text-dark' : 
                        ($request->status == 'approved' ? 'bg-success' : 'bg-danger')
                    }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    <small class="text-muted">{{ $request->created_at->format('M d, Y H:i') }}</small>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info" 
                                onclick="viewRequestDetails('transfer', '{{ $request->id }}')" 
                                title="View Details">
                            <i class="bx bx-show"></i>
                        </button>
                        @if($request->status == 'pending')
                        <button class="btn btn-sm btn-outline-success ms-1" 
                                onclick="handleRequest('transfer', '{{ $request->id }}', 'approve')" 
                                title="Approve Request">
                            <i class="bx bx-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger ms-1" 
                                onclick="handleRequest('transfer', '{{ $request->id }}', 'reject')" 
                                title="Reject Request">
                            <i class="bx bx-x"></i>
                        </button>
                        @else
                            <span class="text-muted ms-2">{{ ucfirst($request->status) }}</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bx bx-transfer fs-1 d-block mb-2"></i>
                        No transfer requests found
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="d-flex justify-content-center">
        {{ $requests->appends(request()->query())->links('vendor.pagination.custom-super') }}
    </div>
    @endif
</div> 