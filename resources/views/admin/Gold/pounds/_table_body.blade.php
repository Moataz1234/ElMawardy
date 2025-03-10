<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Related Item Serial</th>
                <th>Type</th>
                <th>Weight (g)</th>
                <th>Shop Name</th>
                <th>Linked Item</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shopPounds as $pound)
                <tr>
                    <td><span class="fw-medium">{{ $pound->serial_number }}</span></td>
                    <td>{{ $pound->related_item_serial ?? 'N/A' }}</td>
                    <td>{{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'N/A' }}</td>
                    <td>{{ $pound->goldPound ? $pound->goldPound->weight : 'N/A' }}</td>
                    <td>{{ $pound->shop_name }}</td>
                    <td>
                        @if($pound->goldItem)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        @if($pound->status === 'pending_sale' || $pound->status === 'pending')
                            <span class="badge bg-warning">Pending Sale</span>
                        @else
                            <span class="badge bg-success">Available</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-4">
        {{ $shopPounds->links() }}
    </div>
</div>
