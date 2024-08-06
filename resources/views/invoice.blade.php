<!DOCTYPE html>
<html>

<head>
    <title>Cetak Nota {{ $detailTransaction['no_transaction'] }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            font-size: 10px;
            font-family: monospace;
        }

        td {
            font-size: 10px;
        }

        .sheet {
            margin: 0;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            page-break-after: always;
            width: 58mm;
            padding: 2mm;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        @media screen {
            body {
                background: #e0e0e0;
            }

            .sheet {
                background: white;
                box-shadow: 0 0.5mm 2mm rgba(0, 0, 0, 0.3);
                margin: 5mm;
            }
        }

        @media print {
            body {
                width: 58mm;
                text-align: left;
            }

            .sheet {
                padding: 2mm;
            }
        }
    </style>
</head>

<body class="struk">
    <section class="sheet">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td>Toko Nay</td>
            </tr>
            <tr>
                <td>Jl. Muncang Raya No. 123</td>
            </tr>
            <tr>
                <td>Telp: 081234567890</td>
            </tr>
        </table>

        ========================================

        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <tr>
                <td align="left" class="txt-left">Nota&nbsp;</td>
                <td align="left" class="txt-left">:</td>
                <td align="left" class="txt-left">&nbsp;{{ $detailTransaction['no_transaction'] }}</td>
            </tr>

            <tr>
                <td align="left" class="txt-left">Tgl.&nbsp;</td>
                <td align="left" class="txt-left">:</td>
                <td align="left" class="txt-left">&nbsp;{{ $detailTransaction['date'] }} {{ $detailTransaction['time'] }}</td>
            </tr>

        </table>
        <br />
        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <tr>
                <td align="left" class="txt-left">
                    Item&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qty&nbsp;&nbsp;&nbsp;&nbsp;Harga&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total
                </td>
            </tr>
            <tr>
                <td align="left" class="txt-left">
                    ======================================
                </td>
            </tr>
            @foreach($detailTransaction['items'] as $detail)
            <tr>
                <td align="left" class="txt-left">
                    {{ $detail['name'] }}&nbsp;&nbsp;&nbsp;&nbsp;{{ $detail['quantity'] }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $detail['item_price'] }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $detail['quantity'] * $detail['item_price'] }}
                </td>
            </tr>
            @endforeach
            <tr>
                <td>--------------------------------------</td>
            </tr>
            <tr>
                <td>
                    Sub&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $detailTransaction['total_price'] }}
                </td>
            </tr>
            <tr>
                <td>
                    Grand&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $detailTransaction['total_price'] }}
                </td>
            </tr>
            <tr>
                <td>
                    BAYAR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $detailTransaction['cash'] }}
                </td>
            </tr>
            <tr>
                <td>
                    KEMBALI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $detailTransaction['change'] }}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
        **Terima kasih atas kunjungan anda**
        <br /><br /><br /><br />
        <p>&nbsp;</p>
    </section>
</body>

</html>