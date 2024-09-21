@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gold Items List</h1>
    <table>
        <thead>
            <tr>
                <th>Link</th>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Shop ID</th>
                <th>Kind</th>
                <th>Model</th>
                <th>Talab</th>
                <th>Gold Color</th>
                <th>Stones</th>
                <th>Metal Type</th>
                <th>Metal Purity</th>
                <th>Quantity</th>
                <th>Weight</th>
                <th>Rest Since</th>
                <th>Source</th>
                <th>To Print</th>
                <th>Price</th>
                <th>Semi or No</th>
                <th>Average of Stones</th>
                <th>Net Weight</th>
            </tr>
        </thead>
        <tbody>
            @foreach($goldItems as $item)
            <tr>
                <td>{{ $item->link }}</td>
                <td>{{ $item->serial_number }}</td>
                <td>{{ $item->shop_name }}</td>
                <td>{{ $item->shop_id }}</td>
                <td>{{ $item->kind }}</td>
                <td>{{ $item->model }}</td>
                <td>{{ $item->talab }}</td>
                <td>{{ $item->gold_color }}</td>
                <td>{{ $item->stones }}</td>
                <td>{{ $item->metal_type }}</td>
                <td>{{ $item->metal_purity }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->weight }}</td>
                <td>{{ $item->rest_since }}</td>
                <td>{{ $item->source }}</td>
                <td>{{ $item->to_print ? 'Yes' : 'No' }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->semi_or_no }}</td>
                <td>{{ $item->average_of_stones }}</td>
                <td>{{ $item->net_weight }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
