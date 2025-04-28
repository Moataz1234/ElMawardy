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
            height: 10px;
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
            /* background-color: #218838; */
        }

        @page {
            size: A4;
            margin: 0;
        }

        .report-container {
            height: 1000px;
            /* Keep original height */
            width: {{ $isPdf ? '90%' : '800px' }};
            /* A4-like width for web view */
            page-break-inside: avoid;
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
            border: 5px solid #6A6458;
            border-radius: 10px;
            padding: 20px;
            overflow: hidden;
        }

        .pdf-only {
            display: none;
            /* Hide by default */
        }

        @media print {
            .no-export {
                display: none;
                /* Hide sections not to be exported */
            }

            .pdf-only {
                display: block;
                /* Show PDF-only sections */
            }

            .report-container {
                page-break-before: always;
                /* Ensure each report starts on a new page */
            }

            .report-container:first-child {
                page-break-before: avoid;
                /* Prevent page break before the first report */
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
            /* Space between the export button and the email icon */
            margin-bottom: 20px;
        }

        .no-export {
            display: {{ $isPdf ? 'none' : 'block' }};
        }

        /* Ensure the total items sold section is hidden in PDF/email */
        .total-items-sold {
            display: {{ $isPdf ? 'none' : 'block' }};
        }

        /* Add these styles to your existing CSS */
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

        /* This helps with making the variant stand out in the model cell */
        .model-variant {
            margin-left: 5px;
            font-weight: bold;
        }

        /* Add these to your existing CSS */

        /* Variant color styles */
        .variant-A {
            color: black !important;
            font-weight: bold;
        }

        .variant-B {
            color: gold !important;
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

        /* Style for the model cell to display variants nicely */
        td .variant-display {
            margin-left: 5px;
            font-weight: bold;
        }

        /* Style for the variant legend */
        .variant-legend {
            margin-top: 10px;
            font-size: 12px;
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .variant-legend span {
            display: inline-block;
            margin-right: 10px;
            padding: 2px 5px;
        }

        /* Make variant numbers stand out in the table */
        td .variant-count {
            display: inline-block;
            margin-left: 5px;
            font-weight: bold;
        }
    </style>

</head>

<body>
    <div class="variant-legend"
    style="margin-top: 10px; font-size: 12px; padding: 5px; background-color: #f5f5f5; border-radius: 4px;">
    <strong>Model Variants:</strong>
    <span style="color: black; font-weight: bold; margin-right: 10px;">A = Black</span>
    <span style="color: gold; font-weight: bold; margin-right: 10px;">B = Yellow</span>
    <span style="color: red; font-weight: bold; margin-right: 10px;">C = Red</span>
    <span style="color: blue; font-weight: bold; margin-right: 10px;">D = Blue</span>
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
                            {{-- <div class="info-box">
                                At Workshop
                                <div class="data-text">{{ $data['workshop_count'] }}</div>
                            </div>
                            <div class="info-box">
                                Order Date
                                <div class="data-text">{{ $data['order_date'] }}</div>
                            </div> --}}
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

                    <div class="table-section">
                        <table>
                            <tr>
                                <th>Model</th>
                                <th>Remaining</th>
                                <th>Total Production</th>
                                <th>Total Sold</th>
                            </tr>
                            <tr>
                                {{-- <td>{{ $data['model'] }}</td> --}}
                                <td>
                                    {{ $data['model'] }}
                                    @if (isset($data['variant']) && $data['variant'])
                                        <span
                                            style="color: {{ strtolower($data['variant_color']) }}; font-weight: bold;">({{ $data['variant'] }})</span>
                                    @endif
                                </td>


                                <td>{{ $data['remaining'] }}</td>
                                <td>{{ $data['total_production'] }}</td>
                                <td>{{ $data['total_sold'] }}</td>
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
                                <td>{{ $data['first_production'] }}</td>
                                <td>{{ $data['last_production'] }}</td>
                                <td>{{ $data['shop'] }}</td>
                                <td>{{ $data['pieces_sold_today'] }}</td>
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
                                        {{ $counts['yellow_gold'] }}
                                        @if (isset($counts['variant_B']) && $counts['variant_B'] > 0)
                                            <span
                                                style="color: gold; font-weight: bold; margin-left: 5px;">{{ $counts['variant_B'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $counts['white_gold'] }}
                                        @if (isset($counts['variant_A']) && $counts['variant_A'] > 0)
                                            <span
                                                style="color: black; font-weight: bold; margin-left: 5px;">{{ $counts['variant_A'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $counts['rose_gold'] }}
                                        @if (isset($counts['variant_C']) && $counts['variant_C'] > 0)
                                            <span
                                                style="color: red; font-weight: bold; margin-left: 5px;">{{ $counts['variant_C'] }}</span>
                                        @endif
                                        @if (isset($counts['variant_D']) && $counts['variant_D'] > 0)
                                            <span
                                                style="color: blue; font-weight: bold; margin-left: 5px;">{{ $counts['variant_D'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $counts['all_rests'] }}
                                        @php
                                            $totalVariants =
                                                ($counts['variant_A'] ?? 0) +
                                                ($counts['variant_B'] ?? 0) +
                                                ($counts['variant_C'] ?? 0) +
                                                ($counts['variant_D'] ?? 0);
                                        @endphp
                                        @if ($totalVariants > 0)
                                            <span
                                                style="font-weight: bold; margin-left: 5px;">(+{{ $totalVariants }})</span>
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
