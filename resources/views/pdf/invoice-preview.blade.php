<style>
    body {
        font-family: Arial, sans-serif;
        padding: 30px;
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
            <img src="{{ asset('/images/sws-emarketing.png') }}" alt="SWS eMarketing Inc Logo" style="height:70px;">
        </td>
        <td class="text-right" style="font-size: 36px; font-weight: 300;">INVOICE</td>
    </tr>
    <tr>
        <td class="text-right" style="font-size:14px;">
            <b>SWS eMarketing Inc</b><br>
            203-125 Commerce Valley Dr. W.,<br>
            Thornhill, ON, L3T 7W4<br>
            Canada
        </td>
    </tr>
</table>

<div class="border-bottom" style="margin: 10px 0;"></div>

<table>
    <tr>
        <td class="w-50">
            <span style="font-size:14px; color: #999;">BILL TO</span><br>
            <b>test</b><br>
            test<br>
            ewerwer
        </td>
        <td class="w-50">
            <table>
                <tr>
                    <td class="text-right" style="width: 60%;"><b>Invoice Number:</b></td>
                    <td>3</td>
                </tr>
                <tr>
                    <td class="text-right"><b>Invoice Date:</b></td>
                    <td>Jun 27, 2024</td>
                </tr>
                <tr>
                    <td class="text-right"><b>Payment Due:</b></td>
                    <td>Jun 27, 2024</td>
                </tr>
                <tr>
                    <td class="text-right background-gray"><b>Amount Due (USD):</b></td>
                    <td class="background-gray">$-155.60</td>
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
    <tr>
        <td>test</td>
        <td>$30.00</td>
        <td>4</td>
        <td class="text-right">$120.00</td>
        <td class="text-right">$15.60</td>
    </tr>
</table>

<div class="border-top" style="margin-top: 10px;"></div>

<table style="margin-top: 10px;">
    <tr>
        <td class="w-50"></td>
        <td class="w-50">
            <table>
                <tr>
                    <td class="text-right" style="width: 67%;"><b>Subtotal:</b></td>
                    <td class="text-right">$120.00</td>
                </tr>
                <tr>
                    <td class="text-right">HST 13% (801143694RT0001):</td>
                    <td class="text-right">$15.60</td>
                </tr>
            </table>
            <div class="border-bottom" style="margin: 5px 0;"></div>

            <table>
                <tr>
                    <td class="text-right" style="width: 67%;"><b>Total:</b></td>
                    <td class="text-right">$135.60 USD</td>
                </tr>
                <tr>
                    <td class="text-right">Payment on Jun 28, 2024 by cheque:</td>
                    <td class="text-right">$20.00 USD</td>
                </tr>
                <tr>
                    <td class="text-right">Payment on Jun 28, 2024 by eft:</td>
                    <td class="text-right">$135.60 USD</td>
                </tr>
                <tr>
                    <td class="text-right">Payment on Jun 28, 2024 by cheque:</td>
                    <td class="text-right">$135.60 USD</td>
                </tr>
            </table>
            <div class="border-bottom" style="margin: 5px 0;"></div>

        </td>
    </tr>
</table>
