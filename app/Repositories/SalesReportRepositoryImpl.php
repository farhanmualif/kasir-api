<?php


namespace App\Repositories;

use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReportRepositoryImpl implements SalesReportRepository
{

    public function __construct(
        public AuthManager $auth,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function daily(string $date)
    {
        $storeId = $this->auth->user()->stores()->first()->id;
        // dd($date);
        return DB::table('transactions as t')
            ->join('detail_transactions as dt', 't.id', '=', 'dt.id_transaction')
            ->join('products', 'dt.id_product', '=', 'products.id')
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->join('stores', 'product_store.store_id', '=', 'stores.id')
            ->select(
                't.id as transaction_id',
                't.no_transaction',
                't.created_at',
                DB::raw('TIME(t.created_at) as time'),
                DB::raw('SUM(dt.total_price) as revenue'),
                DB::raw('SUM(dt.quantity * (dt.item_price - products.purchase_price)) as profit'),
                'products.id as product_id',
                'stores.id as store_id',
                'products.name as product_name',
                'dt.quantity',
                'dt.item_price as price',
                'dt.total_price',
                'stores.name as store_name',
                DB::raw('COUNT(DISTINCT t.id) as transaction_count')
            )
            ->whereDate('t.created_at', $date)
            ->where('stores.id', $storeId)
            ->groupBy(
                't.id',
                't.no_transaction',
                't.created_at',
                'stores.id',
                'stores.name',
                'products.id',
                'products.name',
                'dt.quantity',
                'dt.item_price',
                'dt.total_price'
            )
            ->orderBy('t.created_at', 'DESC');
    }

    /**
     * @inheritDoc
     */
    public function monthly(string $month, string $year)
    {
        $storeId = $this->auth->user()->stores()->first()->id;

        return DB::table('transactions')
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
            ->join('products', 'products.id', '=', 'detail_transactions.id_product')
            ->join('product_store', 'products.id', '=', 'product_store.product_id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->whereMonth('transactions.created_at', $month)
            ->whereYear('transactions.created_at', $year)
            ->where('stores.id', $storeId)
            ->select(
                DB::raw('DAY(transactions.created_at) as day'),
                DB::raw('COUNT(DISTINCT detail_transactions.id_transaction) AS transaction_amount'),
                DB::raw('SUM(detail_transactions.quantity * products.selling_price) AS income'),
                DB::raw('SUM(detail_transactions.quantity * (products.selling_price - products.purchase_price)) AS profit')
            )
            ->groupBy(DB::raw('DAY(transactions.created_at)'))
            ->orderBy(DB::raw('DAY(transactions.created_at)'));
    }

    /**
     * @inheritDoc
     */
    public function yearly(string $year)
    {
        $storeId = $this->auth->user()->stores()->first()->id;
        return DB::table('transactions')
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
            ->join('products', 'products.id', '=', 'detail_transactions.id_product')
            ->join('product_store', 'products.id', '=', 'product_store.product_id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->whereYear('transactions.created_at', $year)
            ->where('stores.id', $storeId)
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
