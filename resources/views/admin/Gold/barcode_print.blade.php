<!DOCTYPE html>
<html>
<head>
    @include('components.navbar')
    <title>Barcode Print</title>
    <!-- Include QR code JS library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <style>
        body {
            font-family: "Yu Gothic Medium", "Yu Gothic", YuGothic, sans-serif;
            margin: 0;
            background-color: #c9cec9
        }
        
        .barcode-container {
            width: 72.3mm;  /* Exact width */
            height: 38.1mm; /* Exact height */
            margin: 0;
            page-break-after: always; /* Force each card on new page */
        }
        
        .barcode-card {
            width: 100%;
            height: 100%;
            border: 1px solid #ddd;
            position: relative;
            margin: 0;
            page-break-after: always; /* Additional break for cards */
        }
        
        .shop-id {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 32px;
            font-weight: bold;
            font-family: "Yu Gothic Medium", "Yu Gothic", YuGothic, sans-serif;
        }
        
        .left-item, .right-item {
            position: absolute;
            width: 20%;
            height: 100%;
            
        }
        
        .left-item {
            transform: rotate(180deg);
        }
        
        .right-item {
            right: 5px;
        }
        
        .qr-code {
            position: absolute;
            top: 5px;
            width: 50px;
            height: 50px;
            background-color: white;  /* Ensure white background */
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;  /* Ensure sharp edges */
        }
        
        .stars {
            /* position: absolute; */
            top: 70px;
            right: 5px;
            font-size: 14px;
            font-weight: bold;
            font-family: "Yu Gothic Medium", "Yu Gothic", YuGothic, sans-serif;
        }
        
        .item-details {
            position: absolute;
            top: 70px;
            font-size: 12px;
            font-family: "Yu Gothic Medium", "Yu Gothic", YuGothic, sans-serif;
            font-weight: bold;
            text-align: right;
            
        }        
        
        @media print {
            @page {
                size: 72.3mm 38.1mm; /* Exact label size */
                margin: 0;
            }

            html, body {
                margin: 0;
                padding: 0;
                width: 72.3mm;
            }
            
            .barcode-container {
                display: block;
                page-break-after: always;
                page-break-inside: avoid;
            }
            
            .barcode-card {
                break-inside: avoid;
                break-after: page;
                page-break-after: always;
                margin: 0;
                padding: 0;
            }
            
            .print-controls, 
            .navbar,
            .navbar-expand-lg,
            .bg-body-tertiary,
            nav,
            header {
                display: none !important;
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

        .page {
            width: 72.3mm;
            height: 38.1mm;
            margin: 0 auto;
            page-break-after: always;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: fixed;  /* Fix the page in viewport */
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);  /* Center in viewport */
            display: none;    /* Hide all pages by default */
        }

        .page.active {
            display: block;  /* Show only active page */
        }

        .pagination-controls {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            background: white;
            padding: 10px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .pagination-button {
            padding: 5px 15px;
            margin: 0 5px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .pagination-info {
            display: inline-block;
            margin: 0 15px;
        }

        @media print {
            .page {
                position: relative;
                display: block;
                left: 0;
                top: 0;
                transform: none;
                box-shadow: none;
                margin: 0;
            }

            .pagination-controls {
                display: none;
            }
        }

        /* Add styles for the dialog inputs */
        #printSettingsDialog input,
        #printSettingsDialog select {
            padding: 5px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #printSettingsDialog label {
            display: inline-block;
            width: 100px;
        }

        .item-details div:last-child {
            /* Style for the weight and to_print line */
            white-space: nowrap;  /* Keep weight and to_print on same line */
        }
    </style>
</head>
<body>
    <!-- Add this HTML for the print settings dialog -->
    <div id="printSettingsDialog" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
        background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000;">
        <h3>Printer Settings</h3>
        <div style="margin: 15px 0;">
            <label>Print Speed:</label>
            <input type="text" id="printSpeed" value="4.0" /> "/s
        </div>
        <div style="margin: 15px 0;">
            <label>Darkness:</label>
            <input type="number" id="darkness" value="30" min="0" max="100" />
        </div>
        <div style="margin: 15px 0;">
            <label>Printing Mode:</label>
            <select id="printMode">
                <option value="thermal_transfer" selected>Thermal transfer</option>
                <option value="direct_thermal">Direct Thermal</option>
            </select>
        </div>
        <div style="margin: 15px 0;">
            <label>Offsets:</label><br>
            Top: <input type="text" id="topOffset" value="0" style="width: 50px;" /> "
            <br>
            Left: <input type="text" id="leftOffset" value="0" style="width: 50px;" /> "
        </div>
        <div style="margin: 15px 0;">
            <label>Advanced Settings:</label><br>
            <select id="backfeed">
                <option value="default" selected>Default</option>
                <option value="custom">Custom</option>
            </select>
            <br>
            <select id="pause">
                <option value="no_pause" selected>No pause</option>
                <option value="pause">Pause</option>
            </select>
        </div>
        <div style="text-align: right; margin-top: 20px;">
            <button onclick="cancelPrint()" style="margin-right: 10px; padding: 5px 15px;">Cancel</button>
            <button onclick="confirmPrint()" style="padding: 5px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px;">Print</button>
        </div>
    </div>

    <!-- Update your print button -->
    <div class="print-controls">
        <button class="print-button" onclick="showPrintDialog()">Print Current Card</button>
    </div>
    
    <div class="pagination-controls">
        <button class="pagination-button" onclick="previousPage()">Previous</button>
        <span class="pagination-info">Page <span id="currentPage">1</span> of <span id="totalPages">0</span></span>
        <button class="pagination-button" onclick="nextPage()">Next</button>
    </div>
    
    @php
        // Group items by shop_id
        $itemsByShop = collect($barcodeData)->groupBy('shop_id');
    @endphp

    @foreach($itemsByShop as $shopId => $items)
        @php
            $shopIndex = $loop->index;
            $pairs = $items->chunk(2);
        @endphp

        @foreach($pairs as $pairIndex => $pair)
            <div class="page">
                <div class="barcode-container">
                    <div class="barcode-card">
                        <div class="shop-id">{{ $shopId }}</div>
                        
                        <!-- Right item (always present) -->
                        @if($pair->first())
                            <div class="right-item">
                                <div id="qr-{{ $shopIndex }}-{{ $pairIndex }}-0" class="qr-code"></div>
                                <div class="item-details">
                                    <div class="stars">{{ $pair->first()['stars'] }}</div>
                                    <div>{{ $pair->first()['serial_number'] }}</div>
                                    <div>{{ $pair->first()['model'] }}</div>
                                    <div>{{ $pair->first()['weight'] }} {{ $pair->first()['source'] }}</div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Left item (may not be present) -->
                        @if($pair->count() > 1)
                            <div class="left-item">
                                <div id="qr-{{ $shopIndex }}-{{ $pairIndex }}-1" class="qr-code"></div>
                                <div class="item-details">
                                    <div class="stars">{{ $pair->get(1)['stars'] }}</div>
                                    <div>{{ $pair->get(1)['serial_number'] }}</div>
                                    <div>{{ $pair->get(1)['model'] }}</div>
                                    <div>{{ $pair->get(1)['weight'] }} {{ $pair->get(1)['source'] }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
    
    <script>
        function setupPrinter() {
            if (typeof window.jsPrintSetup !== 'undefined') {
                // Firefox-specific print setup
                window.jsPrintSetup.setPrinterName('YOUR_PRINTER_NAME');
                window.jsPrintSetup.setOption('printSpeed', '4.0');
                window.jsPrintSetup.setOption('darkness', '30');
                window.jsPrintSetup.setOption('printMode', 'thermal');
            } else {
                // For Chrome/Edge, we can use the print API
                let mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        // Before print
                        setPrinterSettings();
                    }
                });
            }
        }

        function setPrinterSettings() {
            // Try to set printer settings using the Printer API
            if (window.Printer && window.Printer.getDefaultPrinter()) {
                let printer = window.Printer.getDefaultPrinter();
                printer.printSpeed = 4.0;
                printer.darkness = 30;
                printer.printMode = 'thermal_transfer';
            }
        }

        // Modified print function
        function printBarcodes() {
            // Hide all pages except current
            pages.forEach(page => {
                if (pages[currentPageIndex] !== page) {
                    page.style.display = 'none';
                } else {
                    page.style.display = 'block';
                }
            });

            // Setup printer and print
            setupPrinter();
            window.print();

            // Restore original display settings after print
            setTimeout(() => {
                pages.forEach(page => page.style.display = '');
                showPage(currentPageIndex);
            }, 100);
        }

        let currentPageIndex = 0;
        let pages;

        document.addEventListener('DOMContentLoaded', function() {
            @foreach($itemsByShop as $shopId => $items)
                @php 
                    $shopIndex = $loop->index;
                    $pairs = $items->chunk(2);
                @endphp
                
                @foreach($pairs as $pairIndex => $pair)
                    generateQR('{{ $pair->first()['serial_number'] }}', 'qr-{{ $shopIndex }}-{{ $pairIndex }}-0');
                    
                    @if($pair->count() > 1)
                        generateQR('{{ $pair->get(1)['serial_number'] }}', 'qr-{{ $shopIndex }}-{{ $pairIndex }}-1');
                    @endif
                @endforeach
            @endforeach

            // Initialize pagination
            pages = document.querySelectorAll('.page');
            document.getElementById('totalPages').textContent = pages.length;
            showPage(0);
        });
        
        function generateQR(text, elementId) {
            var element = document.getElementById(elementId);
            if (!element) {
                console.error('Element not found:', elementId);
                return;
            }
            
            var typeNumber = 0;  // Auto-detect size
            var errorCorrectionLevel = 'H';  // Highest error correction for better scanning
            var qr = qrcode(typeNumber, errorCorrectionLevel);
            qr.addData(text);
            qr.make();
            
            // Create QR code with larger cell size for better scanning
            var qrImage = qr.createImgTag(4, 1);  // cellSize = 4, margin = 1
            
            // Create a wrapper div to style the QR code
            var wrapper = document.createElement('div');
            wrapper.innerHTML = qrImage;
            var img = wrapper.firstChild;
            
            img.style.width = '100%';
            img.style.height = '100%';
            
            element.innerHTML = '';
            element.appendChild(img);
        }

        function showPage(index) {
            pages.forEach(page => page.classList.remove('active'));
            if (pages[index]) {
                pages[index].classList.add('active');
                currentPageIndex = index;
                document.getElementById('currentPage').textContent = index + 1;
            }
        }

        function nextPage() {
            if (currentPageIndex < pages.length - 1) {
                showPage(currentPageIndex + 1);
            }
        }

        function previousPage() {
            if (currentPageIndex > 0) {
                showPage(currentPageIndex - 1);
            }
        }

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                nextPage();
            } else if (e.key === 'ArrowLeft') {
                previousPage();
            }
        });

        // Add these functions to your existing JavaScript
        function showPrintDialog() {
            document.getElementById('printSettingsDialog').style.display = 'block';
        }

        function cancelPrint() {
            document.getElementById('printSettingsDialog').style.display = 'none';
        }

        function confirmPrint() {
            // Get all settings
            const settings = {
                speed: document.getElementById('printSpeed').value,
                darkness: document.getElementById('darkness').value,
                mode: document.getElementById('printMode').value,
                topOffset: document.getElementById('topOffset').value,
                leftOffset: document.getElementById('leftOffset').value,
                backfeed: document.getElementById('backfeed').value,
                pause: document.getElementById('pause').value
            };

            // Apply settings to printer
            setupPrinter(settings);
            
            // Hide dialog
            document.getElementById('printSettingsDialog').style.display = 'none';
            
            // Proceed with printing
            printBarcodes();
        }

        function setupPrinter(settings) {
            if (typeof window.jsPrintSetup !== 'undefined') {
                // Firefox-specific print setup
                window.jsPrintSetup.setPrinterName('YOUR_PRINTER_NAME');
                window.jsPrintSetup.setOption('printSpeed', settings.speed);
                window.jsPrintSetup.setOption('darkness', settings.darkness);
                window.jsPrintSetup.setOption('printMode', settings.mode);
            } else {
                // For Chrome/Edge, we can use the print API
                let mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        // Before print
                        setPrinterSettings(settings);
                    }
                });
            }
        }

        function setPrinterSettings(settings) {
            // Try to set printer settings using the Printer API
            if (window.Printer && window.Printer.getDefaultPrinter()) {
                let printer = window.Printer.getDefaultPrinter();
                printer.printSpeed = parseFloat(settings.speed);
                printer.darkness = parseInt(settings.darkness);
                printer.printMode = settings.mode;
                printer.topOffset = settings.topOffset;
                printer.leftOffset = settings.leftOffset;
            }
        }

        // Update your existing printBarcodes function to work with the dialog
        function printBarcodes() {
            // Hide all pages except current
            pages.forEach(page => {
                if (pages[currentPageIndex] !== page) {
                    page.style.display = 'none';
                } else {
                    page.style.display = 'block';
                }
            });

            // Print
            window.print();

            // Restore original display settings after print
            setTimeout(() => {
                pages.forEach(page => page.style.display = '');
                showPage(currentPageIndex);
            }, 100);
        }
    </script>
</body>
</html> 