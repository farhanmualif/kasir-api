<?php


namespace App\Services;

interface SalesReportService
{
    public function getDailySales(string $date);
    public function getMonthlySales(string $date);
    public function getYearlySales(string $date);
}
