<style>
    body {
        font-family: Arial, sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    .border-bottom {
        border-bottom: 1px solid #ccc;
    }
    .border-top {
        border-top: 1px solid #ccc;
    }
    .background-gray {
        background-color: #f2f2f2;
    }
    .text-right {
        text-align: right;
    }
    .w-50 {
        width: 50%;
    }
</style>

<table>
    <tr>
        <td rowspan="2" valign="middle" style="width: 50%;">
            <img src="data:image/png;base64,{{ $myCompany['logo'] }}" alt="{{ $invoice->company?->name }}" style="height:70px;">
        </td>
        <td class="text-right" style="font-size: 36px; font-weight: 300;">INVOICE</td>
    </tr>
    <tr>
        <td class="text-right" style="font-size:14px;">
            <b>{{ $invoice->company?->name }}</b><br>
            {{ $invoice->company?->address }}<br>
            {{ $invoice->company?->country }}
        </td>
    </tr>
</table>

<div class="border-bottom" style="margin: 10px 0;"></div>

<table>
    <tr>
        <td class="w-50">
            <span style="font-size:14px; color: #999;">BILL TO</span><br>
            <b>{{ $invoice->client?->company_name }}</b><br>
            {{ $invoice->client?->company_address }}<br>
            {{ $invoice->client?->company_email }}<br>
        </td>
        <td class="w-50">
            <table>
                <tr>
                    <td class="text-right" style="width: 60%;"><b>Invoice Number:</b></td>
                    <td>{{ $invoice->invoice_num }}</td>
                </tr>
                <tr>
                    <td class="text-right"><b>Invoice Date:</b></td>
                    <td>{{ $invoice->invoice_date }}</td>
                </tr>
                <tr>
                    <td class="text-right"><b>Payment Due:</b></td>
                    <td>{{ $invoice->invoice_due }}</td>
                </tr>
                <tr>
                    <td class="text-right background-gray"><b>Amount Due:</b></td>
                    <td class="background-gray">{{ $invoice->currency }} {{ $invoice->balance }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table style="margin-top: 20px;">
    <tr>
        <th>Service</th>
        <th>Price</th>
        <th>Quantity</th>
        <th class="text-right">Amount</th>
        <th class="text-right">Tax</th>
    </tr>
    @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td>$ {{ $item->rate }}</td>
            <td>{{ $item->qty }}</td>
            <td class="text-right">$ {{ $item->rate * $item->qty }}</td>
            <td class="text-right">{{ $item->tax ? '13%' : '' }}</td>
        </tr>
    @endforeach
</table>

<div class="border-top" style="margin-top: 10px;"></div>

<table style="margin-top: 10px;">
    <tr>
        <td class="w-50"></td>
        <td class="w-50">
            <table>
                <tr>
                    <td class="text-right" style="width: 67%;"><b>Subtotal:</b></td>
                    <td class="text-right">${{ $invoice->subtotal }}</td>
                </tr>
                <tr>
                    <td class="text-right">HST 13% (801143694RT0001):</td>
                    <td class="text-right">${{ $invoice->tax }}</td>
                </tr>
            </table>
            <div class="border-bottom" style="margin: 5px 0;"></div>

            <table>
                <tr>
                    <td class="text-right" style="width: 67%;"><b>Total:</b></td>
                    <td class="text-right">${{ $invoice->total }} {{ $invoice->currency }}</td>
                </tr>
                @forelse($invoice->payments as $payment)
                    <tr>
                        <td class="text-right">{{ $payment->note }} on {{ $payment->date }} by {{ $payment->type }}:</td>
                        <td class="text-right">${{ $payment->amount }} {{ $invoice->currency }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-right">No Payment</td>
                    </tr>
                @endforelse
            </table>
            <div class="border-bottom" style="margin: 5px 0;"></div>

        </td>
    </tr>
</table>
