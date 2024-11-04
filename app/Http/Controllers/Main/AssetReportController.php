<?php

namespace App\Http\Controllers\Main;

use App\Exports\ReportAssetExport;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ManagementProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class AssetReportController extends Controller
{
    //
    public function index()
    {
        return view('main.report_asset.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('image', function ($data) {
                return $data->image ? '<img src="' . asset('storage/' . $data->image) . '" alt="Image" width="50" height="50"/>' : "kosong";
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? "kosong";
            })
            ->addColumn('serial_number', function ($data) {
                return $data->serial_number ?? "kosong";
            })
            ->addColumn('model_number', function ($data) {
                return $data->model_number ?? "kosong";
            })
            ->addColumn('manager', function ($data) {
                return $data->manager ?? "kosong";
            })
            ->addColumn('category', function ($data) {
                return $data->category ?? "kosong";
            })
            ->addColumn('assets_location', function ($data) {
                return $data->assets_location ?? "kosong";
            })
            ->addColumn('cost', function ($data) {
                return $data->cost ?? "kosong";
            })
            ->addColumn('purchase_date', function ($data) {
                return $data->purchase_date ?? "kosong";
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'image',
            'name',
            'serial_number',
            'model_number',
            'manager',
            'assets_location',
            'category',
            'cost',
            'purchase_date',
        ];

        $query = Asset::orderBy('created_at', 'asc')
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
            $query->whereBetween('purchase_date', [$request->startDate, $request->endDate]);
        }

        // Apply predefined filters
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $query->whereDate('purchase_date', Carbon::today());
                    break;
                case 'minggu ini':
                    $query->whereBetween('purchase_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $query->whereMonth('purchase_date', Carbon::now()->month);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('purchase_date', Carbon::now()->subMonth()->month);
                    break;
                case 'tahun ini':
                    $query->whereYear('purchase_date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('purchase_date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredDataQuery($request);
        $data = $query->get();

        $name = 'AssetReport';
        $name .= '_' . $request->startDate . '_to_' . $request->endDate;

        return Excel::download(new ReportAssetExport($data), $name . '.xlsx');
    }


    /**
     * Returns a query with the applied filters for consistency across exports.
     */
    private function getFilteredDataQuery(Request $request)
    {
        $query = Asset::orderBy('purchase_date', 'asc');

        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    return $query->whereDate('purchase_date', Carbon::today());
                case 'minggu ini':
                    return $query->whereBetween('purchase_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                case 'bulan ini':
                    return $query->whereBetween('purchase_date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                case 'bulan kemarin':
                    return $query->whereBetween('purchase_date', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ]);
                case 'tahun ini':
                    return $query->whereBetween('purchase_date', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear()
                    ]);
                case 'tahun kemarin':
                    return $query->whereBetween('purchase_date', [
                        Carbon::now()->subYear()->startOfYear(),
                        Carbon::now()->subYear()->endOfYear()
                    ]);
                default:
                    return $query;
            }
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('purchase_date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
            return $query;
        }

        return $query;
    }
}
