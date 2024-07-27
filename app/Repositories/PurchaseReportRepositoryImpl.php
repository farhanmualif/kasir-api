<?php

namespace App\Repositories;

use App\Repositories\PurchaseReportRepository;
use Illuminate\Support\Facades\Auth;
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
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->join('stores', 'product_store.store_id', '=', 'stores.id')
            ->select(
                'purchasing.*',
                'products.*',
                DB::raw('MONTHNAME(purchasing.created_at) AS month'),
                DB::raw('YEAR(purchasing.created_at) AS year'),
                DB::raw('MONTH(purchasing.created_at) AS month_number'),
                DB::raw('DAY(purchasing.created_at) AS day'),
                DB::raw('SUM(purchasing.total_payment) as total_expenditure')
            )
            ->where('stores.id', Auth::user()->stores->first()->id)
            ->groupBy('month_number', 'day', 'month', 'year', 'purchasing.id');
    }

    /**
     * @inheritDoc
     */
    public function monthly(string $month, string $year)
    {

        $storeId = Auth::user()->stores->first()->id;

        $monthData = DB::table('purchasing')
            ->join('products', 'products.id', '=', 'purchasing.product_id')
            ->join('product_category', 'product_category.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'product_category.category_id')
            ->whereMonth('purchasing.created_at', '=', $month)
            ->whereYear('purchasing.created_at', '=', $year)
            ->where('categories.store_id', $storeId)
            ->select(
                DB::raw('COUNT(DISTINCT purchasing.id) AS total_transaction'),
                DB::raw('SUM(purchasing.quantity * products.selling_price) AS total_revenue')
            )->first();

        $dailyData = DB::table('purchasing')
            ->join('products', 'products.id', '=', 'purchasing.product_id')
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->whereMonth('purchasing.created_at', '=', $month)
            ->whereYear('purchasing.created_at', '=', $year)
            ->where('stores.id', $storeId)
            ->select(
                DB::raw('COUNT(DISTINCT purchasing.id) AS total_transaction'),
                DB::raw('DATE(purchasing.created_at) AS date'),
                DB::raw('MONTH(purchasing.created_at) AS month'),
                DB::raw('MONTHNAME(purchasing.created_at) AS month_name'),
                DB::raw('SUM(purchasing.quantity * products.selling_price) AS total_revenue')
            )
            ->groupBy(DB::raw('DATE(purchasing.created_at)'), 'month', 'month_name')
            ->orderBy('date')
            ->get();

        return [
            'total_transaction' => $monthData->total_transaction,
            'total_revenue' => intval($monthData->total_revenue),
            'daily_data' => $dailyData
        ];
    }

    /**
     * @inheritDoc
     */
    public function yearly(string $year)
    {
        $storeId = Auth::user()->stores->first()->id;
        $yearData = DB::table('purchasing')
            ->join('products', 'products.id', '=', 'purchasing.product_id')
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->where('stores.id', $storeId)
            ->whereYear('purchasing.created_at', '=', $year) // Ganti '2024' dengan tahun yang diinginkan
            ->select(
                DB::raw('COUNT(*) AS total_transaction'),
                DB::raw('SUM(total_payment) AS total_expendeture')
            )
            ->first();

        $monthlyData = DB::table('purchasing')
            ->join('products', 'products.id', '=', 'purchasing.product_id')
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->where('stores.id', $storeId)
            ->whereYear('purchasing.created_at', '=', $year) // Ganti '2024' dengan tahun yang diinginkan
            ->select(
                DB::raw('MONTH(purchasing.created_at) AS month'),
                DB::raw('YEAR(purchasing.created_at) AS year'),
                DB::raw('COUNT(*) AS total_transaction'),
                DB::raw('SUM(total_payment) AS total_expendeture')
            )
            ->groupBy(DB::raw('MONTH(purchasing.created_at)'), 'year')
            ->get();

        return [
            'total_transaction' => $yearData->total_transaction,
            'total_expendeture' => \intval($yearData->total_expendeture),
            'monthly_purchases' => $monthlyData
        ];
    }
}
