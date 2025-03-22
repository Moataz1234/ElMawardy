<!DOCTYPE html>
<html>
<head>
    @include('components.navbar')
    <title>Barcode Print</title>
    <!-- Include QR code JS library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        
        .barcode-container {
            padding: 0 20px;
            width: 100%;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .barcode-card {
            width: 100%;
            height: 150px;
            border: 1px solid #ddd;
            position: relative;
            margin-bottom: 10px;
        }
        
        .shop-id {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 32px;
            font-weight: bold;
        }
        
        .left-item, .right-item {
            position: absolute;
            width: 20%;
            height: 100%;
        }
        
        .left-item {
            left: 0;
            transform: rotate(180deg);
        }
        
        .right-item {
            right: 50px;
        }
        
        .qr-code {
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
        }
        
        .stars {
            /* position: absolute; */
            top: 70px;
            right: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .item-details {
            position: absolute;
            top: 90px;
            right: 5px;
            font-size: 12px;
            font-weight: bold;
            text-align: right;
        }
        
        .left-item .qr-code {
            left: 5px;
            right: auto;
        }
        
        .left-item .stars {
            left: 5px;
            right: auto;
        }
        
        .left-item .item-details {
            left: 5px;
            right: auto;
            text-align: left;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .barcode-card {
                break-inside: avoid;
            }
            
            .print-controls {
                display: none;
            }

            /* Hide navbar and its components when printing */
            .navbar,
            .navbar-expand-lg,
            .bg-body-tertiary,
            nav,
            header {
                display: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
        }
        
        .print-controls {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .print-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="print-button" onclick="window.print()">Print Barcodes</button>
    </div>
    
    @php
        // Group items by shop_id
        $itemsByShop = collect($barcodeData)->groupBy('shop_id');
    @endphp

    @foreach($itemsByShop as $shopId => $items)
        @php
            $shopIndex = $loop->index;  // Get the current shop index
            $pairs = $items->chunk(2);
        @endphp

        @foreach($pairs as $pairIndex => $pair)
            <div class="barcode-container">
                <div class="barcode-card">
                    <div class="shop-id">{{ $shopId }}</div>
                    
                    <!-- Right item (always present) -->
                    <div class="right-item">
                        <div id="qr-{{ $shopIndex }}-{{ $pairIndex }}-0" class="qr-code"></div>
                        <div class="item-details">
                            <div class="stars">{{ $pair[0]['stars'] }}</div>
                            <div>{{ $pair[0]['serial_number'] }}</div>
                            <div>{{ $pair[0]['model'] }}</div>
                            <div>{{ $pair[0]['weight'] }}</div>
                        </div>
                    </div>
                    
                    <!-- Left item (may not be present) -->
                    @if(isset($pair[1]))
                        <div class="left-item">
                            <div id="qr-{{ $shopIndex }}-{{ $pairIndex }}-1" class="qr-code"></div>
                            <div class="item-details">
                                <div class="stars">{{ $pair[1]['stars'] }}</div>
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($itemsByShop as $shopId => $items)
                @php 
                    $shopIndex = $loop->index;
                    $pairs = $items->chunk(2);
                @endphp
                
                @foreach($pairs as $pairIndex => $pair)
                    generateQR('{{ $pair[0]['serial_number'] }}', 'qr-{{ $shopIndex }}-{{ $pairIndex }}-0');
                    
                    @if(isset($pair[1]))
                        generateQR('{{ $pair[1]['serial_number'] }}', 'qr-{{ $shopIndex }}-{{ $pairIndex }}-1');
                    @endif
                @endforeach
            @endforeach
        });
        
        function generateQR(text, elementId) {
            var element = document.getElementById(elementId);
            if (!element) {
                console.error('Element not found:', elementId);
                return;
            }
            
            var typeNumber = 4;
            var errorCorrectionLevel = 'L';
            var qr = qrcode(typeNumber, errorCorrectionLevel);
            qr.addData(text);
            qr.make();
            element.innerHTML = qr.createImgTag(2);
        }
    </script>
</body>
</html> 