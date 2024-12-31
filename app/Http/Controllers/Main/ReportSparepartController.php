<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\ManagementProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
        } else {
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
        }

        if (session('selected_project_id')) {
            $query->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
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

    public function dataProjectItem(Request $request)
    {
        $query = InspectionSchedule::selectRaw('management_project_id, GROUP_CONCAT(item_id) as item_ids, GROUP_CONCAT(asset_id) as asset_ids')
            ->groupBy('management_project_id')
            ->get()
            ->map(function ($row) {
                $itemIds = array_map(function ($value) {
                    return trim($value, '[]');
                }, explode(',', $row->item_ids));
                $assetIds = array_map(function ($value) {
                    return trim($value, '[]');
                }, explode(',', $row->asset_ids ?? ''));
                $uniqueItemIds = array_unique($itemIds);
                $itemNamesWithCount = [];
                foreach ($uniqueItemIds as $itemId) {
                    $itemName = Item::where('id', $itemId)->value('name');
                    if ($itemName !== null) {
                        $itemNamesWithCount[] = $itemName . ' (' . count(array_keys($itemIds, $itemId)) . ')';
                    }
                }
                $uniqueAssetIds = array_unique($assetIds);
                $assetNamesWithCount = [];
                foreach ($uniqueAssetIds as $assetId) {
                    $assetName = Asset::where('id', $assetId)->value('name');
                    if ($assetName !== null) {
                        $assetNamesWithCount[] = $assetName;
                    }
                }
                $projectName = DB::table('management_projects')->where('id', $row->management_project_id)->value('name');
                $row->project_name = $projectName;
                $row->item_names = implode(', ', $itemNamesWithCount);
                $row->asset_names = implode(', ', $assetNamesWithCount);
                return $row;
            });

        if (session('selected_project_id')) {
            $query = $query->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }

        return datatables()->of($query)
            ->addColumn('item_id', function ($row) {
                return $row->item_names ?: '-';
            })
            ->addColumn('management_project_id', function ($row) {
                return $row->project_name ?: '-';
            })
            ->addColumn('asset_id', function ($row) {
                return $row->asset_names ?: '-';
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getMaintenanceStatus(Request $request)
    {
        $query = InspectionSchedule::query();

        if (session('selected_project_id')) {
            $query = $query->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }

        $underMaintenanceSecondDayCount = $query->where('status', 'UnderMaintenance')
            ->whereDate('updated_at', '=', Carbon::now()->subDays(2)->toDateString())
            ->count();
        $totalItems = Item::count();
        $currentYear = Carbon::now()->year;
        $totalInspectionItemsYear = $query->whereYear('created_at', $currentYear)
            ->pluck('item_id')
            ->flatten()
            ->unique()
            ->count();
        $totalInspectionItemsWeek = $query->whereYear('created_at', $currentYear)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->pluck('item_id')
            ->flatten()
            ->unique()
            ->count();
        $percentageItemsYear = ($totalInspectionItemsYear / $totalItems) * 100;
        $percentageItemsWeek = ($totalInspectionItemsWeek / $totalItems) * 100;
        return response()->json([
            'scheduled' => $query->where('status', 'Scheduled')->count(),
            'inProgress' => $query->where('status', 'InProgress')->count(),
            'onHold' => $query->where('status', 'OnHold')->count(),
            'finish' => $query->where('status', 'Finish')->count(),
            'overdue' => $query->where('status', 'Overdue')->count(),
            'underMaintenanceSecondDay' => $underMaintenanceSecondDayCount,
            'percentageItemsYear' => $percentageItemsYear,
            'percentageItemsWeek' => $percentageItemsWeek,
        ]);
    }

    public function getAssetStatus()
    {
        $query = Asset::selectRaw('status, COUNT(*) as count');
        if (session('selected_project_id')) {
            $managementProject = ManagementProject::find(Crypt::decrypt(session('selected_project_id')));
            if ($managementProject) {
                $assetIds = $managementProject->asset_id;
                $query->whereIn('id', $assetIds);
            }
        }
        $query->groupBy('status');
        $result = $query->get();

        $underMaintenance = $result->where('status', 'UnderMaintenance')->sum('count');
        $totalAssets = $result->sum('count');

        return response()->json([
            'series' => [
                $underMaintenance,
                $totalAssets - $underMaintenance
            ]
        ]);
    }
}
