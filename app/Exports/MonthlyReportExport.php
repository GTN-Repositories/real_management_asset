<?php

namespace App\Exports;

use App\Models\FuelConsumption;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MonthlyReportExport implements FromView
{
    public function view(): View
    {
        $currentMonthData = FuelConsumption::whereBetween('date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->get();

        return view('main.report_fuel.monthly_report', [
            'fuelConsumptions' => $currentMonthData
        ]);
    }
}
