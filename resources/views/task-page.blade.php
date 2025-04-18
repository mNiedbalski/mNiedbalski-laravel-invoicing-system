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
<h1>Required endpoints</h1>
<button onclick="location.href='{{ url('/invoices/view') }}'">View Invoice</button>
<button onclick="location.href='{{ url('/invoices/create') }}'">Create Invoice</button>
<button onclick="location.href='{{ url('/invoices/send') }}'">Send Invoice</button>
</body>
</html>
