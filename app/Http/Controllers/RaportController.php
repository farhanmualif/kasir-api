<?php

namespace App\Http\Controllers;

use App\Services\PurchaseReportService;
use App\Services\SalesReportService;


class RaportController extends Controller
{

    public function __construct(public SalesReportService $salesReportService, public PurchaseReportService $purchaseReportService)
    {
    }
    public function getDailySales($date)
    {
        return \responseJson("berhasil mendapatkan data penjualan", $this->salesReportService->getDailySales($date));
    }

    public function getMonthlySales($date)
    {
        return \responseJson("berhasil mengambil data penjualan perbulan", $this->salesReportService->getMonthlySales($date));
    }


    public function getYearlySales($date)
    {
        $annualSalesData = $this->salesReportService->getYearlySales($date);
        return \responseJson("berhasil mengambil data penjualan pertahun", $annualSalesData);
    }

    public function getDailyPurchases($date)
    {
        return \responseJson("berhasil mendapatkan data pembelian", $this->purchaseReportService->getDailyPurchases($date));
    }

    public function getMonthlyPurchases($date)
    {
        return \responseJson("berhasil mendapatkan data pembelian", $this->purchaseReportService->getMonthlyPurchases($date));
    }

    public function getYearlyPurchases($date)
    {
        return \responseJson("berhasil mendapatkan data pembelian", $this->purchaseReportService->getYearlyPurchases($date));
    }
}
