<?php


namespace App\Repositories;

interface PurchaseReportRepository
{
    public function daily(string $date, string $month, string $year);
    public function monthly(string $month, string $year);
    public function yearly(string $year);
}
