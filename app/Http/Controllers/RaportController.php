<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class RaportController extends Controller
{
    public function getSalesPerDay($date)
    {
        try {
            $date = Carbon::createFromFormat("Y-m-d", $date);
            $today = $date->format('Y-m-d');

            $salesData = DB::table('transactions')
                ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
                ->join('products', 'products.id', '=', 'detail_transactions.id_product')
                ->where('transactions.created_at', '>=', $today . ' 00:00:00')
                ->where('transactions.created_at', '<=', $today . ' 23:59:59')
                ->select(
                    'transactions.*',
                    'detail_transactions.*',
                    'products.name',
                    'products.barcode',
                    'products.stock',
                    'products.selling_price',
                    'products.purchase_price',
                    'products.image',
                    DB::raw('(detail_transactions.quantity * products.selling_price - detail_transactions.quantity * products.purchase_price) AS profit')
                )
                ->get();
            if ($salesData->count() === 0) {
                return \responseJson("data tidak ditemukan", null, false, 404);
            }

            // Group the sales data by no_transaction
            $groupedData = $salesData->groupBy('no_transaction');

            $result = [
                'total_transactions' => $groupedData->count(),
                'total_revenue' => $salesData->sum('total_price'),
                'total_profit' => $salesData->sum('profit'),
                "date" => Carbon::parse($salesData[0]->created_at)->format('M d, Y'),
                'transactions' => []
            ];

            // Iterate through each grouped no_transaction
            foreach ($groupedData as $transactions) {
                $revenue = $transactions->sum('total_price');
                $profit = $transactions->sum('profit');
                $items = [];
                $timestamps = [];

                // Iterate through each transaction item in the group
                foreach ($transactions as $transaction) {
                    $items[] = [
                        'name' => $transaction->name,
                        'quantity' => $transaction->quantity,
                        'price' => $transaction->selling_price,
                        'total_price' => $transaction->total_price
                    ];

                    // Format the timestamp using Carbon
                    $formattedTimestamp = \Carbon\Carbon::parse($transaction->created_at)->format('H:i:s');
                    $timestamps[] = $formattedTimestamp;
                }

                $result['transactions'][] = [
                    'time' => array_shift($timestamps),
                    'revenue' => $revenue,
                    'profit' => $profit,
                    'no_transaction' => $transaction->no_transaction,
                    'items' => $items
                ];
            }

            return \responseJson("berhasil mendapatkan data penjualan", $result);
        } catch (\Throwable $th) {
            return \responseJson("terjadi kesalahan {$th->getMessage()} {$th->getFile()} {$th->getLine()}");
        }
    }

    public function getSalesMonthly($date)
    {
        try {
            $date = explode("-", $date);
            $date = "$date[0]-$date[1]";
            $date = Carbon::createFromFormat("Y-m", $date);

            $salesData = DB::table('transactions')
                ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
                ->join('products', 'products.id', '=', 'detail_transactions.id_product')
                ->whereMonth('transactions.created_at', $date->month)
                ->whereYear('transactions.created_at', $date->year)
                ->select(
                    DB::raw('MONTHNAME(transactions.created_at) AS month'),
                    DB::raw('YEAR(transactions.created_at) AS year'),
                    DB::raw('MONTH(transactions.created_at) AS month_number'),
                    DB::raw('DAY(transactions.created_at) AS day'),
                    DB::raw('COUNT(*) as total_transaction'),
                    DB::raw('SUM(detail_transactions.quantity * products.selling_price) AS revenue'),
                    DB::raw('SUM(detail_transactions.quantity * (products.selling_price - products.purchase_price)) AS profit')
                )
                ->groupBy('month_number', 'day', 'month', 'year')
                ->get();
            if ($salesData->count() === 0) {
                return responseJson("data penjulaan tidak ditemukan", null, false, 404);
            }



            // format the data
            $data = [
                'link' => \url()->current(),
                'total_transactions' => $salesData->first()->total_transaction,
                'total_revenue' => $salesData->sum('revenue'),
                'total_profit' => $salesData->sum('profit'),
                'month' => $salesData->first()->month,
                'transactions' => [],
            ];

            foreach ($salesData as $sale) {
                $data['transactions'][] = [
                    'link' => \url()->previous() . "/api/daily-transaction/{$sale->year}-{$sale->month_number}-{$sale->day}",
                    'date' => "{$sale->month} {$sale->day}",
                    'revenue' => $sale->revenue,
                    'profit' => $sale->profit,
                ];
            }

            return \responseJson("berhasil mengambil data penjualan", $data);
        } catch (\Throwable $th) {
            return responseJson("gagal menambil data penjualan {$th->getMessage()} {$th->getFile()} {$th->getLine()}");
        }
    }
    public function getSalesYears($date)
    {
        try {

            $date = explode("-", $date);
            $date = "$date[0]";
            $date = Carbon::createFromFormat("Y", $date);


            $salesData = DB::table('transactions')
                ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
                ->join('products', 'products.id', '=', 'detail_transactions.id_product')
                ->whereYear('transactions.created_at', $date->year)
                ->select(
                    DB::raw('MONTHNAME(transactions.created_at) AS month'),
                    DB::raw('YEAR(transactions.created_at) AS year'),
                    DB::raw('MONTH(transactions.created_at) AS month_number'),
                    DB::raw('COUNT(*) as total_transaction'),
                    DB::raw('SUM(detail_transactions.quantity * products.selling_price) AS revenue'),
                    DB::raw('SUM(detail_transactions.quantity * (products.selling_price - products.purchase_price)) AS profit')
                )
                ->groupBy('month_number', 'month', 'year')
                ->get();
            if ($salesData->count() === 0) {
                return responseJson("data penjulaan tidak ditemukan", null, false, 404);
            }

            $data = [
                'link' => \url()->current(),
                'total_transactions' => $salesData->first()->total_transaction,
                'total_revenue' => $salesData->sum('revenue'),
                'total_profit' => $salesData->sum('profit'),
                'year' => $salesData->first()->year,
                'transactions' => [],
            ];

            foreach ($salesData as $sale) {
                $data['transactions'][] = [
                    'link' => \url()->previous() . "/api/mountly-transaction/{$sale->year}-{$sale->month_number}",
                    'date' => $sale->month,
                    'revenue' => $sale->revenue,
                    'profit' => $sale->profit,
                ];
            }

            return \responseJson("berhasil mengambil data penjualan", $data);
        } catch (\Throwable $th) {
            return responseJson("gagal menambil data penjualan {$th->getMessage()} {$th->getFile()} {$th->getLine()}");
        }
    }

    public function getDailyPurchases($date)
    {
        try {
            $date = Carbon::createFromFormat("Y-m-d", $date);

            $purchasingData = DB::table('purchasing')
                ->whereDay('purchasing.created_at', '=', $date->day)
                ->whereMonth('purchasing.created_at', '=', $date->month)
                ->whereYear('purchasing.created_at', '=', $date->year)
                ->join('products', 'products.id', '=', 'purchasing.product_id')
                ->select(
                    'purchasing.*',
                    'products.*',
                    DB::raw('MONTHNAME(purchasing.created_at) AS month'),
                    DB::raw('YEAR(purchasing.created_at) AS year'),
                    DB::raw('MONTH(purchasing.created_at) AS month_number'),
                    DB::raw('DAY(purchasing.created_at) AS day'),
                    DB::raw('SUM(purchasing.total_payment) as total_expenditure')
                )
                ->groupBy('month_number', 'day', 'month', 'year', 'purchasing.id')
                ->get();


            $total_expenditure = 0;
            foreach ($purchasingData as $data) {
                $total_expenditure += intval($data->total_expenditure);
            }

            $data = [
                "link" => \url()->current(),
                "total_transaction" => $purchasingData->count(),
                "total_expenditure" => $total_expenditure,
                "items_purchasing" => []
            ];

            foreach ($purchasingData as $purchas_item) {
                $data['items_purchasing'][] = [
                    "time" => Carbon::createFromFormat("Y-m-d H:m:s", $purchas_item->created_at)->format('H:m:s'),
                    "year" => $purchas_item->year,
                    "month" => $purchas_item->month,
                    "day" => $purchas_item->day,
                    "purchases" => intval($purchas_item->total_payment),
                    "no_transaction" => $purchas_item->no_purchasing
                ];
            }
            return \responseJson("berhasil mendapatkan data pembelian", $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function getmonthlyPurchases($date)
    {
        try {
            $date = explode("-", $date);
            $date = "$date[0]-$date[1]";
            $date = Carbon::createFromFormat("Y-m", $date);

            $monthData = DB::table('purchasing')
                ->whereMonth('created_at', '=', $date->month)
                ->whereYear('created_at', '=', $date->year,)
                ->select(
                    DB::raw('COUNT(*) AS total_transaction'),
                    DB::raw('SUM(total_payment) AS total_expenditure')
                )->first();


            $dailyData = DB::table('purchasing')
                ->whereMonth('created_at', '=', $date->month)
                ->whereYear('created_at', '=', $date->year)
                ->select(
                    DB::raw('COUNT(*) AS total_transaction'),
                    DB::raw('DATE(created_at) AS date'),
                    DB::raw('MONTH(created_at) AS month'),
                    DB::raw('MONTHNAME(created_at) AS month_name'),
                    DB::raw('SUM(total_payment) AS total_expenditure')
                )
                ->groupBy(DB::raw('DATE(created_at)'), 'month', 'date', 'month_name')
                ->get();


            foreach ($dailyData as $data) {
                $data->link = url('/api/daily-purchases/' . $data->date);
            }


            $response = [
                'link' => url()->current(),
                'total_transaction' => $monthData->total_transaction,
                'total_expenditure' => intval($monthData->total_expenditure),
                'daily_data' => $dailyData
            ];

            return \responseJson("berhasil mendapatkan data pembelian", $response);
        } catch (\Throwable $th) {
            return responseJson("gagal menambil data pembelian {$th->getMessage()} {$th->getFile()} {$th->getLine()}");
        }
    }
    public function getYearsPurchases($date)
    {
        try {
            $date = explode("-", $date);
            $date = "$date[0]";
            $date = Carbon::createFromFormat("Y", $date);

            $yearData = DB::table('purchasing')
                ->whereYear('created_at', '=', '2024') // Ganti '2024' dengan tahun yang diinginkan
                ->select(
                    DB::raw('COUNT(*) AS total_transaction'),
                    DB::raw('SUM(total_payment) AS total_expendeture')
                )
                ->first();

            $monthlyData = DB::table('purchasing')
                ->whereYear('created_at', '=', '2024') // Ganti '2024' dengan tahun yang diinginkan
                ->select(
                    DB::raw('MONTH(created_at) AS month'),
                    DB::raw('YEAR(created_at) AS year'),
                    DB::raw('COUNT(*) AS total_transaction'),
                    DB::raw('SUM(total_payment) AS total_expendeture')
                )
                ->groupBy(DB::raw('MONTH(created_at)'), 'year')
                ->get();
            // dd($monthlyData);

            foreach ($monthlyData as $data) {
                $data->link = url('/api/mounth-purchases/' . $data->year . '-' . $data->month);
            }

            $response = [
                'link' => url()->current(),
                'total_transaction' => $yearData->total_transaction,
                'total_expendeture' => \intval($yearData->total_expendeture),
                'monthly_data' => $monthlyData
            ];

            return \responseJson("berhasil mendapatkan data pembelian", $response);
        } catch (\Throwable $th) {
            return responseJson("gagal menambil data pembelian {$th->getMessage()} {$th->getFile()} {$th->getLine()}");
        }
    }
}
