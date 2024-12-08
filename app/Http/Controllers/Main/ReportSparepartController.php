<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportSparepartController extends Controller
{
    //
    public function index()
    {
        return view('main.report_sparepart.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('code', function ($data) {
                return $data->code ?? '-';
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? '-';
            })
            ->addColumn('stock', function ($data) {
                return $data->stock ?? '-';
            })
            ->addColumn('price', function ($data) {
                return 'Rp.' . number_format($data->price, 0, ',', '.') ?? '-';
            })
            ->addColumn('size', function ($data) {
                return $data->size ?? '-';
            })
            ->addColumn('brand', function ($data) {
                return $data->brand ?? '-';
            })
            ->addColumn('oum_id', function ($data) {
                return $data->oum->name ?? '-';
            })
            ->addColumn('category_id', function ($data) {
                return $data->category->name ?? '-';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'code',
            'name',
            'stock',
            'price',
            'size',
            'brand',
            'oum_id',
            'category_id',
        ];

        $keyword = $request->keyword ?? "";

        $data = Item::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $data->whereBetween('created_at', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
        }

        // Apply predefined filters
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $data->whereDate('created_at', Carbon::today());
                    break;
                case 'minggu ini':
                    $data->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $data->whereRaw('MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())');
                    break;
                case 'bulan kemarin':
                    $data->whereRaw('MONTH(created_at) = MONTH(NOW()) - 1 AND YEAR(created_at) = YEAR(NOW())');
                    break;
                case 'tahun ini':
                    $data->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $data->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }

        return $data;
    }

    public function getInspectionData(Request $request)
    {
        $query = DB::table('inspection_schedules');

        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $query->whereMonth('date', Carbon::now()->month)
                          ->whereYear('date', Carbon::now()->year);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('date', Carbon::now()->subMonth()->month)
                          ->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $inspectionSchedules = $query->get();

        $result = [];

        foreach ($inspectionSchedules as $schedule) {
            $itemIds = json_decode($schedule->item_id, true);
            $itemStock = json_decode($schedule->item_stock, true);

            if (is_array($itemIds) && is_array($itemStock)) {
                foreach ($itemIds as $itemId) {
                    $category = DB::table('items')
                        ->join('category_items', 'items.category_id', '=', 'category_items.id')
                        ->where('items.id', $itemId)
                        ->value('category_items.name');

                    if (in_array($category, ['Filters', 'Oil', 'Tires'])) {
                        $month = Carbon::parse($schedule->date)->format('Y-m');

                        $result[$month][$category] = ($result[$month][$category] ?? 0) + ($itemStock[$itemId] ?? 0);
                    }
                }
            }
        }

        $formattedData = [
            'months' => [],
            'series' => [
                ['name' => 'Filters', 'data' => []],
                ['name' => 'Oil', 'data' => []],
                ['name' => 'Tires', 'data' => []],
            ],
        ];

        ksort($result);

        foreach ($result as $month => $categories) {
            $formattedData['months'][] = $month;
            $formattedData['series'][0]['data'][] = $categories['Filters'] ?? 0;
            $formattedData['series'][1]['data'][] = $categories['Oil'] ?? 0;
            $formattedData['series'][2]['data'][] = $categories['Tires'] ?? 0;
        }

        return response()->json($formattedData);
    }
}
