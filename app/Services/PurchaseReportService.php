<?php

namespace App\Services;

interface PurchaseReportService
{
    public function getDailyPurchases(string $date);
    public function getMonthlyPurchases(string $date);
    public function getYearlyPurchases(string $date);
}
