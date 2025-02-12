<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\MaintenanceSparepart;
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
                $stock_warehouse = new WerehouseController();
                $stock = $stock_warehouse->getStock(Crypt::decrypt($data->id));

                return $stock ?? null;
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
        $query = Maintenance::query();

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
            // $query->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
            $query->whereHas('inspectionSchedule', function ($q) {
                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $maintenance = $query->get();

        $result = [];

        foreach ($maintenance as $schedule) {
            $maintenanceSparepart = MaintenanceSparepart::where('maintenance_id', Crypt::decrypt($schedule->id))->get();

            foreach ($maintenanceSparepart as $sparepart) {
                $category = DB::table('items')
                        ->join('category_items', 'items.category_id', '=', 'category_items.id')
                        ->where('items.id', $sparepart->item_id)
                        ->value('category_items.name');

                if (in_array($category, ['Filters', 'Oil', 'Tires'])) {
                    $month = Carbon::parse($schedule->date)->format('Y-m');
    
                    $result[$month][$category] = ($result[$month][$category] ?? 0) + ($$sparepart->quantity ?? 0);
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
        $inspeksi = InspectionSchedule::query();
        if (session('selected_project_id')) {
            $inspeksi = $inspeksi->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
            $inspeksi->whereHas('inspection_schedule', function ($q) {
                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $groupBy = $inspeksi->get()->groupBy('management_project_id');

        $inspeksi_ids = [];
        foreach ($groupBy as $key => $value) {
            foreach ($value->pluck('id') as $ckey => $value) {
                $inspeksi_ids[$key][] = Crypt::decrypt($value);
            }
        }
        $asset_ids = [];
        foreach ($groupBy as $key => $value) {
            $asset_ids[$key][] = $value->pluck('asset_id')->toArray();
        }

        $data = [];
        foreach ($inspeksi_ids as $key => $value) {
            $data[$key]['project_name'] = ManagementProject::find($key)->name ?? '-';
            $maintenance_ids = Maintenance::whereIn('inspection_schedule_id', $value)->pluck('id');
            $data[$key]['item'] = '';
            $item_elem = '<ul>';
            foreach ($maintenance_ids as $ckey => $value) {
                $maintenanceSparepart = MaintenanceSparepart::where('maintenance_id', Crypt::decrypt($value))->get()->groupBy('item_id');
                foreach ($maintenanceSparepart as $item_id => $value) {
                    $item_name = Item::find($item_id)->name ?? null;
                    $quantity = $value->sum('quantity');
                    $item_elem .= '<li>' . $item_name . ' : ' . $quantity . '</li>';
                }
            }
            $item_elem .= '</ul>';
            $data[$key]['item'] = $item_elem;
            $asset_elem = '<ul>';

            if (isset($asset_ids[$key])) {
                foreach ($asset_ids[$key] as $ckey => $cvalue) {
                    foreach ($cvalue as $keyasset => $asset_id) {
                        $asset_name = Asset::find($asset_id)->name ?? null;
                        $asset_elem .= '<li> AST - ' . $asset_id . ' ' . $asset_name . '</li>';
                    }
                }
            }else{
                $data[$key]['asset_id'] = [];
            }
            $asset_elem .= '</ul>';
            $data[$key]['asset_id'] = $asset_elem;
        }

        return datatables()->of($data)
            ->addColumn('asset_id', function ($row) {
                return $row['asset_id'] ?? null;
            })
            ->addColumn('item_id', function ($row) {
                return $row['item'] ?? null;
            })
            ->addColumn('management_project_id', function ($row) {
                return $row['project_name'] ?? null;
            })
            ->addIndexColumn()
            ->rawColumns(['asset_id', 'item_id', 'action'])
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
        $totalInspectionItemsYear = MaintenanceSparepart::whereYear('created_at', $currentYear)
            ->when(session('selected_project_id'), function ($querys) {
                $querys->whereHas('maintenance.inspectionSchedule', function ($q) {
                    $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                });
            })
            ->pluck('item_id')
            ->flatten()
            ->unique()
            ->count();
        $totalInspectionItemsWeek = MaintenanceSparepart::whereYear('created_at', $currentYear)
            ->when(session('selected_project_id'), function ($querys) {
                $querys->whereHas('maintenance.inspectionSchedule', function ($q) {
                    $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                });
            })
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
            'scheduled' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Scheduled')->count(),
            'inProgress' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'InProgress')->count(),
            'onHold' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'OnHold')->count(),
            'finish' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Finish')->count(),
            'overdue' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Overdue')->count(),
            
            'active' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Active')->count() ?? 0,
            'inactive' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Inactive')->count() ?? 0,
            'underMaintenance' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'UnderMaintenance')->count() ?? 0,
            'underRepair' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'UnderRepair')->count() ?? 0,
            'waiting' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Waiting')->count() ?? 0,
            'scrap' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'Scrap')->count() ?? 0,
            'rfu' => InspectionSchedule::when(session('selected_project_id'), function ($q) {
                                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                            })
                            ->where('status', 'RFU')->count() ?? 0,

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
