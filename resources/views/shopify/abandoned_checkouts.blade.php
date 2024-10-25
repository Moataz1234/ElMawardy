<!DOCTYPE html>
<html>
<body>
<h2>Abandoned Checkouts</h2>

    <table>
        <thead>
            <tr>
                <th>Checkout ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Region</th>
                <th>Recovery Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checkouts as $checkout)
                <tr>
                    <td>{{ $checkout['id'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($checkout['created_at'])->format('F j, Y, g:i a') }}</td>
                    
                    <td>
                        @if(isset($checkout['customer']))
                            {{ $checkout['customer']['name'] ?? 'No Name' }}
                        @else
                            No Customer Info
                        @endif
                    </td>

                    <!-- Check if the region exists -->
                    <td>
                        @if(isset($checkout['customer']['region']))
                            {{ $checkout['customer']['region'] }}
                        @else
                            No Region
                        @endif
                    </td>

                    <td>
                    @if(isset($checkout['recovery_status']))
                        {{  $checkout['recovery_status']}}
                    @else
                    No recovery status
                    @endif
                    </td>
                    <td>
                        @if(isset($checkout['total_price']))
                            {{  $checkout['total_price']}}
                        @else
                        No total price
                        @endif
                        </td>
                    <td>{{ $checkout['total_price'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $checkouts->links() }}
</body>
</html>