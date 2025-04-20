<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Page</title>
    <link rel="stylesheet" href="{{ asset('styles/task-page.css') }}">
    <script>
        setTimeout(() => {
            const element = document.getElementById('flash-message');
            if (element) element.style.display = 'none';
        }, 5000);
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

    <h1>Required endpoints</h1>

    <h3> Create invoice </h3>
    <form action="{{ route('invoices.create') }}" method="POST">
        @csrf
        <button type="submit">Create Invoice</button>
    </form>

    <h3> View invoices </h3>
    <p>(sending button will be visible after clicking invoice button page)</p>
    <div class="invoice-buttons">
        @if (isset($invoices) && count($invoices) > 0)
            @foreach ($invoices as $invoice)
                <form action="{{ route('invoices.view') }}" method="GET">
                    <input type="hidden" name="id" value="{{ $invoice->id }}">
                    <button type="submit" class="button">
                        Invoice ID: {{ $invoice->id }}
                    </button>
                </form>
            @endforeach
        @else
            <p>No invoices available.</p>
        @endif
    </div>

    <h3> Create test invoices (for assessment purposes) </h3>
    <form action="{{ route('invoices.mock') }}" method="POST">
        @csrf
        <button type="submit">Fill the database!</button>
    </form>
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
