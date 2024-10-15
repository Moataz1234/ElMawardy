
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
            <table class="table">
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Image</th>
                        <th>Total Produced</th>
                        <th>Total Sold</th>
                        <th>Remaining</th>
                        <th>Shops with this Item</th>
                        <th>Gold Color</th>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Section (Dynamic Data) -->
            <div style="display: flex; justify-content: space-between; padding: 10px;">
                <div>
                    <h4>Total Production: {{ $totalProduction }}</h4>
                    <h4>Remaining: {{ $remaining }}</h4>
                    <h4>At Workshop: {{ $atWorkshop }}</h4> <!-- Dynamic workshop data -->
                </div>
                <div>
                    <h4>Total Sold: {{ $totalSold }}</h4>
                    <h4>Model: {{ request('model') }}</h4> <!-- Searched model shown here -->
                </div>
            </div>
        @endif
    </div>
