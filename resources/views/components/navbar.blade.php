<!DOCTYPE html>
<html>
<body>
{{-- <nav>
    <ul>
        <li><a href="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}">Shop Items</a></li>
        <li><a href="{{ route('transfer.requests') }}">Transfer Requests</a></li>
        <li><a href="{{ route('transfer.requests.history') }}">Transfer Request History</a></li>
        <li><a href="{{ route('gold-items.index') }}">Available Items</a></li>
        <li><a href="{{ route('gold-pounds.index') }}">Gold Pounds</a></li>
        <li><a href="{{ route('gold-items.create') }}">Add Gold Item</a></li>
    </ul>
</nav> --}}

      <button id="togglePriceTable" class="prices" >Show Updates</button>

      <!-- Hidden price table section -->
      <div id="priceTable" style="display: none;">
          <table>
              <tbody>
                  @foreach ($latestPrices as $price)
                      <tr>
                          <td>{{ $price->gold_buy }}/{{ $price->gold_sell }}</td>
                          <td>{{ $price->gold_with_work }}</td>
                          <td>{{ $price->dollar_price }}</td>
                          <td>{{ $price->percent }}</td>
                          <td>{{ $price->gold_in_diamond }}</td>
                          <td>{{ $price->shoghl_agnaby }}</td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  
</body>
<script>

</script>
</html>