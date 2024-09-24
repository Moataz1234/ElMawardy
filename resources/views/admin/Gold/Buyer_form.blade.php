<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Details</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <form class="custom-form" action="{{ route('buyers.store', $goldItemSold->id) }}" method="POST">
        @csrf

        <label for="name">Buyer Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="email">Buyer Email:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="payment_method">Payment Method:</label>
        <input type="text" name="payment_method" id="payment_method" required><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
