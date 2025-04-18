<!DOCTYPE html>
<html>

<head>
    @include('components.navbar')
    <title>Barcode Print</title>
    <!-- Include QR code JS library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <link href="{{ asset('css/barcode.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Add a button for printing custom pages -->
    <div class="print-controls">
        <button class="print-button" onclick="printCurrentPage()">Print Current Card</button>
        <button class="print-button" onclick="printAllPages()">Print All Pages</button>
        {{-- <button class="print-button" onclick="showCustomPrintDialog()">Print Custom Pages</button> --}}
    </div>

    <!-- Custom print dialog -->
    <div id="customPrintDialog" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
        background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000;">
        <h3>Print Custom Pages</h3>
        <div style="margin: 15px 0;">
            <label>Page Numbers (comma-separated):</label>
            <input type="text" id="customPageNumbers" placeholder="e.g., 1,3,5" />
        </div>
        <div style="text-align: right; margin-top: 20px;">
            <button onclick="cancelCustomPrint()" style="margin-right: 10px; padding: 5px 15px;">Cancel</button>
            <button onclick="confirmCustomPrint()" style="padding: 5px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px;">Print</button>
        </div>
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

    @foreach ($itemsByShop as $shopId => $items)
        @php
            $shopIndex = $loop->index;
            $itemsArray = $items->toArray();
            $itemCount = count($itemsArray);
        @endphp

        @for ($i = 0; $i < $itemCount; $i += 2)
            <div class="page">
                <div class="barcode-container">
                    <div class="barcode-card">
                        <div class="shop-id">{{ $shopId }}</div>

                        <!-- Right item (always present) -->
                        @if (isset($itemsArray[$i]))
                            <div class="right-item">
                                <div id="qr-{{ $shopIndex }}-{{ $i / 2 }}-0" class="qr-code"></div>
                                <div class="item-details">
                                    <div class="stars">{{ $itemsArray[$i]['stars'] ?? '' }}</div>
                                    <div>{{ $itemsArray[$i]['serial_number'] ?? '' }}</div>
                                    <div>{{ $itemsArray[$i]['model'] ?? '' }}</div>
                                    <div>{{ $itemsArray[$i]['weight'] ?? '' }} {{ $itemsArray[$i]['source'] ?? '' }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Left item (may not be present) -->
                        @if (isset($itemsArray[$i + 1]))
                            <div class="left-item">
                                <div id="qr-{{ $shopIndex }}-{{ $i / 2 }}-1" class="qr-code"></div>
                                <div class="item-details">
                                    <div class="stars">{{ $itemsArray[$i + 1]['stars'] ?? '' }}</div>
                                    <div>{{ $itemsArray[$i + 1]['serial_number'] ?? '' }}</div>
                                    <div>{{ $itemsArray[$i + 1]['model'] ?? '' }}</div>
                                    <div>{{ $itemsArray[$i + 1]['weight'] ?? '' }} {{ $itemsArray[$i + 1]['source'] ?? '' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endfor
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
            // Generate QR codes
            @foreach ($itemsByShop as $shopId => $items)
                @php
                    $shopIndex = $loop->index;
                    $itemsArray = $items->toArray();
                    $itemCount = count($itemsArray);
                @endphp

                @for ($i = 0; $i < $itemCount; $i += 2)
                    @if (isset($itemsArray[$i]))
                        generateQR('{{ $itemsArray[$i]['serial_number'] ?? '' }}',
                            'qr-{{ $shopIndex }}-{{ $i / 2 }}-0');
                    @endif

                    @if (isset($itemsArray[$i + 1]))
                        generateQR('{{ $itemsArray[$i + 1]['serial_number'] ?? '' }}',
                            'qr-{{ $shopIndex }}-{{ $i / 2 }}-1');
                    @endif
                @endfor
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

            var typeNumber = 0; // Auto-detect size
            var errorCorrectionLevel = 'H'; // Highest error correction for better scanning
            var qr = qrcode(typeNumber, errorCorrectionLevel);
            qr.addData(text);
            qr.make();

            // Create QR code with larger cell size and margin for better scanning
            var qrImage = qr.createImgTag(6, 2); // cellSize = 6, margin = 2

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

        function showCustomPrintDialog() {
            document.getElementById('customPrintDialog').style.display = 'block';
        }

        function cancelCustomPrint() {
            document.getElementById('customPrintDialog').style.display = 'none';
        }

        function confirmCustomPrint() {
            const pageNumbers = document.getElementById('customPageNumbers').value.split(',').map(num => parseInt(num.trim()) - 1);
            pages.forEach((page, index) => {
                if (pageNumbers.includes(index)) {
                    page.style.display = 'block';
                } else {
                    page.style.display = 'none';
                }
            });

            window.print();

            // Restore original display settings after print
            setTimeout(() => {
                pages.forEach(page => page.style.display = '');
                showPage(currentPageIndex);
            }, 100);

            document.getElementById('customPrintDialog').style.display = 'none';
        }

        function printCurrentPage() {
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

        function printAllPages() {
            // Show all pages
            pages.forEach(page => page.style.display = 'block');

            // Print all pages
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
