<?php


namespace App\Services;

use App\Exceptions\ApiException;
use App\Repositories\PurchaseReportRepository;
use App\Services\PurchaseReportService;
use Carbon\Carbon;

class PurchaseReportServiceImpl implements PurchaseReportService
{

    public function __construct(public PurchaseReportRepository $purchaseReportRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function getDailyPurchases(string $date)
    {

        try {
            $date = Carbon::createFromFormat("Y-m-d", $date);

            $dailyPurchases = $this->purchaseReportRepository->daily($date->day, $date->month, $date->year)->get();

            $total_expenditure = 0;
            foreach ($dailyPurchases as $data) {
                $total_expenditure += intval($data->total_expenditure);
            }

            $data = [
                "link" => \url()->current(),
                "total_transaction" => $dailyPurchases->count(),
                "total_expenditure" => $total_expenditure,
                "items_purchasing" => []
            ];

            foreach ($dailyPurchases as $purchas_item) {
                $data['items_purchasing'][] = [
                    "time" => Carbon::createFromFormat("Y-m-d H:m:s", $purchas_item->created_at)->format('H:m:s'),
                    "year" => $purchas_item->year,
                    "month" => $purchas_item->month,
                    "day" => $purchas_item->day,
                    "purchases" => intval($purchas_item->total_payment),
                    "no_transaction" => $purchas_item->no_purchasing
                ];
            }

            return $data;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function getMonthlyPurchases(string $date)
    {
        try {
            $date = explode("-", $date);
            $date = "$date[0]-$date[1]";
            $date = Carbon::createFromFormat("Y-m", $date);

            $monthlyPurchases = $this->purchaseReportRepository->monthly($date->month, $date->year);
            $monthlyPurchases["link"] = url()->current();

            foreach ($monthlyPurchases['daily_data'] as $purchase) {
                $purchase->link = url()->previous("/api/purchases/daily/{$purchase->date}");
            }

            return $monthlyPurchases;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getYearlyPurchases(string $date)
    {
        try {
            $date = explode("-", $date);
            $date = "$date[0]";
            $year = Carbon::createFromFormat("Y", $date);

            $purchaseYearly  = $this->purchaseReportRepository->yearly($year);

            if ($purchaseYearly['monthly_purchases']->count() === 0) {
                throw new ApiException('data pembelian tidak ditemukan', 404);
            }

            foreach ($purchaseYearly['monthly_purchases'] as $data) {
                $data->link = url("/api/purchases/monthly/{$data->year}-{$data->month}");
            }

            return $purchaseYearly;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
}
