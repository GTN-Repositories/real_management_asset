<?php

namespace App\Http\Controllers\Main;

use App\Exports\ExportMaintenance;
use App\Http\Controllers\Controller;
use App\Imports\ImportMaintenance;
use App\Mail\ChangeStatusAssetEmail;
use App\Models\Asset;
use App\Models\GeneralSetting;
use App\Models\InspectionComment;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\MaintenanceSparepart;
use App\Models\ManagementProject;
use App\Models\StatusAsset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
            })
            ->when($request->asset_id, function ($query) use ($request) {
                $query->whereHas('inspection_schedule', function ($q) use ($request) {
                    $assetIds = array_map('intval', (array) $request->asset_id);
                    $q->whereIn('asset_id', $assetIds);
                });
            });

        if (session('selected_project_id')) {
            $data->whereHas('inspection_schedule', function ($q) {
                $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
            });
        }
        
        $data = $data->get();

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

                
                $asset = Asset::where('id', $inspection_schedule->asset_id)->first();
                $statusBefore = $asset->status;
                $asset->update([
                    'status' => 'UnderMaintenance'
                ]);

                if ($statusBefore !== $asset->status) {
                    StatusAsset::create([
                        'asset_id' => Crypt::decrypt($asset->id),
                        'status_before' => $statusBefore,
                        'status_after' => $asset->status,
                    ]);
                }

                // SEND EMAIL
                $general = GeneralSetting::where('group', 'reminder')->where('key', 'reminder_change_status_asset')->orderBy('id', 'desc')->first();
                if ($general && $general->status == 'active') {
                    $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');
                    Mail::to($generalEmailSmtp)->send(new ChangeStatusAssetEmail($asset));
                }

                $maintenance = Maintenance::create($data);

                if (isset($data['selected_items'])) {
                    foreach ($data['selected_items'] as $key => $value) {
                        $item_id = Crypt::decrypt($value['id']);
                        $asset_kanibal_id = null;
                        if ($value['asset_kanibal_id'] != "null") {
                            $asset_kanibal_id = str_replace('AST - ', '', $value['asset_kanibal_id']);
                        }
                        $quantity = $value['item_stock'] ?? $value['kanibal_stock'];

                        MaintenanceSparepart::create([
                            'maintenance_id' => Crypt::decrypt($maintenance->id),
                            'warehouse_id' => Crypt::decrypt($data['werehouse_id']),
                            'item_id' => $item_id,
                            'asset_id' => $asset_kanibal_id,
                            'quantity' => $quantity,
                            'type' => ($asset_kanibal_id != null) ? 'Replacing' : 'Stock',
                        ]);

                        $item = Item::findOrFail($item_id);
        
                        if (isset($value['item_stock'])) {
                            $item->decrement('stock', $value['item_stock']);
                        }
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan dengan ID MNTS-'.Crypt::decrypt($maintenance->id).'!',
                    'schedule_id' => $maintenance->id,
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

            $comments = InspectionComment::where('inspection_schedule_id', Crypt::decrypt($data->id))->get();
            $maintenanceSparepart = MaintenanceSparepart::where('maintenance_id', Crypt::decrypt($id))->get();

            return view('main.inspection_schedule.edit-maintenance', compact('data', 'maintenance', 'comments', 'maintenanceSparepart'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        // try {
            return $this->atomic(function () use ($request, $id) {
                $maintenance = Maintenance::findByEncryptedId($id);
                $schedule = InspectionSchedule::find($maintenance->inspection_schedule_id);

                $data = $request->only(['status', 'comment', 'asset_id']);

                try {
                    $assst_id = Crypt::encrypt($data['asset_id']);
                } catch (\Exception $e) {
                    $assst_id = $data['asset_id'];
                }

                $asset = Asset::where('id', Crypt::decrypt($assst_id))->first();
                $statusBefore = $asset->status;
                $asset->update([
                    'status' => $data['status']
                ]);

                if ($statusBefore !== $asset->status) {
                    StatusAsset::create([
                        'asset_id' => Crypt::decrypt($asset->id),
                        'status_before' => $statusBefore,
                        'status_after' => $asset->status,
                        'type' => ($asset->status == 'RFU' || $asset->status == 'Scrap' ||  $asset->status == 'Aktif') ? 'maintenance' : null,
                    ]);

                    // SEND EMAIL
                    $general = GeneralSetting::where('group', 'reminder')->where('key', 'reminder_change_status_asset')->orderBy('id', 'desc')->first();
                    if ($general && $general->status == 'active') {
                        $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');
                        Mail::to($generalEmailSmtp)->send(new ChangeStatusAssetEmail($asset));
                    }
                }

                $schedule->update($data);

                if (isset($data['comment'])) {
                    $comment = InspectionComment::create([
                        'inspection_schedule_id' => Crypt::decrypt($schedule->id),
                        'comment' => $data['comment'],
                        'user_id' => Auth::user()->id,
                        'time_note' => Carbon::now(),
                    ]);
                }

                $startMaintenance = Carbon::parse($request->get('start_maintenace'));
                $endMaintenance = Carbon::parse($request->get('end_maintenace'));
                $deviasi = $startMaintenance->diffInHours($endMaintenance);

                $start_date = Carbon::parse($maintenance->date);
                $end_rfu = Carbon::parse($request->get('finish_at'));
                $delay = $start_date->diffInHours($end_rfu);

                $maintenance->code_delay = $request->get('code_delay');
                $maintenance->delay_reason = $request->get('delay_reason');
                $maintenance->estimate_finish = $request->get('estimate_finish');
                $maintenance->delay_hours = $delay;
                $maintenance->start_maintenace = $request->get('start_maintenace');
                $maintenance->end_maintenace = $request->get('end_maintenace');
                $maintenance->deviasi = $deviasi;
                $maintenance->finish_at = $request->get('finish_at');
                $maintenance->hm = $request->get('hm');
                $maintenance->km = $request->get('km');
                $maintenance->status = $data['status'];
                $maintenance->location = $request->get('location');
                $maintenance->detail_problem = $request->get('detail_problem');
                $maintenance->action_to_do = $request->get('action_to_do');
                $maintenance->urgention = $request->get('urgention');
                $maintenance->save();

                if (isset($request['werehouse_id'])) {
                    MaintenanceSparepart::where('maintenance_id', Crypt::decrypt($maintenance->id))->update([
                        'warehouse_id' => Crypt::decrypt($request['werehouse_id']),
                    ]);
                }

                if (isset($request['selected_items'])) {
                    foreach ($request['selected_items'] as $key => $value) {
                        $item_id = Crypt::decrypt($value['id']);
                        $asset_kanibal_id = null;
                        if ($value['asset_kanibal_id'] != "null") {
                            $asset_kanibal_id = str_replace('AST - ', '', $value['asset_kanibal_id']);
                        }
                        $quantity = $value['item_stock'] ?? $value['kanibal_stock'];

                        // dd($item_id, $asset_kanibal_id, $quantity);
                        MaintenanceSparepart::create([
                            'maintenance_id' => Crypt::decrypt($maintenance->id),
                            'warehouse_id' => Crypt::decrypt($request['werehouse_id']),
                            'item_id' => $item_id,
                            'asset_id' => $asset_kanibal_id,
                            'quantity' => (int)$quantity,
                            'type' => ($asset_kanibal_id != null) ? 'Replacing' : 'Stock',
                        ]);

                        $item = Item::findOrFail($item_id);
        
                        if (isset($value['item_stock'])) {
                            $item->decrement('stock', $value['item_stock']);
                        }
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Status berhasil diperbarui!',
                ]);

                return redirect()->back()->with('success', 'Status berhasil diperbarui!');
            });
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Data gagal diperbarui! ' . $th->getMessage(),
        //     ], 500);
        // }
    }

    public function maintenanceStatus()
    {
        $asset = Asset::get();

        $data = [];
        foreach ($asset as $key => $value) {
            $maintenance = Maintenance::whereHas('inspection_schedule', function ($q) use ($value) {
                $q->where('asset_id', Crypt::decrypt($value->id));
            });
            if (session('selected_project_id')) {
                $maintenance->whereHas('inspection_schedule', function ($q) {
                    $q->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
                });
            }

            $total = $maintenance->count();


            $data[] = [
                'name' => 'AST - '. Crypt::decrypt($value->id) . ' - ' . $value->name. ' - ' . $value->serial_number,
                'status' => $value->status,
                'total' => $total,
            ];
        }

        $dataSorting = collect($data)->sortByDesc('total')->where('total', '>', 0)->values()->all();
        
        return datatables()->of($dataSorting)
            ->addColumn('name', function ($data) {
                return $data['name'];
            })
            ->addColumn('status', function ($data) {
                return $data['status'];
            })
            ->addColumn('total', function ($data) {
                return $data['total'];
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function importForm()
    {
        return view('main.inspection_schedule.maintenance.import');
    }

    public function importExcel(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded!'
                ], 400);
            }

            $file = $request->file('excel_file');
            Excel::import(new ImportMaintenance, $file);

            return response()->json([
                'status' => true,
                'message' => 'Data imported successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel()
    {
        $fileName = 'Maintenance ' . now()->format('Ymd_His') . '.xlsx';
        $data = Maintenance::whereHas('inspection_schedule', function ($query) {
            if (session('selected_project_id')) {
                $query->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
            }
        })
        ->get();

        return Excel::download(new ExportMaintenance($data), $fileName);
    }
}
