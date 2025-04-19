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
        button {
            display: block;
            width: 200px;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            color: white;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
@if (session('success'))
    <div style="color: green; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif
<h1>Required endpoints</h1>

<form action="{{ route('invoices.view') }}" method="GET">
    <input
        type="text"
        name="id"
        required
        placeholder="Invoice ID..."
    >
    <button type="submit">View Invoice</button>
</form>

<form action="{{ url('invoices/create') }}" method="POST">
    @csrf
    <button type="submit">Create Invoice</button>
</form>

<button onclick="location.href='{{ url('/invoices/send') }}'">Send Invoice</button>
</body>
</html>
