<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportFuelController extends Controller
{

    public function index()
    {
        return view('main.report_fuel.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                return 1;
            })
            ->addColumn('relationId', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->addColumn('management_project_id', function ($data) {
                return $data->management_project->name ?? null;
            })
            ->addColumn('asset_id', function ($data) {
                return $data->asset->name ?? null;
            })
            ->addColumn('liter', function ($data) {
                return $data->liter ?? null;
            })
            ->addColumn('price', function ($data) {
                return $data->price ?? null;
            })
            ->addColumn('total', function ($data) {
                return $data->liter * $data->price ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }



    public function getData(Request $request)
    {
        $columns = [
            'id',
            'management_project_id',
            'asset_id',
            'receiver',
            'date',
            'liter',
            'price',
        ];

        $keyword = $request->search['value'] ?? "";

        $data = FuelConsumption::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        return $data;
    }

    public function getChartData()
    {
        $consumptions = FuelConsumption::selectRaw('DATE(date) as date, SUM(liter) as total_liters')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dates = $consumptions->pluck('date')->toArray();
        $liters = $consumptions->pluck('total_liters')->toArray();

        return response()->json([
            'dates' => $dates,
            'liters' => $liters
        ]);
    }

    public function exportPdf(Request $request)
    {
        $chartImage = $request->input('chartImage'); // This is the base64 image from the chart

        // Fetch table data
        $data = $this->getData($request)->get();

        $pdf = Pdf::loadView('main.report_fuel.pdf', compact('data', 'chartImage'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('FuelConsumptionReport.pdf');
    }
}
