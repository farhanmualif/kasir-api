<?php

namespace App\Repositories;

interface SalesReportRepository
{
    public function daily(string $date);
    public function monthly(string $month, string $year);
    public function yearly(string $year);
}
