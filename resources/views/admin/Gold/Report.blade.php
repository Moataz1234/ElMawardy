
    <style>
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .report-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
    <div class="container" style="font-family: Arial, sans-serif;">
        <h1>Gold Inventory Report by Model</h1>

        <!-- Search Form -->
        <form action="{{ route('gold.report') }}" method="GET" style="margin-bottom: 20px;">
            <div class="form-group">
                <label for="model">Search by Model Name:</label>
                <input type="text" name="model" id="model" class="form-control" placeholder="Enter model name" value="{{ request('model') }}">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        @if($modelsData->isEmpty())
            <p>No results found for the model '{{ request('model') }}'. Please enter a valid model name.</p>
        @else
            <!-- Table showing the models data -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Image</th>
                        <th>Total Produced</th>
                        <th>Total Sold</th>
                        <th>Remaining</th>
                        <th>Shops with this Item</th>
                        <th>Gold Color</th>
                        <th>Source</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modelsData as $model => $data)
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $model }}</td>
                            <td style="border: 1px solid #000; padding: 5px;">
                                @if($data['link'])
                                    <img src="{{ asset($data['link']) }}" alt="Model Image" width="50">
                                @else
                                    No Image
                                @endif
                            </td>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $data['total_production'] }}</td>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $data['total_sold'] }}</td>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $data['remaining'] }}</td>
                            <td style="border: 1px solid #000; padding: 5px;">
                                @foreach($data['shops'] as $shop)
                                    {{ $shop }}<br>
                                @endforeach
                            </td>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $data['gold_color'] }}</td>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $data['source'] ?? 'N/A' }}</td>
                            <td style="border: 1px solid #000; padding: 5px;">{{ $data['source'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
