<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Form;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Unit;
use Carbon\Carbon;
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


    public function addItemToSession(Request $request)
    {
        $itemId = $request->item_id;

        $sessionItems = $request->session()->get('selected_item_ids', []);

        if (!in_array($itemId, $sessionItems)) {
            $sessionItems[] = $itemId;
            $request->session()->put('selected_item_ids', $sessionItems);
        }

        return response()->json(['message' => 'Item ID added to session successfully']);
    }

    public function getSelectedItems(Request $request)
    {
        $selectedItemIds = $request->session()->get('selected_item_ids', []);
        $items = [];

        if (!empty($selectedItemIds)) {
            try {
                $decryptedSelected = array_map(fn($id) => Crypt::decrypt($id), $selectedItemIds);
                $items = Item::whereIn('id', $decryptedSelected)->get();
            } catch (\Exception $e) {
                $items = [];
            }
        }

        return response()->json($items);
    }

    public function removeItemFromSession(Request $request)
    {
        $itemId = $request->item_id;
        $sessionItems = $request->session()->get('selected_item_ids', []);

        $updatedItems = array_filter($sessionItems, function ($id) use ($itemId) {
            try {
                return Crypt::decrypt($id) != Crypt::decrypt($itemId);
            } catch (\Exception $e) {
                return true;
            }
        });

        $request->session()->put('selected_item_ids', array_values($updatedItems));

        return response()->json(['message' => 'Item removed from session successfully']);
    }

    public function clearAllItemsFromSession(Request $request)
    {
        $request->session()->forget('selected_item_ids');
        return response()->json(['message' => 'All items removed from session successfully']);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $request) {
                $asset_id = Crypt::decrypt($data['asset_id']);

                $selectedItemIds = $request->session()->get('selected_item_ids', []);

                $decryptedItemIds = [];
                foreach ($selectedItemIds as $encryptedItemId) {
                    try {
                        $decryptedItemIds[] = Crypt::decrypt($encryptedItemId);
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Create schedule
                $schedule = InspectionSchedule::create([
                    'name' => $data['name'],
                    'date' => $data['date'],
                    'type' => $data['type'],
                    'asset_id' => $asset_id,
                    'note' => $data['note'],
                    'item_id' => json_encode($decryptedItemIds)
                ]);

                // Clear the session after successful save
                $request->session()->forget('selected_item_ids');

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
            $encryptedIds = array_map(function ($id) {
                return Crypt::encrypt($id);
            }, $itemIds);

            session(['selected_item_ids' => $encryptedIds]);

            $items = Item::whereIn('id', $itemIds)->get();

            return view('main.inspection_schedule.edit', compact('data', 'items'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $selectedItemIds = $request->session()->get('selected_item_ids', []);
        $decryptedItemIds = [];
        foreach ($selectedItemIds as $encryptedItemId) {
            try {
                $decryptedItemIds[] = Crypt::decrypt($encryptedItemId);
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return $this->atomic(function () use ($data, $id, $decryptedItemIds, $request) {
                $uniqueDecryptedItemIds = array_values(array_unique($decryptedItemIds));
                $data['item_id'] = $uniqueDecryptedItemIds;
                $schedule = InspectionSchedule::findByEncryptedId($id)->update($data);

                $request->session()->forget('selected_item_ids');

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

    public function showQuizDetails($scheduleId) {}
}
