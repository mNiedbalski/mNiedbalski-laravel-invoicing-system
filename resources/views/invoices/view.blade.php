<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
</head>
<body>
<h1>Invoice Details</h1>

@if (isset($invoice['error']))
    <p style="color: red;">{{ $invoice['error'] }}</p>
@else
    <p><strong>Invoice ID:</strong> {{ $invoice['Invoice ID'] }}</p>
    <p><strong>Invoice Status:</strong> {{ $invoice['Invoice Status'] }}</p>
    <p><strong>Customer Name:</strong> {{ $invoice['Customer Name'] }}</p>
    <p><strong>Customer Email:</strong> {{ $invoice['Customer Email'] }}</p>

    <h2>Product Lines</h2>
    <ul>
        @foreach ($invoice['Invoice Product Lines'] as $productLine)
            <li>
                <strong>Product Name:</strong> {{ $productLine['Product Name'] }}<br>
                <strong>Quantity:</strong> {{ $productLine['Quantity'] }}<br>
                <strong>Unit Price:</strong> {{ $productLine['Unit Price'] }}<br>
                <strong>Total Unit Price:</strong> {{ $productLine['Total Unit Price'] }}
            </li>
        @endforeach
    </ul>

    <p><strong>Total Price:</strong> {{ $invoice['Total Price'] }}</p>
@endif
</body>
</html>
