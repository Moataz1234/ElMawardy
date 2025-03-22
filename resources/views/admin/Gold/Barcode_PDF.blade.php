<!DOCTYPE html>
<html>

<head>
    <title>Barcode PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .barcode-container {
            width: 100%;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .barcode-card {
            width: 100%;
            height: 200px;
            border: 1px solid #ddd;
            position: relative;
            margin-bottom: 10px;
        }

        .shop-id {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 36px;
            font-weight: bold;
        }

        .left-item,
        .right-item {
            position: absolute;
            width: 25%;
            height: 100%;
        }

        .left-item {
            left: 0;
            transform: rotate(180deg);
        }

        .right-item {
            right: 0;
        }

        .qr-code {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 60px;
            height: 60px;
        }

        .stars {
            position: absolute;
            top: 75px;
            right: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .item-details {
            position: absolute;
            top: 100px;
            right: 10px;
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        .left-item .qr-code {
            left: 10px;
            right: auto;
        }

        .left-item .stars {
            left: 10px;
            right: auto;
        }

        .left-item .item-details {
            left: 10px;
            right: auto;
            text-align: left;
        }

        @media print {
            .barcode-card {
                break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }

        .qr-code {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 120px;
            /* Larger size for better scanning */
            height: 120px;
        }
    </style>
</head>

<body>
    @php
        // Group items by shop_id
        $itemsByShop = collect($barcodeData)->groupBy('shop_id');
    @endphp

    @foreach ($itemsByShop as $shopId => $items)
        @php
            // Process items in pairs
            $pairs = $items->chunk(2);
        @endphp

        @foreach ($pairs as $pair)
            <div class="barcode-container">
                <div class="barcode-card">
                    <div class="shop-id">{{ $shopId }}</div>

                    <!-- Right item (always present) -->
                    <div class="right-item">
                        <!-- In your barcode_new.blade.php view -->
                        <img src="{{ $pair[0]['barcode_image'] }}" class="qr-code" alt="{{ $pair[0]['serial_number'] }}">
                        <div class="stars">{{ $pair[0]['stars'] }}</div>
                        <div class="item-details">
                            <div>{{ $pair[0]['serial_number'] }}</div>
                            <div>{{ $pair[0]['model'] }}</div>
                            <div>{{ $pair[0]['weight'] }}</div>
                        </div>
                    </div>

                    <!-- Left item (may not be present) -->
                    @if (isset($pair[1]))
                        <div class="left-item">
                            <img src="data:image/png;base64,{{ $pair[1]['barcode_image'] }}" class="qr-code">
                            <div class="stars">{{ $pair[1]['stars'] }}</div>
                            <div class="item-details">
                                <div>{{ $pair[1]['serial_number'] }}</div>
                                <div>{{ $pair[1]['model'] }}</div>
                                <div>{{ $pair[1]['weight'] }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endforeach
</body>

</html>
