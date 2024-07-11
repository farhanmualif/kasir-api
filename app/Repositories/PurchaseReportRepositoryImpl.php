<?php

namespace App\Repositories;

use App\Repositories\PurchaseReportRepository;
use Illuminate\Support\Facades\DB;

class PurchaseReportRepositoryImpl implements PurchaseReportRepository
{

    /**
     * @inheritDoc
     */
    public function daily(string $date, string $month, string $year)
    {
        return DB::table('purchasing')
            ->whereDay('purchasing.created_at', '=', $date)
            ->whereMonth('purchasing.created_at', '=', $month)
            ->whereYear('purchasing.created_at', '=', $year)
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
            ->groupBy('month_number', 'day', 'month', 'year', 'purchasing.id');
    }

    /**
     * @inheritDoc
     */
    public function monthly(string $month, string $year)
    {

        $monthData = DB::table('purchasing')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year,)
            ->select(
                DB::raw('COUNT(*) AS total_transaction'),
                DB::raw('SUM(total_payment) AS total_expenditure')
            )->first();


        $dailyData = DB::table('purchasing')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->select(
                DB::raw('COUNT(*) AS total_transaction'),
                DB::raw('DATE(created_at) AS date'),
                DB::raw('MONTH(created_at) AS month'),
                DB::raw('MONTHNAME(created_at) AS month_name'),
                DB::raw('SUM(total_payment) AS total_expenditure')
            )
            ->groupBy(DB::raw('DATE(created_at)'), 'month', 'date', 'month_name')
            ->get();

        return [
            'total_transaction' => $monthData->total_transaction,
            'total_expenditure' => intval($monthData->total_expenditure),
            'daily_data' => $dailyData
        ];
    }

    /**
     * @inheritDoc
     */
    public function yearly(string $year)
    {
        $yearData = DB::table('purchasing')
            ->whereYear('created_at', '=', $year) // Ganti '2024' dengan tahun yang diinginkan
            ->select(
                DB::raw('COUNT(*) AS total_transaction'),
                DB::raw('SUM(total_payment) AS total_expendeture')
            )
            ->first();

        $monthlyData = DB::table('purchasing')
            ->whereYear('created_at', '=', $year) // Ganti '2024' dengan tahun yang diinginkan
            ->select(
                DB::raw('MONTH(created_at) AS month'),
                DB::raw('YEAR(created_at) AS year'),
                DB::raw('COUNT(*) AS total_transaction'),
                DB::raw('SUM(total_payment) AS total_expendeture')
            )
            ->groupBy(DB::raw('MONTH(created_at)'), 'year')
            ->get();

        return [
            'total_transaction' => $yearData->total_transaction,
            'total_expendeture' => \intval($yearData->total_expendeture),
            'monthly_purchases' => $monthlyData
        ];
    }
}
