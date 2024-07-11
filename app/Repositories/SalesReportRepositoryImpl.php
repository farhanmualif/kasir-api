<?php


namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SalesReportRepositoryImpl implements SalesReportRepository
{

    /**
     * @inheritDoc
     */
    public function daily(string $date)
    {
        return DB::table('transactions')
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
            ->join('products', 'products.id', '=', 'detail_transactions.id_product')
            ->where('transactions.created_at', '>=', "$date 00:00:00")
            ->where('transactions.created_at', '<=', "$date 23:59:59")
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
            );
    }

    /**
     * @inheritDoc
     */
    public function monthly(string $date, string $month)
    {
        return DB::table('transactions')
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
            ->join('products', 'products.id', '=', 'detail_transactions.id_product')
            ->whereMonth('transactions.created_at', $date)
            ->whereYear('transactions.created_at', $month)
            ->select(
                DB::raw('MONTHNAME(transactions.created_at) AS month'),
                DB::raw('YEAR(transactions.created_at) AS year'),
                DB::raw('COUNT(transactions.id) AS transaction_amount'),
                DB::raw('MONTH(transactions.created_at) AS month_number'),
                DB::raw('DAY(transactions.created_at) AS day'),
                DB::raw('COUNT(*) as total_transaction'),
                DB::raw('SUM(detail_transactions.quantity * products.selling_price) AS income'),
                DB::raw('SUM(detail_transactions.quantity * (products.selling_price - products.purchase_price)) AS profit')
            )
            ->groupBy('month_number', 'day', 'month', 'year');
    }

    /**
     * @inheritDoc
     */
    public function yearly(string $year)
    {
        return DB::table('transactions')
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
            ->join('products', 'products.id', '=', 'detail_transactions.id_product')
            ->whereYear('transactions.created_at', $year)
            ->select(
                DB::raw('MONTHNAME(transactions.created_at) AS month'),
                DB::raw('YEAR(transactions.created_at) AS year'),
                DB::raw('MONTH(transactions.created_at) AS month_number'),
                DB::raw('COUNT(*) as total_transaction'),
                DB::raw('SUM(detail_transactions.quantity * products.selling_price) AS income'),
                DB::raw('SUM(detail_transactions.quantity * (products.selling_price - products.purchase_price)) AS profit')
            )
            ->groupBy('month_number', 'month', 'year');
    }
}
