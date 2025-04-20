<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Page</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        h1 {
            margin-bottom: 20px;
        }
        .invoice-buttons {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            width: 400px; /* Zwiększona szerokość kontenera */
        }
        button {
            display: block;
            width: 100%; /* Przyciski zajmują całą szerokość kontenera */
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            color: white;
            white-space: nowrap; /* Zapobiega zawijaniu tekstu */
        }
        button:hover {
            background-color: #0056b3;
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

<h1>Required endpoints</h1>

<h3> Create invoice </h3>
<form action="{{ url('invoices/create') }}" method="POST">
    @csrf
    <button type="submit">Create Invoice</button>
</form>

<h3> View invoices </h3>
<p> To view invoice, press button with the invoice id. </p>
<div class="invoice-buttons">
    @if (isset($invoices) && count($invoices) > 0)
        @foreach ($invoices as $invoice)
            <form action="{{ route('invoices.view') }}" method="GET">
                <input type="hidden" name="id" value="{{ $invoice->id }}">
                <button type="submit" style="background-color: #28a745;">
                    Invoice ID: {{ $invoice->id }}
                </button>
            </form>
        @endforeach
    @else
        <p>No invoices available.</p>
    @endif
</div>

{{--<form action="{{ route('invoices.view') }}" method="GET">--}}
{{--    <input--}}
{{--        type="text"--}}
{{--        name="id"--}}
{{--        required--}}
{{--        placeholder="Invoice ID..."--}}
{{--    >--}}
{{--    <button type="submit">View Invoice</button>--}}
{{--</form>--}}

{{--<button onclick="location.href='{{ url('/invoices/send') }}'">Send Invoice</button>--}}
</body>
</html>
