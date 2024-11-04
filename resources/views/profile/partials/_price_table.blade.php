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