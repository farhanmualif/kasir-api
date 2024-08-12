<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Repositories\SalesReportRepository;
use App\Services\SalesReportService;
use Carbon\Carbon;

class SalesReportServiceImpl implements SalesReportService
{

    public function __construct(public SalesReportRepository $salesReportRepository)
    {
    }
    /**
     * @inheritDoc
     */
    public function getDailySales(string $date)
    {

        try {

            $dailySales = $this->salesReportRepository->daily($date)->get();


            if ($dailySales->count() === 0) {
                throw new ApiException("data belum tersedia", 404);
            }

            // Mengorganisir data
            $organizedData = [];
            foreach ($dailySales as $transaction) {
                if (!isset($organizedData[$transaction->transaction_id])) {
                    $organizedData[$transaction->transaction_id] = [
                        'time' => $transaction->time,
                        'id_store' => $transaction->store_id,
                        'revenue' => $transaction->revenue,
                        'profit' => $transaction->profit,
                        'no_transaction' => $transaction->no_transaction,
                        'items' => []
                    ];
                }

                $organizedData[$transaction->transaction_id]['items'][] = [
                    'id' => $transaction->product_id,
                    'name' => $transaction->product_name,
                    'quantity' => $transaction->quantity,
                    'price' => $transaction->price,
                    'total_price' => $transaction->total_price
                ];
            }

            // Menghitung total
            $totalTransactions = count($organizedData);
            $totalRevenue = array_sum(array_column($organizedData, 'revenue'));
            $totalProfit = array_sum(array_column($organizedData, 'profit'));

            // Menyusun respons
            $response = [
                'date' => Carbon::parse($dailySales->first()->created_at)->format('M d, Y'),
                'total_transactions' => $totalTransactions,
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
                'transactions' => array_values($organizedData)
            ];

            return $response;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getMonthlySales(string $date)
    {
        try {
            // Parse tanggal
            $date = Carbon::createFromFormat("Y-m-d", $date);

            $month = $date->month;
            $year = $date->year;

            $data = $this->salesReportRepository->monthly($month, $year)->get();

            if (count($data) == 0) {
                throw new ApiException("data belum tersedia");
            }


            $transactions = $data->map(function ($item) use ($year, $month) {
                return [
                    "link" => url("/api/sales/daily/{$year}-{$month}-{$item->day}"),
                    "date" => (string) $item->day,
                    "income" => (int)$item->income,
                    "transaction_amount" => $item->transaction_amount,
                    "profit" => (int)$item->profit
                ];
            });

            $result = [

                "link" => url("/api/sales/monthly/{$year}-{$month}"),
                "total_transactions" =>  $data->sum('transaction_amount'),
                "total_income" => $data->sum('income'),
                "total_profit" => $data->sum('profit'),
                "month" => date('F', mktime(0, 0, 0, $month, 1)),
                "month_number" => (string)$month,
                "year" => (string)$year,
                "transactions" => $transactions

            ];

            return $result;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getYearlySales(string $date)
    {
        try {
            $date = explode("-", $date);
            $date = "$date[0]";
            $date = Carbon::createFromFormat("Y", $date);

            $salesData = $this->salesReportRepository->yearly($date->year)->get();
            if ($salesData->count() === 0) {
                throw new ApiException("data belm tersedia", 404);
            }

            $data = [
                'link' => \url()->current(),
                'total_transactions' => $salesData->first()->total_transaction,
                'total_income' => $salesData->sum('income'),
                'total_profit' => $salesData->sum('profit'),
                'year' => $salesData->first()->year,
                'transactions' => [],
            ];

            foreach ($salesData as $sale) {
                $data['transactions'][] = [
                    'link' => \url()->previous() . "/api/sales/monthly/{$sale->year}-{$sale->month_number}",
                    'date' => $sale->month,
                    'month_num' => $sale->month_number,
                    'income' => $sale->income,
                    'profit' => $sale->profit,
                ];
            }

            return  $data;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), 404);
        }
    }
}
