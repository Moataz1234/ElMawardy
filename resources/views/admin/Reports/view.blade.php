@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sales Reports</h1>
    
    <form action="{{ route('reports.view') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="date" name="date" value="{{ $selectedDate }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    @if(count($reportsData) > 0)
        @foreach($reportsData as $model => $data)
            <div class="card mb-4">
                <div class="card-header">
                    <h3>{{ $model }}</h3>
                    <p>Total Sold: {{ $data['total_sold'] }} | Total Weight: {{ $data['total_weight'] }}g | Total Price: ${{ number_format($data['total_price'], 2) }}</p>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Shop</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Sold Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['items'] as $item)
                                <tr>
                                    <td>{{ $item->serial_number }}</td>
                                    <td>{{ $item->shop_name }}</td>
                                    <td>{{ $item->weight }}g</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->sold_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">No sales found for this date</div>
    @endif
</div>
@endsection
