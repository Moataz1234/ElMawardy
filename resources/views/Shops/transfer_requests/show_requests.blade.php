<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">Transfer Requests</h1>
                <p class="text-muted">Manage transfer requests for {{ Auth::user()->shop_name }}</p>
            </div>
        </div>

        <!-- Request Type Tabs -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-10">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link active" id="incoming-tab" data-bs-toggle="pill" href="#incoming">
                            <i class="bi bi-arrow-down-circle me-2"></i>استلام
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="outgoing-tab" data-bs-toggle="pill" href="#outgoing">
                            <i class="bi bi-arrow-up-circle me-2"></i>  خروج
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="tab-content">
                    <!-- Incoming Requests -->
                    <div class="tab-pane fade show active" id="incoming">
                        @forelse($incomingRequests as $request)
                        <div class="card mb-4 shadow border-0 rounded-3">
                            <div class="card-header bg-primary text-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-upc-scan me-2"></i>
                                        Serial Number: 
                                        @if($request->type == 'pound')
                                            {{ $request->pound->serial_number ?? 'N/A' }}
                                            <span class="badge bg-warning text-dark ms-2">Pound</span>
                                        @else
                                            {{ $request->goldItem->serial_number ?? 'N/A' }}
                                            <span class="badge bg-info text-dark ms-2">Item</span>
                                        @endif
                                    </h5>
                                    <span class="badge bg-light text-primary">
                                        Request #{{ $request->id }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Item/Pound Details -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="bi bi-info-circle me-2"></i>
                                                @if($request->type == 'pound')
                                                    Pound Details
                                                @else
                                                    Item Details
                                                @endif
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                @if($request->type == 'pound')
                                                    <li class="mb-2">
                                                        <strong>Weight:</strong> 
                                                        {{ $request->pound->goldPound->weight ?? 'N/A' }} g
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Kind:</strong> 
                                                        {{ $request->pound->goldPound->kind ?? 'N/A' }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Purity:</strong> 
                                                        {{ $request->pound->goldPound->purity ?? 'N/A' }}
                                                    </li>
                                                @else
                                                    <li class="mb-2">
                                                        <strong>Weight:</strong> 
                                                        {{ $request->goldItem->weight ?? 'N/A' }} g
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Model:</strong> 
                                                        {{ $request->goldItem->model ?? 'N/A' }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Gold Color:</strong> 
                                                        {{ $request->goldItem->gold_color ?? 'N/A' }}
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Transfer Details -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="bi bi-arrow-left-right me-2"></i>Transfer Details
                                            </h6>
                                            <div class="d-flex justify-content-between mb-3">
                                                <div>
                                                    <small class="text-muted d-block">From Shop</small>
                                                    <span class="badge bg-secondary fs-6">
                                                        <i class="bi bi-shop me-1"></i>
                                                        {{ $request->from_shop_name }}
                                                    </span>
                                                </div>
                                                <div class="text-center">
                                                    <i class="bi bi-arrow-right text-primary fs-4"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">To Shop</small>
                                                    <span class="badge bg-secondary fs-6">
                                                        <i class="bi bi-shop me-1"></i>
                                                        {{ $request->to_shop_name }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <small class="text-muted d-block mb-1">Current Status</small>
                                                <span class="badge bg-info fs-6">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    {{ $request->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 text-center">
                                    <form action="{{ route('transfer-requests.handle', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-success btn-lg px-4">
                                            <i class="bi bi-check-circle me-2"></i>Accept Transfer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>No incoming transfer requests at the moment.
                        </div>
                        @endforelse
                    </div>

                    <!-- Outgoing Requests -->
                    <div class="tab-pane fade" id="outgoing">
                        @forelse($outgoingRequests as $request)
                        <div class="card mb-4 shadow border-0 rounded-3">
                            <div class="card-header bg-secondary text-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-upc-scan me-2"></i>
                                        Serial Number: 
                                        @if($request->type == 'pound')
                                            {{ $request->pound->serial_number ?? 'N/A' }}
                                            <span class="badge bg-warning text-dark ms-2">Pound</span>
                                        @else
                                            {{ $request->goldItem->serial_number ?? 'N/A' }}
                                            <span class="badge bg-info text-dark ms-2">Item</span>
                                        @endif
                                    </h5>
                                    <span class="badge bg-light text-primary">
                                        Request #{{ $request->id }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Item/Pound Details -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="bi bi-info-circle me-2"></i>
                                                @if($request->type == 'pound')
                                                    Pound Details
                                                @else
                                                    Item Details
                                                @endif
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                @if($request->type == 'pound')
                                                    <li class="mb-2">
                                                        <strong>Weight:</strong> 
                                                        {{ $request->pound->goldPound->weight ?? 'N/A' }} g
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Kind:</strong> 
                                                        {{ $request->pound->goldPound->kind ?? 'N/A' }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Purity:</strong> 
                                                        {{ $request->pound->goldPound->purity ?? 'N/A' }}
                                                    </li>
                                                @else
                                                    <li class="mb-2">
                                                        <strong>Weight:</strong> 
                                                        {{ $request->goldItem->weight ?? 'N/A' }} g
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Model:</strong> 
                                                        {{ $request->goldItem->model ?? 'N/A' }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <strong>Gold Color:</strong> 
                                                        {{ $request->goldItem->gold_color ?? 'N/A' }}
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Transfer Details -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="bi bi-arrow-left-right me-2"></i>Transfer Details
                                            </h6>
                                            <div class="d-flex justify-content-between mb-3">
                                                <div>
                                                    <small class="text-muted d-block">From Shop</small>
                                                    <span class="badge bg-secondary fs-6">
                                                        <i class="bi bi-shop me-1"></i>
                                                        {{ $request->from_shop_name }}
                                                    </span>
                                                </div>
                                                <div class="text-center">
                                                    <i class="bi bi-arrow-right text-primary fs-4"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">To Shop</small>
                                                    <span class="badge bg-secondary fs-6">
                                                        <i class="bi bi-shop me-1"></i>
                                                        {{ $request->to_shop_name }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <small class="text-muted d-block mb-1">Current Status</small>
                                                <span class="badge bg-info fs-6">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    {{ $request->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>No outgoing transfer requests at the moment.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
