<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new ReportFuelExport($this->data), // First sheet
            new MonthlyReportExport()           // Second sheet with current month's data
        ];
    }
}
