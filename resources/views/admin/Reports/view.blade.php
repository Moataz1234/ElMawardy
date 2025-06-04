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
            padding: 10px;
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
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .data-text {
            background-color: white;
            color: #333;
            padding: 5px;
            margin-top: 5px;
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
            color: #c99a3c;
            right: 200px;
            top: 100px;
        }

        @page {
            size: A4;
            margin: 20px;
        }

        .report-container {
            height: auto;
            min-height: 95vh;
            width: {{ $isPdf ? '100%' : '800px' }};
            page-break-inside: avoid;
            page-break-after: always;
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
            border: 5px solid #6A6458;
            border-radius: 10px;
            padding: 20px;
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
            }

            .report-container:first-child {
                page-break-before: avoid;
            }

            .report-container:last-child {
                page-break-after: avoid;
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
        }

        .variant-B {
            color: goldenrod !important;
            font-weight: bold;
        }

        .variant-C {
            color: red !important;
            font-weight: bold;
        }

        .variant-D {
            color: blue !important;
            font-weight: bold;
        }

        .main-model {
            color: black !important;
            font-weight: bold;
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
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
            border: 2px solid #333;
        }

        .legend-text {
            font-weight: bold;
            font-size: 14px;
        }

        td .variant-count {
            display: inline-block;
            margin-left: 5px;
            font-weight: bold;
        }

        /* Workshop info style */
        .workshop-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #f39c12;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .workshop-info h4 {
            color: white;
            margin-bottom: 8px;
            font-size: 18px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .workshop-info p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        /* Variant counts display */
        .variant-counts {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
        }

        .variant-counts h5 {
            color: #6A6458;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .variant-count-item {
            display: inline-block;
            margin: 5px 10px 5px 0;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            border: 2px solid;
        }

        .variant-count-A {
            background-color: #f8f9fa;
            color: black;
            border-color: black;
        }

        .variant-count-B {
            background-color: #fff8dc;
            color: #b8860b;
            border-color: #b8860b;
        }

        .variant-count-C {
            background-color: #ffe4e1;
            color: #dc143c;
            border-color: #dc143c;
        }

        .variant-count-D {
            background-color: #e6f3ff;
            color: #0066cc;
            border-color: #0066cc;
        }
    </style>

</head>

<body>
    <!-- Enhanced Color Legend -->
    <div class="color-legend no-export">
        <div class="legend-title">🎨 Model Variant Color Guide</div>
        <div class="legend-grid">
            <div class="legend-item">
                <span class="legend-color" style="background-color: black;"></span>
                <span class="legend-text" style="color: black;">Base Model / Variant A</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: goldenrod;"></span>
                <span class="legend-text" style="color: goldenrod;">Variant B (Yellow)</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: red;"></span>
                <span class="legend-text" style="color: red;">Variant C (Red)</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: blue;"></span>
                <span class="legend-text" style="color: blue;">Variant D (Blue)</span>
            </div>
        </div>
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
    <p class="total-items-sold">Total Items Sold on {{ $selectedDate }}: {{ $totalItemsSold }}</p>

    <!-- Report Data -->
    @if (count($reportsData) > 0)
        @foreach ($reportsData as $model => $data)
            <div class="report-container">
                <div class="report-page">
                    <div class="header-section clearfix">
                        <div class="image-section">
                            @if (isset($data['image_path']) && $data['image_path'])
                                <img height="200px" style="max-height: 400px"
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
                        </div>
                    </div>

                    {{-- Workshop Info Section --}}
                    @if(isset($data['workshop_data']) && $data['workshop_data'])
                    <div class="workshop-info">
                        <h4>🏭 At Workshop: {{ $data['workshop_data']['not_finished'] }}</h4>
                        <p>📅 Order Date: {{ $data['workshop_data']['order_date'] }}</p>
                    </div>
                    @endif

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

                        {{-- Show variant counts for base models --}}
                        @if (!isset($data['variant']) || !$data['variant'])
                            @php
                                $hasVariants = false;
                                $baseModel = $data['base_model'] ?? $data['model'];
                                $variantCounts = [
                                    'A' => collect($data['shops_data'])->sum('variant_A'),
                                    'B' => collect($data['shops_data'])->sum('variant_B'),
                                    'C' => collect($data['shops_data'])->sum('variant_C'),
                                    'D' => collect($data['shops_data'])->sum('variant_D')
                                ];
                                $hasVariants = array_sum($variantCounts) > 0;
                            @endphp
                            
                            @if($hasVariants)
                            <div class="variant-counts">
                                <h5>🎨 Variant Counts for {{ $baseModel }}:</h5>
                                @foreach($variantCounts as $variant => $count)
                                    @if($count > 0)
                                        <span class="variant-count-item variant-count-{{ $variant }}">
                                            {{ $variant }}: {{ $count }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        @endif

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
                                <th>All Rests</th>
                            </tr>
                            @foreach ($data['shops_data'] as $shop => $counts)
                                <tr>
                                    <td class="shop-name">{{ $shop }}</td>
                                    <td>
                                        @if (isset($data['variant']) && $data['variant'])
                                            {{-- For variant models, only show in all rests --}}
                                            <span class="main-model">-</span>
                                        @else
                                            <span class="main-model">{{ $counts['yellow_gold'] }}</span>
                                            @if (isset($counts['variant_B']) && $counts['variant_B'] > 0)
                                                <span class="variant-B"> (+{{ $counts['variant_B'] }})</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($data['variant']) && $data['variant'])
                                            {{-- For variant models, only show in all rests --}}
                                            <span class="main-model">-</span>
                                        @else
                                            <span class="main-model">{{ $counts['white_gold'] }}</span>
                                            @if (isset($counts['variant_A']) && $counts['variant_A'] > 0)
                                                <span class="variant-A"> (+{{ $counts['variant_A'] }})</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($data['variant']) && $data['variant'])
                                            {{-- For variant models, only show in all rests --}}
                                            <span class="main-model">-</span>
                                        @else
                                            <span class="main-model">{{ $counts['rose_gold'] }}</span>
                                            @if (isset($counts['variant_C']) && $counts['variant_C'] > 0)
                                                <span class="variant-C"> (+{{ $counts['variant_C'] }})</span>
                                            @endif
                                            @if (isset($counts['variant_D']) && $counts['variant_D'] > 0)
                                                <span class="variant-D"> (+{{ $counts['variant_D'] }})</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($data['variant']) && $data['variant'])
                                            {{-- For variant models, show variant info here with appropriate color --}}
                                            <span class="variant-{{ $data['variant'] }}">{{ $counts['all_rests'] }}</span>
                                            @if ($data['variant'] == 'A' && isset($counts['variant_A']) && $counts['variant_A'] > 0)
                                                <span class="variant-A"> ({{ $counts['variant_A'] }} Black)</span>
                                            @elseif ($data['variant'] == 'B' && isset($counts['variant_B']) && $counts['variant_B'] > 0)
                                                <span class="variant-B"> ({{ $counts['variant_B'] }} Yellow)</span>
                                            @elseif ($data['variant'] == 'C' && isset($counts['variant_C']) && $counts['variant_C'] > 0)
                                                <span class="variant-C"> ({{ $counts['variant_C'] }} Red)</span>
                                            @elseif ($data['variant'] == 'D' && isset($counts['variant_D']) && $counts['variant_D'] > 0)
                                                <span class="variant-D"> ({{ $counts['variant_D'] }} Blue)</span>
                                            @endif
                                        @else
                                            <span class="main-model">{{ $counts['all_rests'] }}</span>
                                            @php
                                                $totalVariants =
                                                    ($counts['variant_A'] ?? 0) +
                                                    ($counts['variant_B'] ?? 0) +
                                                    ($counts['variant_C'] ?? 0) +
                                                    ($counts['variant_D'] ?? 0);
                                            @endphp
                                            @if ($totalVariants > 0)
                                                <span class="main-model"> (+{{ $totalVariants }})</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                   
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
