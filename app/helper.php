<?php

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (!function_exists("generateNoTransaction")) {
    function generateNoTransaction()
    {
        return date("YmdHis") . rand(5, 6);
    }
}


if (!function_exists("responseJson")) {
    function responseJson(string $message, $data = null, bool $status = true, int $status_code = 200): JsonResponse
    {
        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ])->setStatusCode($status_code);
    }
}
if (!function_exists("generateInvoice")) {
    function generateInvoice(string $no_transaction)
    {
        try {
            $transaction = DB::table('transactions')
                ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
                ->join('products', 'detail_transactions.id_product', '=', 'products.id')
                ->select(
                    'transactions.no_transaction',
                    'products.name',
                    'detail_transactions.quantity',
                    'detail_transactions.item_price',
                    'detail_transactions.total_price',
                    'transactions.total_payment',
                    'transactions.cash',
                    DB::raw('TIME(transactions.created_at) as time'),
                    DB::raw('DATE(transactions.created_at) as date'),
                )
                ->where('transactions.no_transaction', '=', $no_transaction)
                ->get();



            $total_price = 0;
            foreach ($transaction as $item) {
                $total_price += $item->total_price;
            }

            $detail_transaction = [
                'no_transaction' => $transaction->first()->no_transaction,
                'time' => $transaction->first()->time,
                'date' => $transaction->first()->date,
                'cash' => intval($transaction->first()->cash),
                'change' => $transaction->first()->cash - $transaction->first()->total_price,
                'total_price' => intval($total_price),
                'total_payment' => intval($transaction->first()->total_payment),
                'items' => []
            ];

            foreach ($transaction as $items) {
                $detail_transaction['items'][] = [
                    'name' => $items->name,
                    'quantity' => $items->quantity,
                    'item_price' => $items->item_price,
                    'total_price' => $items->total_price
                ];
            }

            // $pdf = App::make('dompdf.wrapper');
            // $pdf->loadView('invoice', compact('detail_transaction'));
            $pdf = FacadePdf::loadView('invoice', compact('detail_transaction'));
            $pdf->setPaper('A4', 'portrait');
            // Set lebar kertas menjadi 8cm
            // Simpan file PDF ke storage
            $pdf_filename = 'invoice_' . $no_transaction . '.pdf';
            Storage::put('public/invoices/' . $pdf_filename, $pdf->output());

            // Return the PDF file URL
            return "berhasil menyimpan struk";
        } catch (\Throwable $th) {
            return responseJson("tidak dapat menampilkan struk", null, false, 500);
        }
    }
}
