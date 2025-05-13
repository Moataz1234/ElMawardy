<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Results - {{ $serial }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('components.navbar')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }

        .timeline-container {
            position: relative;
            padding: 20px 0;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            height: 100%;
            width: 4px;
            background: #e0e0e0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            margin-left: 65px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -45px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #007bff;
            z-index: 1;
        }

        .timeline-item-date {
            position: absolute;
            left: -150px;
            top: 0;
            width: 100px;
            text-align: right;
        }

        .timeline-item-date .date {
            display: block;
            font-weight: bold;
            color: #333;
        }

        .timeline-item-date .time {
            display: block;
            font-size: 0.8rem;
            color: #666;
        }

        .timeline-item-content {
            padding-bottom: 10px;
        }

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
        }

        .badge-success {
            background-color: #198754;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #0dcaf0;
            color: #212529;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            border-radius: 0.25rem;
        }

        .common-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .common-details .detail-item {
            margin-bottom: 8px;
        }

        .common-details .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .status-details {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .status-details .detail-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-details .detail-label {
            font-weight: 600;
            color: #495057;
        }

        @media (max-width: 767px) {
            .timeline-item-date {
                position: relative;
                left: 0;
                top: 0;
                width: 100%;
                text-align: left;
                margin-bottom: 10px;
            }

            .timeline-item {
                margin-left: 30px;
            }

            .timeline-item::before {
                left: -30px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-search"></i> Tracking Results for Serial:
                            <strong>{{ $serial }}</strong>
                        </h4>
                        <a href="{{ route('tracking.standalone') }}" class="btn btn-light">
                            <i class="fas fa-search"></i> New Search
                        </a>
                    </div>
                    <div class="card-body">
                        @include('components.serial-tracking-search')

                        @if ($trackingData->isEmpty())
                            {{-- <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No tracking information found for serial number: <strong>{{ $serial }}</strong>
                                <p class="small mt-2 mb-0">
                                    <strong>Note:</strong> The system automatically searched for all variations of this serial number.
                                    If you searched for a number without the "G-" prefix, we also checked for "G-{{ $serial }}".
                                </p>
                            </div> --}}
                        @else
                            {{-- <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Found {{ $trackingData->count() }} records for serial number: <strong>{{ $serial }}</strong>
                                <p class="small mt-2 mb-0">
                                    <strong>Note:</strong> The system automatically searched for all variations of this serial number.
                                    @if (strpos($serial, 'G-') === 0)
                                        We also checked for "{{ substr($serial, 2) }}".
                                    @else
                                        We also checked for "G-{{ $serial }}".
                                    @endif
                                </p>
                            </div> --}}

                            <!-- Common Details Section -->
                            @php
                                $firstItem = $trackingData->first();
                                $commonDetails = [
                                    'Model' => $firstItem['details']['Model'] ?? 'N/A',
                                    'Kind' => $firstItem['details']['Kind'] ?? 'N/A',
                                ];
                            @endphp
                            <div class="common-details">
                                <div class="row">
                                    @foreach ($commonDetails as $key => $value)
                                        <div class="col-md-6 detail-item">
                                            <span class="detail-label">{{ $key }}:</span>
                                            <span>{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="timeline-container">
                                @foreach ($trackingData as $item)
                                    <div class="timeline-item">
                                        <div class="timeline-item-content">
                                            <div
                                                class="card mb-3 shadow-sm 
                                                @if (Str::contains(strtolower($item['source']), 'sold')) bg-success text-white
                                                @elseif(Str::contains(strtolower($item['source']), 'deleted')) bg-danger text-white
                                                @elseif(Str::contains(strtolower($item['source']), 'transfer')) bg-info text-white
                                                @else bg-white @endif">
                                                <div
                                                    class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">{{ $item['source'] }}</h5>
                                                    <span
                                                        class="badge 
                                                        @if ($item['status'] == 'sold' || $item['status'] == 'Sold') badge-success
                                                        @elseif($item['status'] == 'deleted' || $item['status'] == 'Deleted') badge-danger
                                                        @elseif($item['status'] == 'pending') badge-warning
                                                        @elseif($item['status'] == 'approved') badge-info
                                                        @else badge-secondary @endif">
                                                        {{ $item['status'] }}
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="status-details">
                                                        <!-- Common details for all statuses -->
                                                        @if (isset($item['details']['Weight']))
                                                            <div class="detail-item">
                                                                <span class="detail-label">Weight:</span>
                                                                <span>{{ $item['details']['Weight'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if (isset($item['details']['Shop']))
                                                            <div class="detail-item">
                                                                <span class="detail-label">Shop:</span>
                                                                <span>{{ $item['details']['Shop'] }}</span>
                                                            </div>
                                                        @endif

                                                        <!-- Status-specific details -->
                                                        @if (Str::contains(strtolower($item['source']), 'sold'))
                                                            @if (isset($item['details']['Sold Date']))
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Sold Date:</span>
                                                                    <span>{{ $item['details']['Sold Date'] }}</span>
                                                                </div>
                                                            @endif
                                                            @if (isset($item['details']['Price']))
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Price:</span>
                                                                    <span>{{ $item['details']['Price'] }}</span>
                                                                </div>
                                                            @endif
                                                        @elseif(Str::contains(strtolower($item['source']), 'transfer'))
                                                            @if (isset($item['details']['To Shop']))
                                                                <div class="detail-item">
                                                                    <span class="detail-label">To:</span>
                                                                    <span>{{ $item['details']['To Shop'] }}</span>
                                                                </div>
                                                            @endif
                                                            @if (isset($item['details']['From Shop']))
                                                                <div class="detail-item">
                                                                    <span class="detail-label">From:</span>
                                                                    <span>{{ $item['details']['From Shop'] }}</span>
                                                                </div>
                                                            @endif
                                                        @elseif(Str::contains(strtolower($item['source']), 'deleted'))
                                                            @if (isset($item['details']['Deleted By']))
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Deleted By:</span>
                                                                    <span>{{ $item['details']['Deleted By'] }}</span>
                                                                </div>
                                                            @endif
                                                            @if (isset($item['details']['Deletion Reason']))
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Reason:</span>
                                                                    <span>{{ $item['details']['Deletion Reason'] }}</span>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- <div class="card-footer text-muted">
                                                    <i class="far fa-clock"></i> {{ $item['created_at']->format('Y-m-d H:i:s') }}
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="timeline-item-date">
                                            <span class="date">{{ $item['created_at']->format('M d, Y') }}</span>
                                            <span class="time">{{ $item['created_at']->format('H:i') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
