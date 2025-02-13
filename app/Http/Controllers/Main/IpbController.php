<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Mail\FuelStockAddedEmail;
use App\Models\FuelConsumption;
use App\Models\GeneralSetting;
use App\Models\Ipb;
use App\Models\ManagementProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class IpbController extends Controller
{
    //
    public function index()
    {
        $managementProjectId = session('selected_project_id');

        $data['issued_liter'] = Ipb::orderBy('id', 'desc')
        ->when($managementProjectId, function ($query) use ($managementProjectId) {
            $query->where('management_project_id', Crypt::decrypt($managementProjectId));
        })
        ->sum('issued_liter');

        $data['usage_liter'] = Ipb::orderBy('id', 'desc')
        ->when($managementProjectId, function ($query) use ($managementProjectId) {
            $query->where('management_project_id', Crypt::decrypt($managementProjectId));
        })
        ->sum('usage_liter');
        
        $data['balance'] = $data['issued_liter'] - $data['usage_liter'];

        return view('main.ipb.index', compact('data'));
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
            ->addColumn('relationId', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('management_project_id', function ($data) {
                return $data->management_project->name ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->addColumn('issued_liter', function ($data) {
                return number_format($data->issued_liter, 0, ',', '.') ?? null;
            })
            ->addColumn('usage_liter', function ($data) {
                return $data->usage_liter ?? null;
            })
            ->addColumn('balance', function ($data) {
                return number_format($data->balance, 0, ',', '.') ?? null;
            })
            ->addColumn('unit_price', function ($data) {
                return 'Rp. ' . number_format($data->unit_price, 2, ',', '.') ?? null;
            })
            ->addColumn('total_harga', function ($data) {
                return 'Rp. ' . number_format($data->unit_price * $data->usage_liter, 0, ',', '.') ?? null;
            })
            ->addColumn('ppn', function ($data) {
                return 'Rp. ' . number_format(($data->unit_price * $data->usage_liter) * 0.11, 0, ',', '.') ?? null;
            })
            ->addColumn('jumlah', function ($data) {
                return 'Rp. ' . number_format(($data->unit_price * $data->usage_liter) + (($data->unit_price * $data->usage_liter) * 0.11), 0, ',', '.') ?? null;
            })
            ->addColumn('fuel_truck', function ($data) {
                return $data->fuel_truck ?? null;
            })
            ->addColumn('user_id', function ($data) {
                return $data->user->name ?? null;
            })
            ->addColumn('employee_id', function ($data) {
                return $data->employee->name ?? null;
            })
            ->addColumn('location', function ($data) {
                return $data->location ?? null;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('fuel-ipb-edit')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                    }
                }
                if (auth()->user()->hasPermissionTo('fuel-ipb-delete')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                    }
                }
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('literDashboard', function ($data) {
                $liter = $data->liter ?? null;
                if (session('selected_project_id')) {
                    $selectedProjectId = Crypt::decrypt(session('selected_project_id'));
                    if ($data->management_project_id == $selectedProjectId) {
                        return $liter;
                    }
                }
                return $liter;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'date',
            'management_project_id',
            'employee_id',
            'issued_liter',
            'usage_liter',
            'balance',
            'unit_price',
            'fuel_truck',
            'user_id',
            'location',
            'created_at',
            'updated_at',
        ];

        $keyword = $request->keyword ?? "";

        $data = Ipb::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if (session('selected_project_id')) {
            $data->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $data->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        return $data;
    }


    public function create()
    {
        return view('main.ipb.create');
    }

    public function getTotalLiter(Request $request)
    {
        $managementProjectId = $request->management_project_id;
        $date = $request->date;

        $totalLiter = FuelConsumption::where('management_project_id', Crypt::decrypt($managementProjectId))
            ->whereDate('date', $date)
            ->sum('liter');

        return response()->json([
            'total_liter' => $totalLiter
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $data["management_project_id"] = Crypt::decrypt($data["management_project_id"]);
                $data["employee_id"] = isset($data["employee_id"]) ? Crypt::decrypt($data["employee_id"]) : null;
                $data["user_id"] = Auth::user()->id;

                $data['issued_liter'] = isset($data['issued_liter']) && $data['issued_liter'] != '-' ? str_replace('.', '', $data['issued_liter']) : null;
                $data['unit_price'] = isset($data['unit_price']) && $data['unit_price'] != '-' ? str_replace(['.', ','], ['', '.'], $data['unit_price']) : null;

                $lastBalance = Ipb::where('management_project_id', $data["management_project_id"])
                    ->orderBy('id', 'desc')
                    ->value('balance');

                $lastBalance = $lastBalance ?? 0;

                $issuedLiter = $data['issued_liter'] ?? 0;
                $data['usage_liter'] = 0;

                $data['balance'] = $lastBalance + $issuedLiter;

                $data = Ipb::create($data);

                (app(IpbController::class))->synchronizeIpb();

                // SEND REMINDER EMAIL
                $general = GeneralSetting::where('group', 'reminder')->where('key', 'fuel_stock_addition_period')->orderBy('id', 'desc')->first();
                if ($general->status == 'active') {
                    Mail::to('adin72978@gmail.com')->send(new FuelStockAddedEmail($data));
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan dengan ID '.Crypt::decrypt($data->id).'!',
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
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $decryptedId = Crypt::decrypt($id);
        $data = Ipb::findOrFail($decryptedId);

        return view('main.ipb.edit', compact('data'));
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
                    $data["management_project_id"] = Crypt::decrypt($data["management_project_id"]);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data["management_project_id"] = $data["management_project_id"];
                }

                try {
                    $data["employee_id"] = Crypt::decrypt($data["employee_id"]);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data["employee_id"] = $data["employee_id"];
                }

                $data['issued_liter'] = isset($data['issued_liter']) && $data['issued_liter'] != '-' ? str_replace('.', '', $data['issued_liter']) : null;
                $data['usage_liter'] = isset($data['usage_liter']) && $data['usage_liter'] != '-' ? str_replace('.', '', $data['usage_liter']) : null;
                $data['unit_price'] = isset($data['unit_price']) && $data['unit_price'] != '-' ? str_replace(['.', ','], ['', '.'], $data['unit_price']) : null;

                $record = Ipb::findByEncryptedId($id);
                if (!$record) {
                    throw new \Exception('Data tidak ditemukan!');
                }

                $record->update($data);

                $records = Ipb::where('management_project_id', $data["management_project_id"])
                    ->orderBy('id', 'asc')
                    ->get();

                $lastBalance = 0;

                foreach ($records as $row) {
                    $issuedLiter = $row->issued_liter ?? 0;
                    $usageLiter = $row->usage_liter ?? 0;

                    $newBalance = ($lastBalance + $issuedLiter) - $usageLiter;

                    $row->update(['balance' => $newBalance]);

                    $lastBalance = $newBalance;
                }

                (app(IpbController::class))->synchronizeIpb();

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
            $data = Ipb::findByEncryptedId($id);
            $data->delete();

            (app(IpbController::class))->synchronizeIpb();

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

                $delete = Ipb::whereIn('id', $decryptedIds)->delete();

                (app(IpbController::class))->synchronizeIpb();

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

    public function synchronizeIpb()
    {
        try {
            return $this->atomic(function () {
                $records = Ipb::orderBy('date', 'asc')->get();
                $lastBalances = [];

                $project = ManagementProject::get();
                foreach ($project as $key => $value) {
                    $project_id = Crypt::decrypt($value->id);

                    $fuel = FuelConsumption::where('management_project_id', $project_id)->pluck('id');

                    $fuel_ids = [];
                    foreach ($fuel as $key => $id) {
                        $fuel_ids[] = Crypt::decrypt($id);
                    }

                    Ipb::where('management_project_id', $project_id)->where('issued_liter', 0)->whereNotIn('fuel_id', $fuel_ids)->delete();
                }

                foreach ($records as $row) {
                    $managementProjectId = $row->management_project_id;
                    $issuedLiter = $row->issued_liter ?? 0;
                    $usageLiter = $row->usage_liter ?? 0;

                    if (!isset($lastBalances[$managementProjectId])) {
                        $lastBalances[$managementProjectId] = 0;
                    }

                    // Jika tidak ada issued_liter dan tiba-tiba menambah usage_liter
                    if ($issuedLiter == 0 && $usageLiter > 0) {
                        $newBalance = $lastBalances[$managementProjectId] - $usageLiter;
                    } else {
                        $newBalance = ($lastBalances[$managementProjectId] + $issuedLiter) - $usageLiter;
                    }

                    $row->update(['balance' => $newBalance]);
                    $lastBalances[$managementProjectId] = $newBalance;
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil disinkronisasi!'
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal sinkronisasi data! ' . $th->getMessage()
            ]);
        }
    }
}
