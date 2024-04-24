<!DOCTYPE html>
<html>
  <head>
    <title>Cetak Nota 123456789</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.5;
      }
      .sheet {
        max-width: 300px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
      }
      .txt-left {
        text-align: left;
      }
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
      }

      /** Paper sizes **/
      body.struk .sheet {
        width: 58mm;
      }
      body.struk .sheet {
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

      /** For screen preview **/
      @media screen {
        body {
          background: #e0e0e0;
          font-family: monospace;
        }
        .sheet {
          background: white;
          box-shadow: 0 0.5mm 2mm rgba(0, 0, 0, 0.3);
          margin: 5mm;
        }
      }

      /** Fix for Chrome issue #273306 **/
      @media print {
        body {
          font-family: monospace;
        }
        body.struk {
          width: 58mm;
          text-align: left;
        }
        body.struk .sheet {
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
      }
    </style>
  </head>
  <body>
    <section class="sheet">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td>Toko Nay</td>
        </tr>
        <tr>
          <td>Jl. Muncang Raya No. 123</td>
        </tr>
      </table>
      <hr />
      <table cellpadding="0" cellspacing="0" style="width: 100%">
        <tr>
          <td align="left" class="txt-left">No transaksi</td>
          <td align="left" class="txt-left">:</td>
          <td align="left" class="txt-left">&nbsp;{{ $detail_transaction['no_transaction'] }} .</td>
        </tr>
        <tr>
          <td align="left" class="txt-left">Tgl.&nbsp;</td>
          <td align="left" class="txt-left">:</td>
          <td align="left" class="txt-left">&nbsp;{{ $detail_transaction['date'] }} {{ $detail_transaction['time'] }}</td>
        </tr>
        <tr>
          <td align="left" colspan="3" class="txt-left">Suplier</td>
        </tr>
      </table>
      <br />
      <table cellpadding="0" cellspacing="0" style="width: 100%">
        <tr>
          <td align="left" class="txt-left">Item Qty Harga Total</td>
        </tr>
        <tr>
          <td align="left" class="txt-left">
            ========================================
          </td>
        </tr>
        @foreach($detail_transaction['items'] as $detail)
        <tr>
          <td align="left" class="txt-left">{{ $detail['name'] }}</td>
        </tr>
        <tr>
          <td class="txt-left" align="left">{{ $detail['quantity']}} x Rp. {{ $detail['item_price'] }} Rp. {{ $detail['quantity'] * $detail['item_price'] }}</td>
        </tr>
        @endforeach
        <tr>
          <td>----------------------------------------</td>
        </tr>
        <tr>
          <td>Sub&nbsp;Total Rp. {{ $detail_transaction['total_price'] }}</td>
        </tr>
        <tr>
          <td>BAYAR Rp. {{ $detail_transaction['cash'] }}</td>
        </tr>
        <tr>
          <td>KEMBALI Rp. {{ $detail_transaction['change'] }}</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
      <p>*****&nbsp;Terima kasih atas kunjungan anda&nbsp;*****</p>
      <br /><br /><br /><br />
      <p>&nbsp;</p>
    </section>
  </body>

</html>
