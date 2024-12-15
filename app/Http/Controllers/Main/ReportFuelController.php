<?php

namespace App\Http\Controllers\Main;

use App\Exports\MonthlyReportExport;
use App\Exports\MultiSheetExport;
use App\Exports\ReportFuelExport;
use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;
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
            ->addColumn('management_project_id', function ($data) {
                return $data->management_project->name ?? null;
            })
            ->addColumn('asset_id', function ($data) {
                return Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate ?? null;
            })
            ->addColumn('date', function ($data) {
                return \Carbon\Carbon::parse($data->date)->format('d-M-y') ?? null;
            })
            ->addColumn('day_total', function ($data) {
                return \Carbon\Carbon::parse($data->management_project->start_date)->diffInDays(\Carbon\Carbon::parse($data->management_project->end_date)) ?? null;
            })
            ->addColumn('liter', function ($data) {
                return number_format($data->liter, 0, ',', '.') ?? null;
            })
            ->addColumn('liter_trip', function ($data) {
                return number_format($data->liter / max($data->loadsheet, 1), 2) ?? null;
            })
            ->addColumn('avarage_day', function ($data) {
                return number_format($data->liter / max(\Carbon\Carbon::parse($data->management_project->start_date)->diffInDays(\Carbon\Carbon::parse($data->management_project->end_date)), 1), 2) ?? null;
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
            'liter',
            'price'
        ];

        $keyword = $request->search['value'] ?? '';

        $query = FuelConsumption::orderBy('date', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if (session('selected_project_id')) {
            $query->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        // Apply date range filter
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
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
                    $query->whereRaw('MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'bulan kemarin':
                    $query->whereRaw('MONTH(date) = MONTH(NOW()) - 1 AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        return $query->get();
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

        return Excel::download(new MultiSheetExport($data, $request), $name . '.xlsx');
    }

    public function exportExcelMonthly(Request $request)
    {
        $name = 'LoadsheetReport_' . Carbon::now()->format('M_Y');
        return Excel::download(new MonthlyReportExport($request), $name . '.xlsx');
    }

    public function getChartData(Request $request)
    {
        $query = FuelConsumption::selectRaw('DATE(date) as date, SUM(liter) as total_liters')
            ->groupBy('date')
            ->orderBy('date', 'asc');

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        } else {
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
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

        if (session('selected_project_id')) {
            $query->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
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

    public function getHoursData(Request $request)
    {
        $query = FuelConsumption::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(hours) as total_hours')
            ->groupBy('month')
            ->orderBy('month', 'asc');

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
        } else {
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
        }

        // Apply predefined filter if provided
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    break;
                case 'bulan ini':
                    $query->whereMonth('date', Carbon::now()->month)
                        ->whereYear('date', Carbon::now()->year);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('date', Carbon::now()->subMonth()->month)
                        ->whereYear('date', Carbon::now()->subMonth()->year);
                    break;
            }
        }

        if (session('selected_project_id')) {
            $query->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $hoursData = $query->get();

        return response()->json([
            'months' => $hoursData->pluck('month')->toArray(),
            'hours' => $hoursData->pluck('total_hours')->toArray(),
        ]);
    }

    public function getChartExpanseFuel(Request $request)
    {
        $query = FuelConsumption::query();

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
        } else {
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
        }

        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $query->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
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

        $fuelConsumptions = $query->get();

        $avgPerDay = $fuelConsumptions->avg('liter') / max($fuelConsumptions->count(), 1);
        $avgPerTrip = $fuelConsumptions->avg('liter');
        $avgPerLiter = $fuelConsumptions->avg('price');
        $totalFuelCost = $fuelConsumptions->sum('price');

        $chartData = $fuelConsumptions->groupBy('date');

        return response()->json([
            'avgPerDay' => $avgPerDay,
            'avgPerTrip' => $avgPerTrip,
            'avgPerLiter' => $avgPerLiter,
            'totalFuelCost' => $totalFuelCost,
            'dates' => $chartData->keys(),
            'litersData' => $chartData->map(fn($group) => $group->sum('liter'))->values(),
            'priceData' => $chartData->map(fn($group) => $group->sum('price'))->values()
        ]);
    }

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
            }
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = Carbon::parse($request->startDate);
            $endDate = Carbon::parse($request->endDate);

            if ($startDate->isSameDay($endDate)) {
                return $query->whereBetween('date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            }

            return $query->whereBetween('date', [
                $startDate->startOfDay(),
                $endDate->endOfDay()
            ]);
        }

        return $query->whereBetween('date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }
}
