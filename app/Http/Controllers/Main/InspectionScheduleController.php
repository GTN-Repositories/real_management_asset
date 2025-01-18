<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Mail\ChangeStatusAssetEmail;
use App\Models\Asset;
use App\Models\GeneralSetting;
use App\Models\InspectionComment;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class InspectionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $data = new MaintenanceController();
        $data = $data->data($request);

        return view('main.inspection_schedule.index', compact('data'));
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
            ->addColumn('id', function ($data) {
                return $data->id;
            })
            ->addColumn('format_id', function ($data) {
                return 'INS-' . Crypt::decrypt($data->id);
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
            ->addColumn('date', function ($data) {
                return $data->date ?? '-';
            })
            ->addColumn('werehouse_id', function ($data) {
                return $data->werehouse->name ?? '-';
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
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (!auth()->user()->hasRole('Read only')) {
                    $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow me-2" title="Edit Data" onclick="showData(\'' . $data->id . '\')"><i class="ti ti-edit"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('inspection-schedule-show')) {
                    $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-eye"></i></a>';
                }
                // if (auth()->user()->hasPermissionTo('asset-delete')) {
                if (!auth()->user()->hasRole('Read only')) {
                    $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                }
                // }
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
            'type',
            'asset_id',
            'management_project_id',
            'note',
            'item_id',
            'asset_kanibal_id',
            'status',
            'item_stock',
            'kanibal_stock',
            'created_at',
            'updated_at',
            'date',
            'workshop',
            'employee_id',
            'werehouse_id',
        ];

        $keyword = $request->search['value'];
        // $start_date = ($request->start_date != '') ? $request->start_date . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        // $end_date = ($request->end_date != '') ? $request->end_date . ' 23:59:59' : now()->endOfMonth()->format('Y-m-d') . ' 23:59:59';
        $type = $request->type;

        $data = InspectionSchedule::orderBy('id', 'desc')
            ->select($columns)
            // ->whereBetween('date', [$start_date, $end_date])
            ->where(function ($query) use ($type, $keyword, $columns) {
                if ($type != '') {
                    $query->where('type', $type);   
                }

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

        foreach ($data as $key => $value) {
            $value['start'] = $value['date'] . ' 00:00:00';
            $value['end'] = $value['date'] . ' 23:59:59';
        }

        return $data;
    }

    public function create()
    {
        return view('main.inspection_schedule.create');
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
                    $werehouse_id = Crypt::decrypt($data['werehouse_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $werehouse_id = $data['werehouse_id'];
                }

                $decryptedItemIds = [];
                $itemStocks = [];
                $kanibalStocks = [];
                $assetKanibalIds = [];
                // dd($data['selected_items']);
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

                            if (isset($encryptedItemId['asset_kanibal_id']) && $encryptedItemId['asset_kanibal_id'] !== 'null') {
                                $assetKanibalIds[$decryptedItemId] = str_replace('AST - ', '', $encryptedItemId['asset_kanibal_id']);
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    sort($decryptedItemIds);
                }

                $schedule = InspectionSchedule::create([
                    'name' => $data['name'],
                    'date' => $data['date'],
                    'type' => $data['type'],
                    'management_project_id' => $management_project_id,
                    'asset_id' => $asset_id,
                    'werehouse_id' => $werehouse_id,
                    'note' => $data['note'],
                    'item_id' => json_encode($decryptedItemIds) ?? null,
                    'item_stock' => json_encode($itemStocks) ?? null,
                    'kanibal_stock' => json_encode($kanibalStocks) ?? null,
                    'asset_kanibal_id' => json_encode(array_filter($assetKanibalIds)) ?? null,
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

    public function edit(string $id)
    {
        try {
            $data = InspectionSchedule::findByEncryptedId($id);

            $itemIds = is_array(json_decode($data->item_id, true))
                ? json_decode($data->item_id, true)
                : [];

            $itemStocks = is_array(json_decode($data->item_stock, true))
                ? json_decode($data->item_stock, true)
                : [];

            $kanibalStocks = is_array(json_decode($data->kanibal_stock, true))
                ? json_decode($data->kanibal_stock, true)
                : [];

            $assetKanibalIds = is_array(json_decode($data->asset_kanibal_id, true))
                ? json_decode($data->asset_kanibal_id, true)
                : [];

            $items = Item::whereIn('id', $itemIds)->get()->map(function ($item) use ($itemStocks, $kanibalStocks, $assetKanibalIds) {
                $itemId = (string) Crypt::decrypt($item->id);

                $asset_id = $assetKanibalIds[$itemId] ?? 0;
                $item->stock_in_schedule = $itemStocks[$itemId] ?? 0;
                $item->kanibal_stock_in_schedule = $kanibalStocks[$itemId] ?? 0;
                $item->assetKanibalName = isset($assetKanibalIds[$itemId])
                    ?  $asset_id . ' - ' . Asset::find($assetKanibalIds[$itemId] ?? 0)->name . ' - ' . Asset::find($assetKanibalIds[$itemId] ?? 0)->license_plate
                    : '-';

                return $item;
            });

            // foreach ($assetKanibalIds as $key => $value) {
            //     $asset = Asset::find($value['id']);
            //     $value['name'] = $asset->id . ' - ' . $asset->name . ' - ' . $asset->asset_number;
            // }

            $comments = InspectionComment::where('inspection_schedule_id', Crypt::decrypt($data->id))->get();

            return view('main.inspection_schedule.edit', compact('data', 'items', 'comments', 'assetKanibalIds'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $data = InspectionSchedule::findByEncryptedId($id);

            $itemIds = is_array(json_decode($data->item_id, true))
                ? json_decode($data->item_id, true)
                : [];

            $itemStocks = is_array(json_decode($data->item_stock, true))
                ? json_decode($data->item_stock, true)
                : [];

            $kanibalStocks = is_array(json_decode($data->kanibal_stock, true))
                ? json_decode($data->kanibal_stock, true)
                : [];

            $assetKanibalIds = is_array(json_decode($data->asset_kanibal_id, true))
                ? json_decode($data->asset_kanibal_id, true)
                : [];

            $items = Item::whereIn('id', $itemIds)->get()->map(function ($item) use ($itemIds, $itemStocks, $kanibalStocks, $assetKanibalIds) {
                $itemId = (string) Crypt::decrypt($item->id);
                $entries = [];

                // Jika item memiliki stock biasa
                if (isset($itemStocks[$itemId]) && $itemStocks[$itemId] > 0) {
                    $stockEntry = clone $item;
                    $stockEntry->stock_in_schedule = $itemStocks[$itemId];
                    $stockEntry->kanibal_stock_in_schedule = 0;
                    $stockEntry->assetKanibalName = '-';
                    $stockEntry->assetKanibalId = 0;
                    $entries[] = $stockEntry;
                }

                // Jika item memiliki kanibal stock
                if (isset($kanibalStocks[$itemId]) && $kanibalStocks[$itemId] > 0) {
                    $kanibalEntry = clone $item;
                    $kanibalEntry->stock_in_schedule = 0;
                    $kanibalEntry->kanibal_stock_in_schedule = $kanibalStocks[$itemId];
                    $asset_id = $assetKanibalIds[$itemId] ?? 0;
                    $kanibalEntry->assetKanibalName = isset($assetKanibalIds[$itemId])
                        ? $asset_id . ' - ' . Asset::find($assetKanibalIds[$itemId])->name . ' - ' . Asset::find($assetKanibalIds[$itemId])->license_plate
                        : '-';
                    $kanibalEntry->assetKanibalId = $asset_id;
                    $entries[] = $kanibalEntry;
                }

                return $entries;
            })->flatten(1);

            $comments = InspectionComment::where('inspection_schedule_id', $data->id)->get();

            return view('main.inspection_schedule.show', compact('data', 'items', 'comments'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            return $this->atomic(function () use ($request, $id) {
                $schedule = InspectionSchedule::findByEncryptedId($id);

                $data = $request->all();
                // dd($data);
                try {
                    $assst_id = Crypt::encrypt($data['asset_id']);
                } catch (\Exception $e) {
                    $assst_id = $data['asset_id'];
                }

                $asset = Asset::where('id', Crypt::decrypt($assst_id))->first();

                $asset->update([
                    'status' => $data['status'] ?? $asset->status,
                ]);

                // Handle Selected Items
                $decryptedItemIds = [];
                $itemStocks = [];
                $kanibalStocks = [];
                $assetKanibalIds = [];

                if (isset($data['selected_items'])) {
                    foreach ($data['selected_items'] as $encryptedItemId) {
                        try {
                            $decryptedItemId = Crypt::decrypt($encryptedItemId['id']);
                            $decryptedItemIds[] = $decryptedItemId;

                            // Hanya simpan item stock yang tidak kosong
                            if (!empty($encryptedItemId['item_stock'])) {
                                $itemStocks[$decryptedItemId] = $encryptedItemId['item_stock'];
                            }

                            // Hanya simpan kanibal stock yang tidak kosong
                            if (!empty($encryptedItemId['kanibal_stock'])) {
                                $kanibalStocks[$decryptedItemId] = $encryptedItemId['kanibal_stock'];
                                if (isset($encryptedItemId['asset_kanibal_id']) && $encryptedItemId['asset_kanibal_id'] !== 'null') {
                                    $assetKanibalIds[$decryptedItemId] = str_replace('AST - ', '', $encryptedItemId['asset_kanibal_id']);
                                }
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    sort($decryptedItemIds);
                }

                $data['item_id'] = json_encode($decryptedItemIds) ?? null;
                $data['item_stock'] = json_encode($itemStocks) ?? null;
                $data['kanibal_stock'] = json_encode($kanibalStocks) ?? null;
                $data['asset_kanibal_id'] = json_encode($assetKanibalIds) ?? null;

                // dd($data);  
                $schedule->update($data);

                // SEND EMAIL
                $general = GeneralSetting::where('group', 'reminder')->where('key', 'reminder_change_status_asset')->orderBy('id', 'desc')->first();
                if ($general && $general->status == 'active') {
                    $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');
                    Mail::to($generalEmailSmtp)->send(new ChangeStatusAssetEmail($asset));
                }

                if (isset($data['comment'])) {
                    $comment = InspectionComment::create([
                        'inspection_schedule_id' => Crypt::decrypt($schedule->id),
                        'comment' => $data['comment'],
                        'user_id' => Auth::user()->id,
                        'time_note' => Carbon::now(),
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Status berhasil diperbarui!',
                ]);

                return redirect()->back()->with('success', 'Status berhasil diperbarui!');
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal diperbarui! ' . $th->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $data = InspectionSchedule::findByEncryptedId($id);
            Asset::where('id', $data->asset_id)->update([
                'status' => 'active'
            ]);
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

                $delete = InspectionSchedule::whereIn('id', $decryptedIds)->delete();

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


    public function getStatusLast(Request $request)
    {
        $now = InspectionSchedule::where('id', Crypt::decrypt($request->inspectionScheduleId))->first();

        $before = InspectionSchedule::where('id', '<', Crypt::decrypt($now->id))
            ->where('asset_id', $now->asset_id)
            ->orderBy('id', 'desc')
            ->first();

        $maintenance = Maintenance::where('inspection_schedule_id', Crypt::decrypt($before->id))->first();

        return response()->json([
            'status' => $before->status ?? null,
            'maintenance' => $maintenance,
        ]);
    }
}
