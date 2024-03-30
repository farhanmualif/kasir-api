<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Merchant Copy';
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .date-supplier {
            display: flex;
            justify-content: space-between;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            padding: 8px;
            text-align: left;
        }
        .total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <div class="header">

            <p><h2>Toko Nay</h2> Ds. Muncang</p>
        </div>
        <table class="items">
            <tr>
                <th>{{ $detail_transaction['date'] }} <br> {{ $detail_transaction['time'] }}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>supplier</th>
            </tr>
        </table>
        <p>---------------------------------------------------------------</p>
        <table class="items">
            @foreach($detail_transaction['items'] as $detail)
            <tr>
                <td>{{ $detail['name'] }} <br> {{ $detail['quantity'] }} x {{ $detail['item_price'] }}</td>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <td>Rp. {{ $detail['quantity'] * $detail['item_price'] }}</td>
            </tr>
            @endforeach
        </table>
        <p>---------------------------------------------------------------</p>
        <table class="items">
            <tr>
                <th>Subtotal <br> <span style="font-size:15px; text-align: right">Total</span> <br> Cash <br> Kembali</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Rp. {{ $detail_transaction['total_price'] }} <br> <span style="font-size:15px">Rp. {{ $detail_transaction['total_price'] }}</span> <br>Rp. {{ $detail_transaction['cash'] }} <br>Rp. {{ $detail_transaction['change'] }}</th>
            </tr>
        </table>
    </div>
</body>

</html>
