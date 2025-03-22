<!DOCTYPE html>
<html>
<head>
    <title>QR Code</title>
</head>
<body>
    {!! QrCode::format('png')
    ->size(300)
    ->generate('tel:+1234567890'); !!}
</body>
</html>
