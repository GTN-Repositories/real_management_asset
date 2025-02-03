<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\MaintenanceSparepart;
use App\Models\Site;
use App\Models\Werehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class WerehouseController extends Controller
{
    public function index()
    {
        return view('main.werehouse.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $checkbox =
                    '<div class="custom-control custom-checkbox">
                    <input class="custom-control-input checkbox" id="checkbox' .
                    $data->id .
                    '" type="checkbox" value="' .
                    $data->id .
                    '" />
                    <label class="custom-control-label" for="checkbox' .
                    $data->id .
                    '"></label>
                </div>';

                return $checkbox;
            })
            ->addColumn('ids', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('location', function ($data) {
                return $data->location ?? null;
            })
            ->addColumn('management_project', function ($data) {
                return 'PRJ - ' . $data->management_project_id . ' ' . ($data->managementProject->name ?? null);
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('werehouse-edit')) {
                    # code...
                    $btn .= '<a href="javascript:void(0);" class="btn btn-info btn-sm me-1" title="Show Data" onclick="showData(\'' . $data->id . '\')"><i class="ti ti-eye"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('werehouse-edit')) {
                    # code...
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                    }
                }
                if (auth()->user()->hasPermissionTo('werehouse-delete')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                    }
                }
                $btn .= '</div>';

                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'location',
            'management_project_id',
            'created_at',
        ];

        $keyword = $request->search['value'] ?? null;

        $data = Werehouse::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if (session('selected_project_id')) {
            $data->whereHas('managementProject', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        return $data;
    }


    public function create()
    {
        return view('main.werehouse.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                if (isset($data['management_project_id'])) {
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                }

                $data = Werehouse::create($data);

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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = Werehouse::findByEncryptedId($id);
        return view('main.werehouse.show', compact('data'));
    }

    public function showData(Request $request)
    {
        $data = $this->showDataGet($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('stock', function ($data) use ($request) {
                $stock = $this->getStock($data->item_id, Crypt::decrypt($request->werehouse_id));
                return $stock;
            })
            ->addColumn('used_stock', function ($data) use ($request) {
                $usedStock = $this->getUsedStock($data->item_id, $request->werehouse_id);
                return $usedStock ?? 0;
            })
            ->addColumn('balance', function ($data) use ($request) {
                $stock = $this->getStock($data->item_id, Crypt::decrypt($request->werehouse_id));

                $used_stock = $this->getUsedStock($data->item_id, $request->werehouse_id);
                $summary_used_stock = $used_stock ?? 0;

                return $stock - $summary_used_stock;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function showDataGet(Request $request)
    {

        $columns = [
            'items.id as item_id',
            'items.name',
            'items.stock'
        ];

        $keyword = $request->search['value'] ?? null;

        $data = Item::select($columns)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('items.name', 'LIKE', '%' . $keyword . '%');
            });

        return $data;
    }

    private function getUsedStock($itemId, $werehouseId = null)
    {
        if ($werehouseId) {
            $werehouseId = Crypt::decrypt($werehouseId);

            $schedules = MaintenanceSparepart::where('warehouse_id', $werehouseId)->where('item_id', $itemId)->where('type', 'Stock')->get();
        } else {
            $schedules = InspectionSchedule::where('item_stock', 'LIKE', '%"' . $itemId . '"%')
                ->get();
        }

        $usedStock = $schedules->sum('quantity');

        return $usedStock;
    }

    public function getStock($item_id, $warehouse_id = null)
    {
        $management_project_id = (session('selected_project_id') != null) ? Crypt::decrypt(session('selected_project_id')) : null;

        $stock_increase = ItemStock::where('item_id', $item_id)
            ->where('metode', 'increase')
            ->where('status', 'approved')
            ->when($management_project_id, function ($query) use ($management_project_id) {
                if ($management_project_id) {
                    $query->where('management_project_id', $management_project_id);
                }
            })
            ->when($warehouse_id, function ($query) use ($warehouse_id) {
                if ($warehouse_id) {
                    $query->where('warehouse_id', $warehouse_id);
                }
            })
            ->sum('stock');

        $stock_decrease = ItemStock::where('item_id', $item_id)
            ->where('metode', 'decrease')
            ->where('status', 'approved')
            ->when($management_project_id, function ($query) use ($management_project_id) {
                if ($management_project_id) {
                    $query->where('management_project_id', $management_project_id);
                }
            })
            ->when($warehouse_id, function ($query) use ($warehouse_id) {
                if ($warehouse_id) {
                    $query->where('warehouse_id', $warehouse_id);
                }
            })
            ->sum('stock');

        $stock = $stock_increase - $stock_decrease;

        return $stock;
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Werehouse::findByEncryptedId($id);
        return view('main.werehouse.edit', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                try {
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                } catch (\Throwable $th) {
                    $data['management_project_id'] = $data['management_project_id'];
                }

                $data = Werehouse::findByEncryptedId($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal diperbarui! ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = Werehouse::findByEncryptedId($id);
            $data->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal dihapus! ' . $th->getMessage(),
            ]);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                $decryptedIds = [];
                foreach ($ids as $encryptedId) {
                    $decryptedIds[] = Crypt::decrypt($encryptedId);
                }

                $delete = Werehouse::whereIn('id', $decryptedIds)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }
}
