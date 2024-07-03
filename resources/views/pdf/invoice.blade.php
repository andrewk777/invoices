<!DOCTYPE html>
<html>
<head>
    <title>Invoice Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .invoice {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 200px;
            height: auto;
        }
        .header h1 {
            font-size: 24px;
            margin: 10px 0;
        }
        .header p {
            margin: 0;
        }
        .bill-to {
            margin-bottom: 20px;
        }
        .bill-to h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details p {
            margin: 5px 0;
        }
        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-items th,
        .invoice-items td {
            padding: 8px;
            border: 1px solid #ccc;
        }
        .invoice-items th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .invoice-summary {
            margin-bottom: 20px;
        }
        .invoice-summary p {
            margin: 5px 0;
        }
        .invoice-payments {
            margin-top: 20px;
        }
        .invoice-payments p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="invoice">
    <div class="header">

        @if($invoice->company->name === 'SWS eMarketing Inc')
            <img src="{{ public_path('images/sws-emarketing.png') }}" alt="{{ $invoice->company_name }}">
        @else
            <img src="{{ public_path('images/oad-soft.png') }}" alt="{{ $invoice->company_name }}">
        @endif

        <h1>INVOICE</h1>
        <p>SWS eMarketing Inc</p>
        <p>203-125 Commerce Valley Dr. W.,</p>
        <p>Thornhill, ON, L3T 7W4</p>
        <p>Canada</p>
    </div>
    <div class="bill-to">
        <h2>BILL TO</h2>
        <p>{{ $invoice->client->company_name }}</p>
        <p>{{ $invoice->client->company_address }}</p>
    </div>
    <div class="invoice-details">
        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_num }}</p>
        <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</p>
        <p><strong>Payment Due:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_due)->format('M d, Y') }}</p>
        <p><strong>Amount Due (USD):</strong> $-{{ $invoice->balance }}</p>
    </div>
    <table class="invoice-items">
        <thead>
        <tr>
            <th>Description</th>
            <th>Rate</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Tax</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>${{ $item->rate }}</td>
                <td>{{ $item->qty }}</td>
                <td>${{ ($item->rate * $item->qty) }}</td>
                <td>${{ $item->tax }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="invoice-summary">
        <p><strong>Subtotal:</strong> ${{ $invoice->subtotal }}</p>
        <p><strong>HST 13% (801143594RT0001):</strong> ${{ $invoice->taxes }}</p>
        <p><strong>Total:</strong> ${{ $invoice->total }} USD</p>
    </div>
    <div class="invoice-payments">
        @foreach ($invoice->payments as $payment)
            <p>Payment on {{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }} by {{ $payment->type }}: ${{ $payment->amount }} USD</p>
        @endforeach
    </div>
</div>
</body>
</html>
