<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AssetReminder;
use App\Models\InspectionSchedule;
use App\Models\Ipb;
use App\Models\Item;
use App\Models\LoadhseetTarget;
use App\Models\Loadsheet;
use App\Models\ManagementProject;
use App\Models\RecordInsurance;
use App\Models\RecordRent;
use App\Models\RecordTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AssetPerformance extends Controller
{
    //
    public function index()
    {
        return view('main.asset_performance.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);
        $latestTarget = LoadhseetTarget::latest()->first();

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('asset', function ($data) {
                return $data->asset_id . '-' . $data->asset->name . '-' . $data->asset->serial_number ?? null;
            })
            ->addColumn('PerformanceRate', function ($data) use ($latestTarget) {
                $percentage = (int) (($data->total_loadsheet / $latestTarget->target) * 100);
                $color = 'success';
                if ($percentage < 50) {
                    $color = 'danger';
                } elseif ($percentage >= 50 && $percentage < 80) {
                    $color = 'warning';
                }

                return '<div class="progress">
                    <div class="progress-bar bg-' . $color . '" role="progressbar" style="width: ' . $percentage . '%;" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">' . $percentage . '%</div>
                    </div>';
            })

            ->addColumn('Expenses', function ($data) {
                $ipb = Ipb::whereHas('fuel', function ($query) use ($data) {
                    $query->where('asset_id', $data->asset_id);
                })->selectRaw('SUM(usage_liter * unit_price) as total_ipb')->value('total_ipb');
                $ppn = $ipb * 0.11;
                $inspections = InspectionSchedule::where('asset_id', $data->asset_id)
                    ->where('status', 'finish')
                    ->get();
                $itemStocks = [];
                foreach ($inspections as $inspection) {
                    $itemStocks = array_merge($itemStocks, is_array(json_decode($inspection->item_stock, true)) ? json_decode($inspection->item_stock, true) : []);
                }
                $stock = array_sum($itemStocks);
                $total = $stock > 0 ? ($ipb + $ppn) * $stock : ($ipb + $ppn);
                return number_format($total, 0, ',', '.') ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'asset_id',
            'loadsheet',
        ];
        $keyword = $request->search['value'] ?? '';

        $data = Loadsheet::selectRaw('asset_id, SUM(loadsheet) as total_loadsheet')
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->groupBy('asset_id');

        if (session('selected_project_id')) {
            $data->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }

        return $data;
    }


    public function create(Request $request)
    {
        return view('main.asset_performance.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data = LoadhseetTarget::create($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan! ' . $th->getMessage(),
            ]);
        }
    }


    public function expanses()
    {
        if (session('selected_project_id')) {
            $managementProject = ManagementProject::find(Crypt::decrypt(session('selected_project_id')));

            $assetIds = $managementProject->asset_id;

            $tax = RecordTax::whereIn('asset_id', $assetIds)->sum('summary');
            $rent = RecordRent::whereIn('asset_id', $assetIds)->sum('summary');
            $insurance = RecordInsurance::whereIn('asset_id', $assetIds)->sum('summary');
        } else {
            $tax = RecordTax::sum('summary');
            $rent = RecordRent::sum('summary');
            $insurance = RecordInsurance::sum('summary');
        }
        $other = $tax + $rent + $insurance;

        $ipbQuery = Ipb::selectRaw('SUM(usage_liter * unit_price) as total_ipb');
        if (session('selected_project_id')) {
            $ipbQuery->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }
        $ipb = $ipbQuery->value('total_ipb');

        $ppn = $ipb * 0.11;
        $fuel = $ipb + $ppn;

        $totalHarga = 0;
        $dataItem = InspectionSchedule::all();
        if (session('selected_project_id')) {
            $dataItem->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }

        foreach ($dataItem as $item) {
            $itemIds = json_decode($item->item_id, true) ?? [];
            $itemStocks = json_decode($item->item_stock, true) ?? [];

            foreach ($itemIds as $itemId) {
                $itemModel = Item::find($itemId);
                $harga = $itemModel ? $itemModel->price : 0;
                $stock = $itemStocks[$itemId] ?? 0;
                $totalHarga += $harga * $stock;
            }
        }


        $data = [
            'labels' => ['Other', 'Fuel', 'Item'],
            'series' => [$other, $fuel, $totalHarga],
        ];

        return response()->json($data);
    }
}
