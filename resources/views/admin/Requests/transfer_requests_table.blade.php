<table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center" width="5%">#</th>
            <th width="25%">Item Details</th>
            <th width="15%">From Shop</th>
            <th width="15%">To Shop</th>
            <th width="15%">Status</th>
            <th width="25%">Timeline</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transferRequests as $index => $request)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="item-details">
                        @if($status === 'completed')
                            <div class="serial">{{ $request->serial_number }}</div>
                            <div class="model">{{ $request->model }}</div>
                        @else
                            <div class="serial">{{ $request->goldItem->serial_number }}</div>
                            <div class="model">{{ $request->goldItem->model }}</div>
                        @endif
                    </div>
                </td>
                <td>{{ $request->from_shop_name }}</td>
                <td>{{ $request->to_shop_name }}</td>
                <td>
                    <span class="badge status-{{ $request->status }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    <div class="timeline">
                        @if($status === 'completed')
                            <div class="mb-2">
                                <i class="bi bi-calendar-event"></i>
                                Created: {{ $request->created_at ? $request->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-clock-history"></i>
                                Completed: {{ $request->transfer_completed_at ? $request->transfer_completed_at->format('Y-m-d H:i:s') : 'N/A' }}
                            </div>
                        @else
                            <div class="mb-2">
                                <i class="bi bi-calendar-event"></i>
                                Created: {{ $request->created_at ? $request->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-clock-history"></i>
                                Updated: {{ $request->updated_at ? $request->updated_at->format('Y-m-d H:i:s') : 'N/A' }}
                            </div>
                        @endif
                        <div>
                            <i class="bi bi-hourglass-split"></i>
                            {{ $request->created_at ? $request->created_at->diffForHumans() : 'N/A' }}
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table> 