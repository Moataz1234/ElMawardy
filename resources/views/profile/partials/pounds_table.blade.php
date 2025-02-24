@foreach ($shopPounds as $pound)
    <tr>
        <td>
            @if ($pound->status === 'pending_sale' || $pound->status === 'pending')
                <span class="badge bg-warning">Pending</span>
            @else
                <div class="form-check">
                    <input type="checkbox" class="form-check-input pound-checkbox"
                        name="selected_pounds[]"
                        value="{{ $pound->serial_number }}"
                        data-serial="{{ $pound->serial_number }}">
                </div>
            @endif
        </td>
        <td><span class="fw-medium">{{ $pound->serial_number }}</span></td>
        <td>{{ $pound->related_item_serial ?? 'N/A' }}</td>
        <td>{{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'N/A' }}</td>
        <td>{{ $pound->goldPound ? $pound->goldPound->weight : 'N/A' }}</td>
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
