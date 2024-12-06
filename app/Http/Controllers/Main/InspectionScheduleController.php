<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionComment;
use App\Models\InspectionSchedule;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                try{
                    $asset_id = Crypt::decrypt($data['asset_id']);
                }catch(\Illuminate\Contracts\Encryption\DecryptException $e){
                    $asset_id = $data['asset_id'];
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

    public function update(Request $request, string $id)
    {
        try {
            return $this->atomic(function () use ($request, $id) {
                $schedule = InspectionSchedule::findByEncryptedId($id);

                $data = $request->only(['status', 'comment']);
                $schedule->update($data);

                if (isset($data['comment'])) {
                    $comment = InspectionComment::create([
                        'inspection_schedule_id' => Crypt::decrypt($schedule->id),
                        'comment' => $data['comment'],
                        'user_id' => Auth::user()->id,
                        'time_note' => Carbon::now(),
                    ]);
                }

                // return response()->json([
                //     'status' => true,
                //     'message' => 'Status berhasil diperbarui!',
                // ]);

                return redirect()->back()->with('success', 'Status berhasil diperbarui!');
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal diperbarui! ' . $th->getMessage(),
            ], 500);
        }
    }
}
