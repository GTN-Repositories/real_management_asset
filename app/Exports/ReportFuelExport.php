<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportFuelExport implements FromView
{
    protected $data;
    protected $request;

    public function __construct($data, $request)
    {
        $this->data = $data;
        $this->request = $request;
    }

    public function view(): View
    {
        return view('main.report_fuel.excel', [
            'fuelConsumptions' => $this->data,
            'startDate' => $this->request->startDate,
            'endDate' => $this->request->endDate,
        ]);
    }
}
