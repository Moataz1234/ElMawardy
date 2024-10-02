<!DOCTYPE html>
<html>
<head>
<svg  width="80px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 259.559 259.559" xml:space="preserve">
<g>
	<polygon style="fill:#19c5fa;" points="186.811,106.547 129.803,218.647 73.273,106.547 	"/>
	<polygon style="fill:#19c5fa;" points="78.548,94.614 129.779,43.382 181.011,94.614 	"/>
	<polygon style="fill:#19c5fa;" points="144.183,40.912 213.507,40.912 193.941,90.67 	"/>
	<polygon style="fill:#19c5fa;" points="66.375,89.912 50.044,40.912 115.375,40.912 	"/>
	<polygon style="fill:#19c5fa;" points="59.913,106.547 109.546,204.977 3.288,106.547 	"/>
	<polygon style="fill:#19c5fa;" points="200.2,106.547 256.271,106.547 150.258,204.75 	"/>
	<polygon style="fill:#19c5fa;" points="205.213,94.614 223.907,47.082 259.559,94.614 	"/>
	<polygon style="fill:#19c5fa;" points="38.331,43.507 55.373,94.614 0,94.614 	"/>
</g>
</svg>

</head>
{{-- <body>
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
<script>
    // JavaScript to toggle the visibility of the price table
    document.getElementById('togglePriceTable').addEventListener('click', function() {
        const priceTable = document.getElementById('priceTable');
        if (priceTable.style.display === 'none') {
            priceTable.style.display = 'block';
            this.innerText = 'Hide Prices';
        } else {
            priceTable.style.display = 'none';
            this.innerText = 'Show Prices';
        }
    });
	</script>
</body> --}}
</html>