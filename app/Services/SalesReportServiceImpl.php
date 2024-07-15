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
            $date = Carbon::createFromFormat("Y-m-d", $date);
            $day = $date->format('Y-m-d');

            $dailySales = $this->salesReportRepository->daily($day)->get();

            if ($dailySales->count() === 0) {
                throw new ApiException("data tidak ditemukan", 404);
            }

            // Group the sales data by no_transaction
            $groupedData = $dailySales->groupBy('no_transaction');

            $result = [
                'total_transactions' => $groupedData->count(),
                'total_revenue' => $dailySales->sum('total_price'),
                'total_profit' => $dailySales->sum('profit'),
                "date" => Carbon::parse($dailySales[0]->created_at)->format('M d, Y'),
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
                    $formattedTimestamp = Carbon::parse($transaction->created_at)->format('H:i:s');
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

            return $result;
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
            $date = explode("-", $date);
            $date = "$date[0]-$date[1]";
            $date = Carbon::createFromFormat("Y-m", $date);

            $monthlySales = $this->salesReportRepository->monthly($date->month, $date->year)->get();

            $salesData = [
                'link' => \url()->current(),
                'total_transactions' => $monthlySales->first()->total_transaction,
                'total_income' => $monthlySales->sum('income'),
                'total_profit' => $monthlySales->sum('profit'),
                'month' => $monthlySales->first()->month,
                'month_number' => $monthlySales->first()->month_number,
                'year' => $monthlySales->first()->year,
                'transactions' => [],
            ];

            foreach ($monthlySales as $sale) {
                $salesData['transactions'][] = [
                    'link' => \url()->previous() . "/api/sales/daily/{$sale->year}-{$sale->month_number}-{$sale->day}",
                    'date' => (string) $sale->day,
                    'income' => intval($sale->income),
                    'transaction_amount' => $sale->transaction_amount,
                    'profit' => intval($sale->profit),
                ];
            }

            return $salesData;
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
                throw new ApiException("data penjulaan tidak ditemukan", 404);
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
                    'link' => \url()->previous() . "/api/sale/monthly/{$sale->year}-{$sale->month_number}",
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
