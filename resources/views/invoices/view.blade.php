<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #444;
        }

        p, ul {
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error {
            font-weight: bold;
            text-align: center;
        }

        .product-lines {
            margin-top: 20px;
        }

        .product-lines ul {
            list-style: none;
            padding: 0;
        }

        .product-lines li {
            background: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .product-lines li strong {
            margin-bottom: 5px;
        }
        .product-line-details {
            display: block;
        }

        .total-price {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }
    </style>
    <script>
        setTimeout(() => {
            const element = document.getElementById('flash-message');
            if (element) element.style.display = 'none';
        }, 7000);
    </script>
</head>
<body>
<div class="container">
    @if(session('success'))
        <div id="flash-message" class="alert alert-success" style="color: green; padding: 15px; margin-bottom: 20px; border: 1px solid #d6e9c6; border-radius: 4px; background-color: #dff0d8;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="alert alert-danger" style="color: #a94442; padding: 15px; margin-bottom: 20px; border: 1px solid #ebccd1; border-radius: 4px; background-color: #f2dede;">
            {{ session('error') }}
        </div>
    @endif
    <h1>Invoice Details</h1>

    @if (isset($error))
        <p class="error">{{ $error }}</p>
    @else
        <p><strong>Invoice ID:</strong> {{ $invoice->getId() }}</p>
        <p><strong>Invoice Status:</strong> {{ $invoice->getStatus()->value }}</p>
        <p><strong>Customer Name:</strong> {{ $invoice->getCustomer()->getName() }}</p>
        <p><strong>Customer Email:</strong> {{ $invoice->getCustomer()->getEmail() }}</p>

        <div class="product-lines">
            <h2>Product Lines</h2>
            <ul>
                @foreach ($invoice->getProductLines() as $productLine)
                    <li>
                        <div class="product-line-details">
                            <strong>{{ $productLine->getName() }}</strong>
                        </div>
                        <div class="product-line-details">
                            <strong>Quantity:</strong> {{ $productLine->getQuantity() }}
                        </div>
                        <div class="product-line-details">
                            <strong>Unit Price:</strong> {{ number_format($productLine->getUnitPrice()->getAmount() / 100, 2) }} {{ $invoice->getTotalPrice()->getCurrency() }}
                        </div>
                        <div class="product-line-details">
                            <strong>Total Unit Price:</strong> {{ number_format($productLine->getTotalUnitPrice()->getAmount() / 100, 2) }} {{ $invoice->getTotalPrice()->getCurrency() }}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="total-price"><strong>Total Price:</strong> {{ number_format($invoice->getTotalPrice()->getAmount() / 100, 2) }} {{ $invoice->getTotalPrice()->getCurrency() }}</p>
        <form action="{{ route('invoices.send') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $invoice->getId() }}">
            <button type="submit">Send Invoice</button>
        </form>
    @endif
</div>
</body>
</html>
