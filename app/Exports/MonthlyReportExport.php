<?php

namespace App\Exports;

use App\Models\FuelConsumption;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class MonthlyReportExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $currentMonthData = FuelConsumption::whereBetween('date', [
            Carbon::createFromDate($this->request->year, $this->request->month, 1)->startOfMonth(),
            Carbon::createFromDate($this->request->year, $this->request->month, 1)->endOfMonth(),
        ])->get()->groupBy(function ($item) {
            return $item->asset->name . '-' . ($item->user->name ?? 'N/A');
        });

        // Aggregate data by summing fuel consumption for the same day
        $fuelConsumptions = $currentMonthData->map(function ($entries) {
            $dailyConsumption = [];
            foreach ($entries as $entry) {
                $day = Carbon::parse($entry->date)->day;
                if (!isset($dailyConsumption[$day])) {
                    $dailyConsumption[$day] = 0;
                }
                $dailyConsumption[$day] += $entry->liter;
            }
            return [
                'asset_name' => $entries->first()->asset->name,
                'driver_name' => $entries->first()->user->name ?? 'N/A',
                'daily_consumption' => $dailyConsumption,
            ];
        });

        return view('main.report_fuel.monthly_report', [
            'fuelConsumptions' => $fuelConsumptions
        ]);
    }
}

