<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\FuelConsumption;
use App\Models\ManagementProject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class ReportFuelExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('main.report_fuel.excel', [
            'fuelConsumptions' => $this->data
        ]);
    }
}
