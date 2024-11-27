<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Form;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InspectionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->data($request);

        return view('main.inspection_schedule.index', compact('data'));
    }

    public function data(Request $request)
    {
        $columns = [
            'id',
            'name',
            'type',
            'asset_id',
            'note',
            'date',
        ];

        $keyword = $request->search;
        $start_date = ($request->start_date != '') ? $request->start_date . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $end_date = ($request->end_date != '') ? $request->end_date . ' 23:59:59' : now()->endOfMonth()->format('Y-m-d') . ' 23:59:59';
        $type = $request->type;

        $data = InspectionSchedule::orderBy('created_at', 'asc')
            ->select($columns)
            ->whereBetween('date', [$start_date, $end_date])
            ->where(function ($query) use ($type, $keyword, $columns) {
                if ($type != '') {
                    $query->where('type', $type);
                }

                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

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
                $asset_id = Crypt::decrypt($data['asset_id']);
                $asset_kanibal_id = isset($data['asset_kanibal_id']) ? Crypt::decrypt($data['asset_kanibal_id']) : null;

                $decryptedItemIds = [];
                $itemStocks = [];
                if (isset($data['selected_items'])) {
                    foreach ($data['selected_items'] as $encryptedItemId) {
                        try {
                            $decryptedItemId = Crypt::decrypt($encryptedItemId['id']);
                            $decryptedItemIds[] = $decryptedItemId;
                            $itemStocks[$decryptedItemId] = $encryptedItemId['stock'];
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                $schedule = InspectionSchedule::create([
                    'name' => $data['name'],
                    'date' => $data['date'],
                    'type' => $data['type'],
                    'asset_id' => $asset_id,
                    'note' => $data['note'],
                    'item_id' => json_encode($decryptedItemIds) ?? null,
                    'item_stock' => json_encode($itemStocks) ?? null,
                    'asset_kanibal_id' => $asset_kanibal_id,
                ]);

                foreach ($decryptedItemIds as $itemId) {
                    $item = Item::findOrFail($itemId);
                    $item->decrement('stock', $itemStocks[$itemId]);
                }

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

            $itemIds = json_decode($data->item_id, true) ?? [];
            $itemStocks = json_decode($data->item_stock, true) ?? [];

            $items = Item::whereIn('id', $itemIds)->get()->map(function ($item) use ($itemStocks) {
                $item->stock_in_schedule = $itemStocks[$item->id] ?? 1;
                return $item;
            });
            return view('main.inspection_schedule.edit', compact('data', 'items'));
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

                $data['asset_id'] = Crypt::decrypt($data['asset_id']);
                $data['asset_kanibal_id'] = isset($data['asset_kanibal_id']) ? Crypt::decrypt($data['asset_kanibal_id']) : null;

                $decryptedItemIds = [];
                $itemStocks = [];


                if (isset($data['selected_items'])) {
                    foreach ($data['selected_items'] as $encryptedItemId) {
                        try {
                            $decryptedItemId = Crypt::decrypt($encryptedItemId['id']);
                            $decryptedItemIds[] = $decryptedItemId;
                            $itemStocks[$decryptedItemId] = $encryptedItemId['stock'];
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                $data['item_id'] = json_encode($decryptedItemIds);
                $data['item_stock'] = json_encode($itemStocks);

                $previousStocks = json_decode($schedule->item_stock, true) ?? [];
                foreach ($decryptedItemIds as $itemId) {
                    $item = Item::findOrFail($itemId);
                    $previousStock = $previousStocks[$itemId] ?? 0;
                    $currentStock = $itemStocks[$itemId];

                    if ($previousStock !== $currentStock) {
                        $item->decrement('stock', $currentStock - $previousStock);
                    }
                }

                $removedItemIds = array_diff(array_keys($previousStocks), $decryptedItemIds);
                foreach ($removedItemIds as $removedItemId) {
                    $removedItem = Item::findOrFail($removedItemId);
                    $removedItem->increment('stock', $previousStocks[$removedItemId]);
                }
                
                $schedule->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal diperbarui! ' . $th->getMessage(),
            ], 500);
        }
    }
}
