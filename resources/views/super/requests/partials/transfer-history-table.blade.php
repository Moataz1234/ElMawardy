<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Serial Number</th>
                <th>Model</th>
                <th>Kind</th>
                <th>From Shop</th>
                <th>To Shop</th>
                <th>Weight</th>
                <th>Status</th>
                <th>Transfer Date</th>
                <th>Sold Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $history)
            <tr>
                <td><strong>#{{ $history->id }}</strong></td>
                <td>
                    <span class="badge bg-primary">{{ $history->serial_number }}</span>
                </td>
                <td>{{ $history->model }}</td>
                <td>
                    <span class="badge bg-light text-dark">{{ $history->kind }}</span>
                </td>
                <td>
                    @if($history->fromShop)
                        <span class="badge bg-info">{{ $history->fromShop->name }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $history->from_shop_name }}</span>
                    @endif
                </td>
                <td>
                    @if($history->toShop)
                        <span class="badge bg-success">{{ $history->toShop->name }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $history->to_shop_name }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-warning text-dark">{{ $history->weight }}g</span>
                </td>
                <td>
                    <span class="badge {{ $history->status == 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ ucfirst($history->status) }}
                    </span>
                </td>
                <td>
                    @if($history->transfer_completed_at)
                        <small class="text-muted">{{ \Carbon\Carbon::parse($history->transfer_completed_at)->format('M d, Y H:i') }}</small>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>
                <td>
                    @if($history->item_sold_at)
                        <small class="text-success">{{ \Carbon\Carbon::parse($history->item_sold_at)->format('M d, Y H:i') }}</small>
                    @else
                        <span class="text-muted">Not sold</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bx bx-history fs-1 d-block mb-2"></i>
                        No transfer history found
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