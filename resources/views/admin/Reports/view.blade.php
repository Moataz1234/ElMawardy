<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


    @include('components.navbar')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {{-- <link href="{{ asset('css/report.css') }}" rel="stylesheet"> --}}

    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* padding: 10px; */
        }


        /* Container styles */

        .report-page {
            page-break-after: always;
            margin-bottom: 20px;
            height: auto;
            min-height: 100vh;
        }

        .report-page:last-child {
            page-break-after: avoid;
        }


        /* Header section */

        .header-section {
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }

        .image-section {
            width: 60%;
            float: left;
        }

        .image-section img {
            width: 300px;
            height: auto;
            border: 5px solid #6A6458;
        }

        .info-section {
            width: 35%;
            float: right;
        }


        /* Info boxes */

        .info-box {
            background-color: #6A6458;
            color: white;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 5px;
            text-align: center;
        }

        .data-text {
            background-color: white;
            color: #333;
            padding: 3px;
            margin-top: 3px;
            font-weight: bold;
        }


        /* Table styles */

        .table-section {
            width: 100%;
            margin-top: 20px;
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #6A6458;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #6A6458;
        }

        td {
            padding: 5px;
            text-align: center;
            border: 1px solid #6A6458;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .shop-name {
            background-color: #8c8c8c;
            color: white;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }


        /* Filter form styles */

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form input[type="date"] {
            padding: 5px;
            font-size: 16px;
        }

        .filter-form button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #6A6458;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #5a5448;
        }

        .export-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .export-button:hover {
            background-color: #218838;
        }

        .total-items-sold {
            position: absolute;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #423a2a;
            /* right: 100px; */
            left: 10px;
            top: 100px;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        .report-container {
            height: auto;
            max-height: {{ $isPdf ? '90vh' : 'auto' }};
            width: {{ $isPdf ? '90%' : '800px' }};
            page-break-inside: avoid;
            page-break-after: {{ $isPdf ? 'always' : 'avoid' }};
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
            border: 5px solid #6A6458;
            border-radius: 10px;
            padding: {{ $isPdf ? '10px' : '15px' }};
            overflow: hidden;
        }

        .report-container:last-child {
            page-break-after: avoid;
        }

        .pdf-only {
            display: none;
        }

        @media print {
            .no-export {
                display: none;
            }

            .pdf-only {
                display: block;
            }

            .report-container {
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
                width: 95% !important;
                max-width: 95% !important;
                max-height: 95vh !important;
                margin: 0 auto !important;
                padding: 5px !important;
                font-size: 10px !important;
            }

            .report-container:first-child {
                page-break-before: avoid;
            }

            .report-container:last-child {
                page-break-after: avoid;
            }

            body {
                margin: 0 !important;
                padding: 5px !important;
            }

            .variants-table {
                display: table !important;
                visibility: visible !important;
                page-break-inside: avoid;
                font-size: 10px;
            }

            table {
                font-size: 11px;
            }

            .header-section {
                margin-bottom: 10px;
            }

            .image-section img {
                max-width: 250px !important;
                max-height: 200px !important;
            }

            /* Force workshop info to stay in same row for PDF - using float */
            .workshop-row {
                width: 100% !important;
                margin-bottom: 5px !important;
                overflow: hidden !important; /* Clear floats */
            }

            .workshop-box {
                float: left !important;
                width: 48% !important;
                margin-right: 2% !important;
                vertical-align: top !important;
                font-size: 11px !important;
            }

            .workshop-box:last-child {
                margin-right: 0 !important;
            }

            /* Compact info boxes for PDF */
            .info-box {
                padding: 4px !important;
                margin-bottom: 4px !important;
                font-size: 11px !important;
            }

            .data-text {
                padding: 2px !important;
                margin-top: 2px !important;
                font-size: 10px !important;
            }

            /* Reduce overall spacing in PDF */
            .header-section {
                margin-bottom: 5px !important;
            }

            .table-section {
                margin-top: 10px !important;
            }

            table {
                margin-bottom: 8px !important;
                font-size: 10px !important;
            }

            th, td {
                padding: 3px !important;
            }
        }

        @media screen {

            .header-section {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .image-section {
                width: 50%;
            }

            .info-section {
                width: 45%;
            }

            .image-section img {
                max-width: 100%;
                height: auto;
            }
        }


        /* New styles for the email management dialogue */

        .email-dialogue {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .email-dialogue.open {
            display: block;
        }

        .email-list {
            margin-bottom: 10px;
        }

        .email-list input {
            margin-right: 10px;
            margin-bottom: 5px;
            padding: 5px;
            width: 200px;
        }

        .add-email-button,
        .save-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #6A6458;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-email-button:hover,
        .save-button:hover {
            background-color: #5a5448;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        .icon-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .no-export {
            display: {{ $isPdf ? 'none' : 'block' }};
        }

        .total-items-sold {
            display: {{ $isPdf ? 'none' : 'block' }};
        }

        /* Variant color styles - improved */
        .variant-A {
            color: black !important;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 2px;
            border: 1px solid black;
        }

        .variant-B {
            color: #b8860b !important;
            font-weight: bold;
            background-color: #fff8dc;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 2px;
            border: 1px solid #b8860b;
        }

        .variant-C {
            color: #dc143c !important;
            font-weight: bold;
            background-color: #ffe4e1;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 2px;
            border: 1px solid #dc143c;
        }

        .variant-D {
            color: #0066cc !important;
            font-weight: bold;
            background-color: #e6f3ff;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 2px;
            border: 1px solid #0066cc;
        }

        .main-model {
            color: black !important;
            font-weight: bold;
            background-color: #ffffff;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 2px;
            border: 1px solid #6A6458;
        }

        .model-variant {
            margin-left: 5px;
            font-weight: bold;
        }

        td .variant-display {
            margin-left: 5px;
            font-weight: bold;
        }

        /* Enhanced variant legend */
        .color-legend {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border: 2px solid #6A6458;
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
        }

        .legend-title {
            color: #6A6458;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            justify-items: center;
        }

        .legend-item {
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 10px 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            min-width: 150px;
        }

        .legend-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .legend-color {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 8px;
            vertical-align: middle;
            border: 2px solid;
        }

        .legend-text {
            font-weight: bold;
            font-size: 14px;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid;
        }

        /* Variants color table styling */
        .variants-table {
            margin-top: 10px;
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        .variants-table th {
            padding: 4px;
            text-align: center;
            border: 1px solid #6A6458;
            background-color: #6A6458;
            color: white;
            font-weight: bold;
        }

        .variants-table td {
            padding: 4px;
            text-align: center;
            border: 1px solid #6A6458;
            background-color: white;
        }

        @media print {
            .variants-table {
                display: table !important;
                visibility: visible !important;
                page-break-inside: avoid;
            }
        }
    </style>

</head>

<body>
    <!-- Enhanced Color Legend -->
    <div class="color-legend no-export">
        <div class="legend-title">🎨 Model Display Guide</div>
        <div class="legend-grid">
            {{-- <div class="legend-item">
                <span class="legend-color" style="background-color: #ffffff; border-color: #6A6458;"></span>
                <span class="legend-text" style="color: black; background-color: #ffffff; border-color: #6A6458;">Base Model</span>
            </div> --}}
            <div class="legend-item">
                <span class="legend-color" style="background-color: #f8f9fa; border-color: black;"></span>
                <span class="legend-text" style="color: black; background-color: #f8f9fa; border-color: black;">Variant A</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #fff8dc; border-color: #b8860b;"></span>
                <span class="legend-text" style="color: #b8860b; background-color: #fff8dc; border-color: #b8860b;">Variant B</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #ffe4e1; border-color: #dc143c;"></span>
                <span class="legend-text" style="color: #dc143c; background-color: #ffe4e1; border-color: #dc143c;">Variant C</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #e6f3ff; border-color: #0066cc;"></span>
                <span class="legend-text" style="color: #0066cc; background-color: #e6f3ff; border-color: #0066cc;">Variant D</span>
            </div>
        </div>
        <p style="text-align: center; margin-top: 10px; font-style: italic; color: #666;">
            📝 Gold colors show base model + variants by gold color. All Rests shows variants by character (A,B,C,D). Sold variant appears first in All Rests.
        </p>
    </div>
    
    <div class="no-export position-relative">
        <h1>Sales Reports</h1>

        <!-- Date Filter Form -->
        <form action="{{ route('reports.view') }}" method="GET" class="filter-form">
            <label for="date">Select Date:</label>
            <input type="date" name="date" value="{{ $selectedDate }}" required>
            <button type="submit">Filter</button>
            <a href="{{ route('reports.view', ['date' => $selectedDate, 'export' => 'pdf']) }}" class="export-button">
                Download as PDF
            </a>
            <img src="{{ asset('storage/icons/email.svg') }}" onclick="openEmailDialogue()" alt="Email Icon"
                style="width:54px; height: 34px; cursor: pointer;">
        </form>
    </div>

    <!-- Email Management Dialogue -->
    <div class="email-dialogue" id="emailDialogue">
        <button class="close-button" onclick="closeEmailDialogue()">×</button>
        <h3>Manage Email Recipients</h3>
        <div class="email-list" id="emailList">
            @foreach ($recipients as $recipient)
                <div>
                    <input type="email" value="{{ $recipient }}" readonly>
                    <button onclick="removeEmail(this)" class="bg-danger">Remove</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="add-email-button" onclick="addEmailField()">Add Email</button>
        <button type="button" class="send-button bg-info" onclick="sendReport()">Send Report</button>
    </div>

    <!-- Total Items Sold -->
    <p class="total-items-sold">Total Items Sold on {{ $selectedDate }}: <span style="color: #b97f0b; font-weight: bold;">{{ $totalItemsSold }}</span></p>

    <!-- Report Data -->
    @if (count($reportsData) > 0)
        @foreach ($reportsData as $model => $data)
            <div class="report-container">
                <div class="report-page">
                    <div class="header-section clearfix">
                        <div class="image-section">
                            @if (isset($data['image_path']) && $data['image_path'])
                                <img height="200px" style="max-height: 300px"
                                    src="{{ $isPdf ? public_path('storage/' . $data['image_path']) : asset('storage/' . $data['image_path']) }}"
                                    alt="Product Image" />
                            @else
                                <p>No Image Available</p>
                            @endif
                        </div>
                        <div class="info-section">
                            <div class="info-box">
                                Gold Color
                                <div class="data-text">{{ $data['gold_color'] }}</div>
                            </div>
                            <div class="info-box">
                                Source
                                <div class="data-text">{{ $data['source'] }}</div>
                            </div>
                            <div class="info-box">
                                Stars
                                <div class="data-text">{{ $data['stars'] }}</div>
                            </div>
                            <div class="workshop-row" style="display: flex; flex-direction: row; gap: 10px; width: 100%;">
                            <div class="info-box workshop-box" style="width: 50%;">
                               At Workshop
                                <div class="data-text">{{ $data['workshop_data']['not_finished'] }}</div>
                            </div>
                            <div class="info-box workshop-box" style="width: 50%;">
                                Order Date
                                <div class="data-text">{{ $data['workshop_data']['order_date'] }}</div>
                            </div>
                            </div>
                          
                        </div>
                    </div>

                    <div class="table-section">
                        <table>
                            <tr>
                                <th>Model</th>
                                <th>Remaining</th>
                                <th>Total Production</th>
                                <th>Total Sold</th>
                            </tr>
                            <tr>
                                <td>
                                    @if (isset($data['variant']) && $data['variant'])
                                        {{-- For variant models, show in variant color --}}
                                        <span class="variant-{{ $data['variant'] }}">{{ $data['model'] }}</span>
                                    @else
                                        {{-- For base models, show in black --}}
                                        <span class="main-model">{{ $data['base_model'] ?? $data['model'] }}</span>
                                    @endif
                                </td>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['remaining'] }}</td>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['total_production'] }}</td>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['total_sold'] }}</td>
                            </tr>
                        </table>

                        <table>
                            <tr>
                                <th>First Production</th>
                                <th>Last Production</th>
                                <th>Shop</th>
                                <th>Sold Pieces</th>
                            </tr>
                            <tr>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['first_production'] }}</td>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['last_production'] }}</td>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['shop'] }}</td>
                                <td class="{{ isset($data['variant']) && $data['variant'] ? 'variant-' . $data['variant'] : 'main-model' }}">{{ $data['pieces_sold_today'] }}</td>
                            </tr>
                        </table>

                        <table>
                            <tr>
                                <th>Shop</th>
                                <th>Yellow Gold</th>
                                <th>White Gold</th>
                                <th>Rose Gold</th>
                                <th>
                                    All Rests
                                    @if(count($data['existing_variants']) > 0)
                                        <div style="font-size: 14px; margin-top: 3px; border-top: 1px solid white; padding-top: 2px;">
                                            @php
                                                // Create dynamic order: sold variant first, then others
                                                $orderedVariants = [];
                                                $soldVariant = $data['variant'] ?? null;
                                                
                                                // Add sold variant first if it exists
                                                if ($soldVariant && in_array($soldVariant, $data['existing_variants'])) {
                                                    $orderedVariants[] = $soldVariant;
                                                }
                                                
                                                // Add remaining variants in A,B,C,D order
                                                foreach(['A', 'B', 'C', 'D'] as $variant) {
                                                    if (in_array($variant, $data['existing_variants']) && $variant !== $soldVariant) {
                                                        $orderedVariants[] = $variant;
                                                    }
                                                }
                                            @endphp
                                            
                                            @foreach($orderedVariants as $variant)
                                                @if($variant == 'A')
                                                    <span style="color: white; font-weight: bold; margin: 0 3px; font-size: 16px;">A⚫</span>
                                                @elseif($variant == 'B')
                                                    <span style="color: #FFD700; font-weight: bold; margin: 0 3px; font-size: 16px;">B🟡</span>
                                                @elseif($variant == 'C')
                                                    <span style="color: #FFB6C1; font-weight: bold; margin: 0 3px; font-size: 16px;">C🔴</span>
                                                @elseif($variant == 'D')
                                                    <span style="color: #87CEEB; font-weight: bold; margin: 0 3px; font-size: 16px;">D🔵</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </th>
                            </tr>
                            @foreach ($data['shops_data'] as $shop => $counts)
                                <tr>
                                    <td class="shop-name">{{ $shop }}</td>
                                    <td style="color: black;">
                                        {{-- Yellow Gold column: Show ONLY base model yellow gold --}}
                                        {{ $counts['yellow_gold'] }}
                                    </td>
                                    <td style="color: black;">
                                        {{-- White Gold column: Show ONLY base model white gold --}}
                                        {{ $counts['white_gold'] }}
                                    </td>
                                    <td style="color: black;">
                                        {{-- Rose Gold column: Show ONLY base model rose gold --}}
                                        {{ $counts['rose_gold'] }}
                                    </td>
                                    <td>
                                        {{-- All Rests column: Show total count for base models, then variant counts in dynamic order --}}
                                        @php
                                            // Use the same dynamic ordering as the header
                                            $orderedVariants = [];
                                            $soldVariant = $data['variant'] ?? null;
                                            
                                            // Add sold variant first if it exists
                                            if ($soldVariant && in_array($soldVariant, $data['existing_variants'])) {
                                                $orderedVariants[] = $soldVariant;
                                            }
                                            
                                            // Add remaining variants in A,B,C,D order
                                            foreach(['A', 'B', 'C', 'D'] as $variant) {
                                                if (in_array($variant, $data['existing_variants']) && $variant !== $soldVariant) {
                                                    $orderedVariants[] = $variant;
                                                }
                                            }
                                        @endphp
                                        
                                        @if(isset($data['variant']) && $data['variant'])
                                            {{-- For variant models, show variants in dynamic order --}}
                                            @foreach($orderedVariants as $variantLetter)
                                                <span class="variant-{{ $variantLetter }}">{{ $counts['variant_' . $variantLetter] }}</span>
                                            @endforeach
                                        @else
                                            {{-- For base models, show base model total first --}}
                                            @php
                                                $baseModelCount = $counts['white_gold'] + $counts['yellow_gold'] + $counts['rose_gold'];
                                            @endphp
                                            @if($baseModelCount > 0)
                                                <span class="main-model">{{ $baseModelCount }}</span>
                                            @endif
                                            {{-- Then show variants in dynamic order --}}
                                            @foreach($orderedVariants as $variantLetter)
                                                <span class="variant-{{ $variantLetter }}">{{ $counts['variant_' . $variantLetter] }}</span>
                                            @endforeach
                                            {{-- If no base model count and no variants, show the total --}}
                                            @if($baseModelCount == 0 && empty($data['existing_variants']))
                                                <span class="main-model">{{ $counts['all_rests'] }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">No sales found for this date</div>
    @endif
    
    <script>
        // Function to open the email dialogue
        function openEmailDialogue() {
            document.getElementById('emailDialogue').classList.add('open');
        }

        // Function to close the email dialogue
        function closeEmailDialogue() {
            document.getElementById('emailDialogue').classList.remove('open');
        }

        // Function to remove an email field
        function removeEmail(button) {
            button.parentElement.remove();
        }

        // Function to add a new email input field
        function addEmailField() {
            const emailList = document.getElementById('emailList');
            const newInput = document.createElement('div');
            newInput.innerHTML = `
                <input type="email" placeholder="Enter email">
                <button onclick="removeEmail(this)" class="bg-danger">Remove</button>
            `;
            emailList.appendChild(newInput);
        }

        // Function to send the report
        function sendReport() {
            const emailInputs = document.querySelectorAll('#emailList input');
            const emails = Array.from(emailInputs).map(input => input.value);

            if (emails.length === 0) {
                alert('Please add at least one email.');
                return;
            }

            // Get the selected date
            const selectedDate = "{{ $selectedDate }}";

            // Send the report to the specified emails
            fetch("{{ route('reports.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        date: selectedDate,
                        recipients: emails
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Report sent successfully!');
                        closeEmailDialogue();
                    } else {
                        alert('Failed to send report: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the report.');
                });
        }
    </script>
</body>

</html>
