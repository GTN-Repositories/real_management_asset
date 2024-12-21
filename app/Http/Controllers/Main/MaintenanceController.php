<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionComment;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\ManagementProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $columns = [
            'id',
            'name',
            'workshop',
            'inspection_schedule_id',
            'employee_id',
            'status',
            'date',
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
            })->get();

        foreach ($data as $key => $value) {
            $value['start'] = Carbon::parse($value['date'])->format('Y-m-d') . ' 00:00:00';
            $value['end'] = Carbon::parse($value['date'])->format('Y-m-d') . ' 23:59:59';
            $value['type'] = $value->inspection_schedule->type ?? '';
        }

        return $data;
    }

    public function create()
    {
        return view('main.inspection_schedule.create-maintenance');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $request) {
                $inspection_schedule = InspectionSchedule::findByEncryptedId($data['inspection_schedule_id']);
                $data['inspection_schedule_id'] = Crypt::decrypt($inspection_schedule->id);

                // $employee_id = [];
                // foreach ($data['employee_id'] as $key => $value) {
                //     $employee_id[] = Crypt::decrypt($value);
                // }
                $data['employee_id'] = json_encode($data['employee_id']);

                $data['status'] = 1;

                Asset::where('id', $inspection_schedule->asset_id)->update([
                    'status' => 'UnderMaintenance'
                ]);

                $schedule = Maintenance::create($data);

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
            $maintenance = Maintenance::findByEncryptedId($id);
            $data = InspectionSchedule::find($maintenance->inspection_schedule_id);

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

            return view('main.inspection_schedule.edit-maintenance', compact('data', 'maintenance', 'items', 'comments', 'assetKanibalIds'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            return $this->atomic(function () use ($request, $id) {
                $maintenance = Maintenance::findByEncryptedId($id);
                $schedule = InspectionSchedule::find($maintenance->inspection_schedule_id);

                $data = $request->only(['status', 'comment', 'asset_id']);

                try {
                    $assst_id = Crypt::encrypt($data['asset_id']);
                } catch (\Exception $e) {
                    $assst_id = $data['asset_id'];
                }

                Asset::where('id', Crypt::decrypt($assst_id))->update([
                    'status' => $data['status']
                ]);

                $schedule->update($data);

                if (isset($data['comment'])) {
                    $comment = InspectionComment::create([
                        'inspection_schedule_id' => Crypt::decrypt($schedule->id),
                        'comment' => $data['comment'],
                        'user_id' => Auth::user()->id,
                        'time_note' => Carbon::now(),
                    ]);
                }

                $maintenance->code_delay = $request->get('code_delay');
                $maintenance->delay_reason = $request->get('delay_reason');
                $maintenance->estimate_finish = $request->get('estimate_finish');
                $maintenance->delay_hours = $request->get('delay_hours');
                $maintenance->start_maintenace = $request->get('start_maintenace');
                $maintenance->end_maintenace = $request->get('end_maintenace');
                $maintenance->deviasi = $request->get('deviasi');
                $maintenance->finish_at = $request->get('finish_at');
                $maintenance->hm = $request->get('hm');
                $maintenance->km = $request->get('km');
                $maintenance->location = $request->get('location');
                $maintenance->detail_problem = $request->get('detail_problem');
                $maintenance->action_to_do = $request->get('action_to_do');
                $maintenance->urgention = $request->get('urgention');
                $maintenance->save();

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
}
