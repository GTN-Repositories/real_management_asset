<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\ManagementProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    //
    public function index()
    {
        return view('main.inspection_schedule.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name ?? '-';
            })
            ->addColumn('type', function ($data) {
                return $data->type ?? '-';
            })
            ->addColumn('note', function ($data) {
                return $data->note ?? '-';
            })
            ->addColumn('managementProject', function ($data) {
                return $data->managementProject->name ?? '-';
            })
            ->addColumn('asset_id', function ($data) {
                return Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate ?? '-';
            })
            ->addColumn('result', function ($data) {
                return $data->result ?? '-';
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? '-';
            })
            ->addColumn('item_name', function ($data) {
                $itemIds = is_array(json_decode($data->item_id, true)) ? json_decode($data->item_id, true) : [];
                $items = Item::whereIn('id', $itemIds)->get()->pluck('name')->implode(', ');
                return $items;
            })
            ->addColumn('item_stock', function ($data) {
                $itemStocks = is_array(json_decode($data->item_stock, true)) ? json_decode($data->item_stock, true) : [];
                return array_sum($itemStocks);
            })
            ->addColumn('kanibal_stock', function ($data) {
                $kanibalStocks = is_array(json_decode($data->kanibal_stock, true)) ? json_decode($data->kanibal_stock, true) : [];
                return array_sum($kanibalStocks);
            })
            ->addColumn('asset_kanibal_name', function ($data) {
                $assetKanibalIds = is_array(json_decode($data->asset_kanibal_id, true)) ? json_decode($data->asset_kanibal_id, true) : [];
                $items = Item::whereIn('id', array_keys($assetKanibalIds))->get()->map(function ($item) use ($assetKanibalIds) {
                    $itemId = (string) Crypt::decrypt($item->id);
                    $item->assetKanibalName = isset($assetKanibalIds[$itemId])
                        ? $assetKanibalIds[$itemId] . ' - ' . Asset::find($assetKanibalIds[$itemId] ?? 0)->name . ' - ' . Asset::find($assetKanibalIds[$itemId] ?? 0)->license_plate
                        : '-';
                    return $item;
                });
                return $items->pluck('assetKanibalName')->implode(', ');
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'type',
            'note',
            'asset_id',
            'management_project_id',
            'result',
            'date',
            'item_id',
            'item_stock',
            'kanibal_stock',
            'asset_kanibal_id'
        ];

        $keyword = $request->search['value'] ?? '';

        $data = Maintenance::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    $query->where(function ($q) use ($keyword, $columns) {
                        foreach ($columns as $column) {
                            $q->orWhere($column, 'LIKE', '%' . $keyword . '%');
                        }
                    });
                }
            });

        if (session('selected_project_id')) {
            $managementProject = ManagementProject::find(Crypt::decrypt(session('selected_project_id')));

            if ($managementProject) {
                $assetIds = $managementProject->asset_id;
                $data->whereIn('id', $assetIds);
            }
        }

        return $data;
    }

    public function create()
    {
        return view('main.inspection_schedule.create-inspection');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $request) {
                try {
                    $asset_id = Crypt::decrypt($data['asset_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $asset_id = $data['asset_id'];
                }
                try {
                    $management_project_id = Crypt::decrypt($data['management_project_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $management_project_id = $data['management_project_id'];
                }
                try {
                    $employee_id = Crypt::decrypt($data['employee_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $employee_id = $data['employee_id'];
                }

                $decryptedItemIds = [];
                $itemStocks = [];
                $kanibalStocks = [];
                $assetKanibalIds = [];
                if (isset($data['selected_items'])) {
                    foreach ($data['selected_items'] as $encryptedItemId) {
                        try {
                            $decryptedItemId = Crypt::decrypt($encryptedItemId['id']);
                            $decryptedItemIds[] = $decryptedItemId;

                            if (isset($encryptedItemId['item_stock'])) {
                                $itemStocks[$decryptedItemId] = $encryptedItemId['item_stock'];
                            }

                            if (isset($encryptedItemId['kanibal_stock'])) {
                                $kanibalStocks[$decryptedItemId] = $encryptedItemId['kanibal_stock'];
                            }

                            if (isset($encryptedItemId['asset_kanibal_id'])) {
                                $assetKanibalIds[$decryptedItemId] = Crypt::decrypt($encryptedItemId['asset_kanibal_id']);
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                Asset::where('id', $asset_id)->update([
                    'status' => 'UnderMaintenance'
                ]);

                $schedule = Maintenance::create([
                    'name' => $data['name'],
                    'date' => $data['date'],
                    'type' => $data['type'],
                    'management_project_id' => $management_project_id,
                    'asset_id' => $asset_id,
                    'note' => $data['note'] ?? null,
                    'result' => $data['result'],
                    'employee_id' => $employee_id,
                    'date_breakdown' => $data['date_breakdown'] ?? null,
                    'action' => $data['action'] ?? null,
                    'major_minor' => $data['major_minor'] ?? null,
                    'hm' => $data['hm'] ?? null,
                    'km' => $data['km'] ?? null,
                    'detail_problem' => $data['detail_problem'] ?? null,
                    'item_id' => json_encode($decryptedItemIds) ?? null,
                    'item_stock' => json_encode($itemStocks) ?? null,
                    'kanibal_stock' => json_encode($kanibalStocks) ?? null,
                    'asset_kanibal_id' => json_encode($assetKanibalIds) ?? null,
                ]);

                foreach ($decryptedItemIds as $itemId) {
                    $item = Item::findOrFail($itemId);

                    if (isset($itemStocks[$itemId])) {
                        $item->decrement('stock', $itemStocks[$itemId]);
                    }
                };

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                    'schedule_id' => $schedule->id,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan! ' . $e->getMessage(),
            ]);
        }
    }
}
