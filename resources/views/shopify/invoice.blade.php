<!-- Create new file: resources/views/pdf/invoice.blade.php -->
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: ltr;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            font-size: 16px;
            line-height: 24px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 20px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .addresses {
            margin-bottom: 30px;
        }
        .billing-shipping {
            width: 100%;
            margin-bottom: 20px;
        }
        .billing-shipping td {
            width: 50%;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f8f8;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .totals-table td {
            padding: 5px;
        }
        .totals-table .label {
            text-align: right;
            font-weight: bold;
        }
        .arabic {
            direction: rtl;
            text-align: right;
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1>{{ $company['name'] }}</h1>
            <h2 >فاتورة</h2>
        </div>

        <div class="company-info">
            Tax ID: {{ $company['tax_id'] }}<br>
            {{ $company['address'] }}<br>
            {{ $company['city'] }}, {{ $company['postal_code'] }}
        </div>
        <div style="margin-bottom: 20px">
            <strong>Invoice #:</strong> {{ $invoice_number }}<br>
            <strong>Invoice Date:</strong> {{ $invoice_date }}
        </div>
        <table class="billing-shipping">
            <tr>
                <td>
                    <strong>Bill To</strong><br>
                    {{ $customer['name'] }}<br>
                    <div class="arabic">
                        {{ $customer['address']['line1'] }}<br>
                        {{ $customer['address']['line2'] }}<br>
                        {{ $customer['address']['city'] }}
                    </div>
                </td>
                <td>
                    <strong>Ship To</strong><br>
                    {{ $customer['name'] }}<br>
                    <div class="arabic">
                        {{ $customer['address']['line1'] }}<br>
                        {{ $customer['address']['line2'] }}<br>
                        {{ $customer['address']['city'] }}
                    </div>
                </td>
            </tr>
        </table>

     

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ number_format($item['price'], 2) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="label">Total:</td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Paid:</td>
                <td>{{ number_format($paid, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Balance Due:</td>
                <td>{{ number_format($balance_due, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>