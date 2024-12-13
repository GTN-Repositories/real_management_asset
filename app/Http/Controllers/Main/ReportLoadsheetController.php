<?php

namespace App\Http\Controllers\Main;

use App\Exports\ReportLoadsheetExport;
use App\Http\Controllers\Controller;
use App\Models\Loadsheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class ReportLoadsheetController extends Controller
{
    //
    public function index()
    {
        return view('main.report_loadsheet.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('management_project_id', function ($data) {
                return $data->management_project->name ?? '-';
            })
            ->addColumn('asset_id', function ($data) {
                return ($data->asset->name ?? '') . ' ' . ($data->asset->license_plate ?? '') ?? '-';
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? '-';
            })
            ->addColumn('location', function ($data) {
                return $data->location ?? '-';
            })
            ->addColumn('soil_type_id', function ($data) {
                return $data->soilType->name ?? '-';
            })
            ->addColumn('bpit', function ($data) {
                return $data->bpit ?? '-';
            })
            ->addColumn('kilometer', function ($data) {
                return $data->kilometer ? number_format($data->kilometer, 0, ',', '.') : '-';
            })
            ->addColumn('loadsheet', function ($data) {
                return $data->loadsheet ? number_format($data->loadsheet, 0, ',', '.') : '-';
            })
            ->addColumn('perload', function ($data) {
                return $data->perload ? number_format($data->perload, 0, ',', '.') : '-';
            })
            ->addColumn('lose_factor', function ($data) {
                return $data->lose_factor ?? '-';
            })
            ->addColumn('cubication', function ($data) {
                return $data->cubication ? number_format($data->cubication, 0, ',', '.') : '-';
            })
            ->addColumn('price', function ($data) {
                return $data->price ? number_format($data->price, 0, ',', '.') : '-';
            })
            ->addColumn('billing_status', function ($data) {
                return $data->billing_status ?? '-';
            })
            ->addColumn('remarks', function ($data) {
                return $data->remarks ?? '-';
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
            'location',
            'soil_type_id',
            'bpit',
            'kilometer',
            'loadsheet',
            'perload',
            'cubication',
            'price',
            'billing_status',
            'remarks',
            'lose_factor',
        ];

        $keyword = $request->keyword ?? "";

        $data = Loadsheet::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if (session('selected_project_id')) {
            $data->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $data->whereBetween('date', [
                $request->startDate,
                $request->endDate
            ]);
        }

        // Apply predefined filters
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $data->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $data->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $data->whereRaw('MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'bulan kemarin':
                    $data->whereRaw('MONTH(date) = MONTH(NOW()) - 1 AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'tahun ini':
                    $data->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $data->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        return $data;
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredDataQuery($request);
        $data = $query->get();

        $name = 'Loadsheet';
        $start_date = $request->startDate ?? now()->startOfMonth();
        $end_date = $request->endDate ?? now()->endOfMonth();
        $name .= '_' . $start_date . '_to_' . $end_date;

        return Excel::download(new ReportLoadsheetExport($data), $name . '.xlsx');
    }

    private function getFilteredDataQuery(Request $request)
    {
        $query = Loadsheet::orderBy('id', 'asc');

        $start_date = $request->startDate ?? now()->startOfMonth();
        $end_date = $request->endDate ?? now()->endOfMonth();

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

        $query->whereBetween('date', [$start_date, $end_date]);

        return $query;
    }
}
