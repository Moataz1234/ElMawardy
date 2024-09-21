
<head>
    @include('layouts.app')
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>
<div class="diamond-list">
    <a class="diamond-list" href="{{route('new-item.create')}}">ادخال قطعة جديدة</a>
    </div>
    {{-- <div class="diamond-list">
        <a class="diamond-list" href="{{route('new-item.create')}}">ادخال رقم الدفتر</a>
        </div> --}}

        {{-- Report for shops about models --}}
        <form action="{{ route('search.model') }}" method="GET">
            @csrf
            <label for="model">Enter Model Name:</label>
            <input type="text" name="model" id="model" required>
            <button type="submit">Check Pieces in Store</button>
        </form>
        
        @if(isset($piecesDetails) && $piecesDetails->isNotEmpty())
        <p>Number of pieces in store: {{ $piecesInStore }}</p>
        
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Shop</th>
                <th>Kind</th>
                <th>Cost</th>
                <th>Calico 1</th>
                <th>Weight 1</th>

                @if($piecesDetails->first()->calico2 !== 'H')
                    <th>Calico 2</th>
                    <th>Number 2</th>
                    <th>Weight 2</th>
                @endif

                @if($piecesDetails->first()->calico3 !== 'H')
                    <th>Calico 3</th>
                    <th>Number 3</th>
                    <th>Weight 3</th>
                @endif

                @if($piecesDetails->first()->calico4 !== 'H')
                    <th>Calico 4</th>
                    <th>Number 4</th>
                    <th>Weight 4</th>
                @endif

                @if($piecesDetails->first()->calico5 !== 'H')
                    <th>Calico 5</th>
                    <th>Number 5</th>
                    <th>Weight 5</th>
                @endif

                @if($piecesDetails->first()->calico6 !== 'H')
                    <th>Calico 6</th>
                    <th>Number 6</th>
                    <th>Weight 6</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach($piecesDetails as $piece)
                <tr>
                    <td>{{ $piece->code }}</td>
                    <td>{{ $piece->name }}</td>
                    <td>{{ $piece->kind }}</td>
                    <td>{{ $piece->cost }}</td>
                    <td>{{ $piece->calico1 }}</td>
                    <td>{{ $piece->weight1 }}</td>

                    @if($piece->calico2 !== 'H')
                        <td>{{ $piece->calico2 }}</td>
                        <td>{{ $piece->number2 }}</td>
                        <td>{{ $piece->weight2 }}</td>
                    @endif

                    @if($piece->calico3 !== 'H')
                        <td>{{ $piece->calico3 }}</td>
                        <td>{{ $piece->number3 }}</td>
                        <td>{{ $piece->weight3 }}</td>
                    @endif

                    @if($piece->calico4 !== 'H')
                        <td>{{ $piece->calico4 }}</td>
                        <td>{{ $piece->number4 }}</td>
                        <td>{{ $piece->weight4 }}</td>
                    @endif

                    @if($piece->calico5 !== 'H')
                        <td>{{ $piece->calico5 }}</td>
                        <td>{{ $piece->number5 }}</td>
                        <td>{{ $piece->weight5 }}</td>
                    @endif

                    @if($piece->calico6 !== 'H')
                        <td>{{ $piece->calico6 }}</td>
                        <td>{{ $piece->number6 }}</td>
                        <td>{{ $piece->weight6 }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
        
        @endif
     
     
        {{-- @if(isset($shops) && $shops->isNotEmpty())
    <p>Available in the following shops:</p>
    <ul>
        @foreach($shops as $shop)
            <li>{{ $shop }}</li>
        @endforeach
    </ul>
@elseif(isset($piecesInStore) && $piecesInStore == 0)
    <p>No pieces available in store.</p>
@endif --}}
