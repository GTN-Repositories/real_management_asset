<?php

namespace App\Http\Controllers\Main;

use App\Exports\MultiSheetExport;
use App\Exports\ReportFuelExport;
use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;

class ReportFuelController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission::report read', ['only' => ['index', 'data', 'getData', 'getChartData', 'exportPdf', 'exportExcel']]),
        ];
    }

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
            ->addColumn('loadsheet', function ($data) {
                return $data->loadsheet ?? null;
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
            'date',
            'loadsheet',
            'liter',
            'price'
        ];

        $query = FuelConsumption::orderBy('created_at', 'asc')
            ->select($columns);

        if ($request->filled('search.value')) {
            $query->where(function ($q) use ($request, $columns) {
                $keyword = $request->search['value'];
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', "%$keyword%");
                }
            });
        }

        // Apply date range filter
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        // Apply predefined filters
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $query->whereMonth('date', Carbon::now()->month);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('date', Carbon::now()->subMonth()->month);
                    break;
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        return $query;
    }


    public function getChartData(Request $request)
    {
        $query = FuelConsumption::selectRaw('DATE(date) as date, SUM(liter) as total_liters')
            ->groupBy('date')
            ->orderBy('date', 'asc');

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        // Apply predefined filter if provided
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $query->whereMonth('date', Carbon::now()->month);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('date', Carbon::now()->subMonth()->month);
                    break;
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        $consumptions = $query->get();

        // Prepare data for chart
        $dates = $consumptions->pluck('date')->toArray();
        $liters = $consumptions->pluck('total_liters')->toArray();

        return response()->json([
            'dates' => $dates,
            'liters' => $liters
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = $this->getFilteredDataQuery($request);
        $data = $query->get();

        $chartImage = $request->input('chartImage');
        $pdf = Pdf::loadView('main.report_fuel.pdf', compact('data', 'chartImage'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('FuelConsumptionReport.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredDataQuery($request);
        $data = $query->get();

        $name = 'FuelConsumptionReport';
        $name .= '_' . $request->startDate . '_to_' . $request->endDate;

        return Excel::download(new MultiSheetExport($data), $name . '.xlsx');
    }


    /**
     * Returns a query with the applied filters for consistency across exports.
     */
    private function getFilteredDataQuery(Request $request)
    {
        $query = FuelConsumption::orderBy('date', 'asc');

        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    return $query->whereDate('date', Carbon::today());
                case 'minggu ini':
                    return $query->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                case 'bulan ini':
                    return $query->whereBetween('date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                case 'bulan kemarin':
                    return $query->whereBetween('date', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ]);
                case 'tahun ini':
                    return $query->whereBetween('date', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear()
                    ]);
                case 'tahun kemarin':
                    return $query->whereBetween('date', [
                        Carbon::now()->subYear()->startOfYear(),
                        Carbon::now()->subYear()->endOfYear()
                    ]);
                default:
                    return $query;
            }
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
            return $query;
        }

        return $query;
    }
}
