<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @include('components.navbar')
</head>
<div class="container mt-4">
    <h2 class="mb-4">Approved Sales History</h2>
    <div class="row mb-4">
        <div class="col-md-12">
            <button type="button" id="exportExcel" class="btn btn-success">Export to Excel</button>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Weight</th>
                <th>Price</th>
                <th>Price/Gram</th>
                <th>Payment Method</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($soldItemRequests as $request)
                @php
                    $weight = $request->item_type === 'pound' ? $request->weight : ($request->goldItem->weight ?? 0);
                    $pricePerGram = $weight > 0 ? round($request->price / $weight, 2) : 0;
                @endphp
                <tr>
                    <td>
                        @if ($request->status === 'pending')
                            <input type="checkbox" name="requests[]" value="{{ $request->id }}" class="request-checkbox" />
                        @endif
                    </td>
                    <td>
                        <div class="item-container">
                            <!-- Serial number links to item details -->
                            <a href="#" class="item-details text-primary" 
                               data-toggle="modal" 
                               data-target="#itemModal_{{ $request->id }}">
                                {{ $request->item_serial_number }}
                            </a>
                            <!-- + badge for pound details -->
                            @if ($request->associatedPound)
                                <a href="#" class="badge badge-info ml-2" data-toggle="collapse" 
                                   data-target="#poundDetails_{{ $request->id }}">+</a>
                            @endif
                        </div>
                        <!-- Pound details collapse -->
                        @if ($request->associatedPound)
                            <div id="poundDetails_{{ $request->id }}" class="collapse mt-2">
                                <div class="card card-body">
                                    <p><strong>Pound Serial:</strong> {{ $request->associatedPound->item_serial_number }}</p>
                                    <p><strong>Weight:</strong> {{ $request->associatedPound->weight }}g</p>
                                    <p><strong>Purity:</strong> {{ $request->associatedPound->purity }}K</p>
                                    <p><strong>Price:</strong> {{ $request->associatedPound->price }} {{ config('app.currency') }}</p>
                                </div>
                            </div>
                        @endif
                    </td>
{{--                         
                        <a href="#" class="item-details text-primary" 
                           data-serial="{{ $request->item_serial_number }}"
                           data-toggle="modal" 
                           data-target="#itemModal_{{ $request->id }}">
                            {{ $request->item_serial_number }}
                        </a>
                        @if($request->item_type === 'pound')
                            <span class="badge badge-info">Pound</span>
                        @endif
                         --}}
                        <!-- Modal for this specific item -->
                        <div class="modal fade" id="itemModal_{{ $request->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content d-flex">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            @if($request->item_type === 'pound')
                                                Pound Details
                                            @else
                                                Item Details
                                            @endif
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body d-flex justify-content-between">
                                        @if($request->item_type === 'pound')
                                            <!-- Pound Details -->
                                            {{-- <div class="row"> --}}
                                                <div class="col-md-5">
                                                    <h6 class="font-weight-bold">Pound Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->item_serial_number }}</p>
                                                    <p><strong>Type:</strong> {{ $request->kind ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->weight ?? 'N/A' }}g</p>
                                                    <p><strong>Purity:</strong> {{ $request->purity ?? 'N/A' }}K</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Price/Gram:</strong> {{ $pricePerGram }} {{ config('app.currency') }}g</p>
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Item Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->goldItem->serial_number ?? 'N/A' }}</p>
                                                    <p><strong>Kind:</strong> {{ $request->goldItem->kind ?? 'N/A' }}</p>
                                                    <p><strong>Model:</strong> {{ $request->goldItem->model ?? 'N/A' }}</p>
                                                    <p><strong>Gold Color:</strong> {{ $request->goldItem->gold_color ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->goldItem->weight ?? 'N/A' }}</p>
                                                    <p><strong>Metal Purity:</strong> {{ $request->goldItem->metal_purity ?? 'N/A' }}</p>
                                                </div> --}}
                                            {{-- </div> --}}
                                        @else
                                            <!-- Regular Item Details -->
                                            {{-- <div class="row"> --}}
                                                <div class="col-md-5">
                                                    <h6 class="font-weight-bold">Item Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->goldItem->serial_number ?? 'N/A' }}</p>
                                                    <p><strong>Kind:</strong> {{ $request->goldItem->kind ?? 'N/A' }}</p>
                                                    <p><strong>Model:</strong> {{ $request->goldItem->model ?? 'N/A' }}</p>
                                                    <p><strong>Gold Color:</strong> {{ $request->goldItem->gold_color ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->goldItem->weight ?? 'N/A' }}g</p>
                                                    <p><strong>Metal Purity:</strong> {{ $request->goldItem->metal_purity ?? 'N/A' }}</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Price/Gram:</strong> {{ $pricePerGram }} {{ config('app.currency') }}/g</p>
                                                </div>
                                                
                                                <!-- Show associated pound if exists -->
                                                @php
                                                    $poundRequest = null;
                                                    if ($request->goldItem && in_array($request->goldItem->model, [
                                                        '5-1416', '1-1068', '5-1338-C', '2-1928', '5-1290',
                                                        '2-1899', '5-1369', '1-1291',
                                                        '9-0194', '7-1329', '7-1013-A', '4-0854', '5-1370', '7-1386'
                                                    ])) {
                                                        $poundRequest = \App\Models\SaleRequest::where('item_serial_number', $request->item_serial_number)
                                                            ->where('item_type', 'pound')
                                                            ->first();
                                                    }
                                                @endphp
                                                
                                                @if($poundRequest)
                                                    <div class="col-md-6">
                                                        <h6 class="font-weight-bold">Associated Pound</h6>
                                                        <p><strong>Status:</strong> {{ $poundRequest->status }}</p>
                                                        <p><strong>Price:</strong> {{ $poundRequest->price }} {{ config('app.currency') }}</p>
                                                        <p><strong>Weight:</strong> {{ $poundRequest->weight }}g</p>
                                                        <p><strong>Purity:</strong> {{ $poundRequest->purity }}K</p>
                                                    </div>
                                                @endif
                                            {{-- </div> --}}
                                        @endif

                                        @if($request->customer)
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <h6 class="font-weight-bold">Customer Information</h6>
                                                    <p><strong>Name:</strong> {{ $request->customer->first_name }} {{ $request->customer->last_name }}</p>
                                                    <p><strong>Phone:</strong> {{ $request->customer->phone_number ?? 'N/A' }}</p>
                                                    <p><strong>Email:</strong> {{ $request->customer->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $request->shop_name }}</td>
                    <td>{{ $weight }}g</td>
                    <td>{{ $request->price }} {{ config('app.currency') }}</td>
                    <td>{{ $pricePerGram }} {{ config('app.currency') }}/g</td>
                    <td>{{ $request->payment_method ?? 'N/A' }}</td>
                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Export to Excel
    $('#exportExcel').click(function() {
        window.location.href = '/export-sales';
    });
});
</script>
