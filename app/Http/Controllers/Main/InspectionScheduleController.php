<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Form;
use App\Models\InspectionComment;
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

                $schedule = InspectionSchedule::create([
                    'name' => $data['name'],
                    'date' => $data['date'],
                    'type' => $data['type'],
                    'asset_id' => $asset_id,
                    'note' => $data['note'],
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

    public function edit(string $id)
    {
        try {
            $data = InspectionSchedule::findByEncryptedId($id);

            $itemIds = json_decode($data->item_id, true) ?? [];
            $itemStocks = json_decode($data->item_stock, true) ?? [];
            $kanibalStocks = json_decode($data->kanibal_stock, true) ?? [];
            $assetKanibalIds = json_decode($data->asset_kanibal_id, true) ?? [];

            $items = Item::whereIn('id', $itemIds)->get()->map(function ($item) use ($itemStocks, $kanibalStocks) {
                $itemId = (string) $item->id;
                $item->stock_in_schedule = isset($itemStocks[$itemId]) ? $itemStocks[$itemId] : 1;
                $item->kanibal_stock_in_schedule = isset($kanibalStocks[$itemId]) ? $kanibalStocks[$itemId] : 0;
                return $item;
            });

            $comments = InspectionComment::where('inspection_schedule_id', Crypt::decrypt($data->id))->get();

            return view('main.inspection_schedule.edit', compact('data', 'items', 'comments', 'assetKanibalIds'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            return $this->atomic(function () use ($request, $id) {
                $schedule = InspectionSchedule::findByEncryptedId($id);

                $data = $request->only(['status', 'note', 'comment']);

                $schedule->update($data);

                if (isset($data['comment'])) {
                    $comment = InspectionComment::create([
                        'inspection_schedule_id' => Crypt::decrypt($schedule->id),
                        'comment' => $data['comment'],
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Status berhasil diperbarui!',
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
