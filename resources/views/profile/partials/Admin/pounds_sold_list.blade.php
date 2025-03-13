<div class="spreadsheet">
    <table class="table">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Kind</th>
                <th>Weight</th>
                <th>Purity</th>
                <th>Price</th>
                {{-- <th>Customer</th> --}}
                <th>Sold Date</th>
            </tr>
        </thead>
        <tbody id="pounds-table-body">   
            @foreach ($goldPounds as $pound)
                <tr>
                    <td>{{ $pound->serial_number }}</td>
                    <td>{{ $pound->shop_name }}</td>
                    <td>{{ $pound->goldPound->kind ?? 'N/A' }}</td>
                    <td>{{ $pound->goldPound->weight ?? 'N/A' }}</td>
                    <td>{{ $pound->goldPound->purity ?? 'N/A' }}</td>
                    <td>{{ $pound->price }}</td>
                    {{-- <td>{{ $pound->customer->name ?? 'N/A' }}</td> --}}
                    <td>{{ $pound->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 