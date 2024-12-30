<?php

namespace App\Http\Controllers\Main;

use App\Exports\MonthlyReportExport;
use App\Exports\MultiSheetExport;
use App\Exports\ReportFuelExport;
use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use App\Models\Ipb;
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
                return $data->asset ? 'AST-' . Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate : null;
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

        // $chartImage = $request->input('chartImage');
        $pdf = Pdf::loadView('main.report_fuel.pdf', compact('data'))
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

    public function getChartExpanseFuel(Request $request)
    {
        $query = FuelConsumption::query();
        $Ipbquery = Ipb::query();

        if (session('selected_project_id')) {
            $query->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });

            $Ipbquery->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        if (!$request->filled('predefinedFilter') && !$request->filled('startDate') && !$request->filled('endDate')) {
            // Default to current month
            $query->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year);

            $Ipbquery->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year);
        }

        // Adjust query if predefined filter is provided
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $query->whereDate('date', Carbon::today());
                    $Ipbquery->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $query->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    $Ipbquery->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'bulan ini':
                    $query->whereMonth('date', Carbon::now()->month);
                    $Ipbquery->whereMonth('date', Carbon::now()->month);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('date', Carbon::now()->subMonth()->month);
                    $Ipbquery->whereMonth('date', Carbon::now()->subMonth()->month);
                    break;
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    $Ipbquery->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    $Ipbquery->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        // Adjust query if start and end dates are provided
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
            $Ipbquery->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
        }

        $fuelConsumptions = $query->get();
        $ipbData = $Ipbquery->get();

        $avgPerDay = $fuelConsumptions->avg('liter') / max($fuelConsumptions->count(), 1);
        $avgPerTrip = $fuelConsumptions->avg('liter');
        $avgPerLiter = $ipbData->avg('unit_price');
        $totalFuelCost = $ipbData->sum(fn($ipb) => $ipb->usage_liter * $ipb->unit_price);

        $chartData = $fuelConsumptions->groupBy('date');

        // dd($priceData);
        return response()->json([
            'avgPerDay' => $avgPerDay,
            'avgPerTrip' => $avgPerTrip,
            'avgPerLiter' => $avgPerLiter,
            'totalFuelCost' => $totalFuelCost,
            'dates' => $chartData->keys(),
            'litersData' => $chartData->map(fn($group) => $group->sum('liter'))->values(),
            'priceData' => $ipbData->map(fn($ipb) => $ipb->usage_liter * $ipb->unit_price)->values(),
            'priceDate' => $ipbData->map(fn($ipb) => $ipb->date)->values(),
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

            if ($startDate->isSameDay($endDate) && $startDate->isSameMonth(Carbon::now())) {
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

    public function getDataProjectFuel(Request $request)
    {
        $query = FuelConsumption::join('management_projects', 'fuel_consumptions.management_project_id', '=', 'management_projects.id')
            ->selectRaw('management_projects.id, management_projects.name, SUM(fuel_consumptions.liter) as total_liter')
            ->groupBy('management_projects.id', 'management_projects.name');

        if (session('selected_project_id')) {
            $query->where('management_projects.id', Crypt::decrypt(session('selected_project_id')));
        }

        $data = $query->get();

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('total_liter', function ($row) {
                return $row->total_liter;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDataAssetFuel()
    {
        $query = FuelConsumption::join('assets', 'fuel_consumptions.asset_id', '=', 'assets.id')
            ->selectRaw('assets.id, assets.name, assets.license_plate, SUM(fuel_consumptions.liter) as total_liter')
            ->groupBy('assets.id', 'assets.name', 'assets.license_plate');

        if (session('selected_project_id')) {
            $query->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $data = $query->get();

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return 'AST-' . Crypt::decrypt($row->id) . ' - ' . $row->name . ' - ' . $row->license_plate;
            })
            ->addColumn('total_liter', function ($row) {
                return $row->total_liter;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }
}
