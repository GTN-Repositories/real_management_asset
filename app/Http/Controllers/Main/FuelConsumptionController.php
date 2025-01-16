<?php

namespace App\Http\Controllers\Main;

use App\Exports\FuelConsumptionExport;
use App\Http\Controllers\Controller;
use App\Imports\ImportFuel;
use App\Models\FuelConsumption;
use App\Models\Ipb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\TryCatch;

class FuelConsumptionController extends Controller
{
    //

    public function index()
    {
        return view('main.fuel_consumtion.index');
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
            ->addColumn('asset_id', function ($data) {
                return isset($data->asset) ? Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate : null;
            })
            ->addColumn('user_id', function ($data) {
                return $data->employee->name ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->addColumn('liter', function ($data) {
                return number_format($data->liter, 0, ',', '.') . ' liter' ?? null;
            })
            ->addColumn('hm', function ($data) {
                return number_format($data->hm, 0, ',', '.') ?? null;
            })
            ->addColumn('loadsheet', function ($data) {
                return number_format($data->loadsheet, 0, ',', '.') ?? null;
            })
            ->addColumn('price', function ($data) {
                return 'Rp. ' . number_format($data->price, 0, ',', '.') ?? null;
            })
            ->addColumn('category', function ($data) {
                return $data->category ?? null;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('fuel-edit')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                    }
                }
                if (auth()->user()->hasPermissionTo('fuel-delete')) {
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
            'management_project_id',
            'asset_id',
            'user_id',
            'date',
            'liter',
            'hm',
            'price',
            'category',
            'loadsheet',
            'hours',
        ];

        $keyword = $request->keyword ?? "";
        // $project_id = $this->projectId();
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

        $data = FuelConsumption::orderBy('id', 'desc')
            ->select($columns)
            // ->whereIn($project_id)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->filled('filterType')) {
            switch ($request->filterType) {
                case 'hari ini':
                    $data->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $data->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $data->whereRaw('MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'bulan kemarin':
                    $data->whereRaw('MONTH(date) = MONTH(NOW()) - 1 AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'tahun ini':
                    $data->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $data->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        if (session('selected_project_id')) {
            $data->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        return $data;
    }


    public function create()
    {
        return view('main.fuel_consumtion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                // $data['price'] = isset($data['price']) && $data['price'] != '-' ? str_replace('.', '', $data['price']) : null;
                // $data['loadsheet'] = isset($data['loadsheet']) && $data['loadsheet'] != '-' ? str_replace('.', '', $data['loadsheet']) : null;
                $data['liter'] = isset($data['liter']) && $data['liter'] != '-' ? str_replace('.', '', $data['liter']) : null;
                $data['hm'] = isset($data['hm']) && $data['hm'] != '-' ? str_replace('.', '', $data['hm']) : null;
                // $data['hours'] = isset($data['hours']) && $data['hours'] != '-' ? str_replace('.', '', $data['hours']) : null;
                $data['lasted_km_asset'] = isset($data['lasted_km_asset']) && $data['lasted_km_asset'] != '-' ? str_replace('.', '', $data['lasted_km_asset']) : null;

                $data["hours"] = 0;
                $data["asset_id"] = crypt::decrypt($data["asset_id"]);
                $data["management_project_id"] = crypt::decrypt($data["management_project_id"]);
                $data["user_id"] = crypt::decrypt($data["user_id"]);
                $data = FuelConsumption::create($data);

                $field['management_project_id'] = $data->management_project_id;
                $field['usage_liter'] = $data->liter;
                $field['date'] = $data->date;

                $lastBalance = Ipb::where('management_project_id', $data["management_project_id"])
                    ->orderBy('id', 'desc')
                    ->value('balance');
                $unitprice = Ipb::where('management_project_id', $data["management_project_id"])
                    ->orderBy('id', 'desc')
                    ->value('unit_price');

                $lastBalance = $lastBalance ?? 0;
                $field['issued_liter'] = 0;
                $field['balance'] = $lastBalance - $field['usage_liter'];
                $field['unit_price'] = $unitprice;
                $field['fuel_id'] = Crypt::decrypt($data->id);
                Ipb::create($field);

                (app(IpbController::class))->synchronizeIpb();

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
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $decryptedId = Crypt::decrypt($id);
        $data = FuelConsumption::findOrFail($decryptedId);
        $data->assets = $data->getAssetsAttribute();

        return view('main.fuel_consumtion.edit', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $id) {
                // $data['price'] = isset($data['price']) && $data['price'] != '-' ? str_replace('.', '', $data['price']) : null;
                $data['loadsheet'] = isset($data['loadsheet']) && $data['loadsheet'] != '-' ? str_replace('.', '', $data['loadsheet']) : null;
                $data['liter'] = isset($data['liter']) && $data['liter'] != '-' ? str_replace('.', '', $data['liter']) : null;
                // $data['hours'] = isset($data['hours']) && $data['hours'] != '-' ? str_replace('.', '', $data['hours']) : null;
                $data['hm'] = isset($data['hm']) && $data['hm'] != '-' ? str_replace('.', '', $data['hm']) : null;
                $data['lasted_km_asset'] = isset($data['lasted_km_asset']) && $data['lasted_km_asset'] != '-' ? str_replace('.', '', $data['lasted_km_asset']) : null;

                try {
                    $data["management_project_id"] = Crypt::decrypt($data["management_project_id"]);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data["management_project_id"] = $data["management_project_id"];
                }
                try {
                    $data["asset_id"] = crypt::decrypt($data["asset_id"]);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data["asset_id"] = $data["asset_id"];
                }
                try {
                    $data["user_id"] = crypt::decrypt($data["user_id"]);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data["user_id"] = $data["user_id"];
                }
                $data["hours"] = 0;
                $fuelRecord = FuelConsumption::findByEncryptedId($id);
                if (!$fuelRecord) {
                    throw new \Exception('Data FuelConsumption tidak ditemukan!');
                }

                $fuelRecord->update($data);
                $field['usage_liter'] = $data['liter'];
                $ipbRecords = Ipb::where('fuel_id', Crypt::decrypt($id))
                    ->orderBy('id', 'asc')
                    ->first();

                $ipbRecords->update($field);

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
            $data = FuelConsumption::findByEncryptedId($id);
            $ipb = Ipb::where('fuel_id', Crypt::decrypt($id))->first();
            $ipb->delete();
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

                $ipb = Ipb::whereIn('fuel_id', $decryptedIds)->delete();

                $delete = FuelConsumption::whereIn('id', $decryptedIds)->delete();

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

    public function import(Request $request)
    {
        return view('main.fuel_consumtion.import');
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
            Excel::import(new ImportFuel, $file);

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
        $fileName = 'Fuel Consumption' . now()->format('Ymd_His') . '.xlsx';
        $data = FuelConsumption::all();

        if(session('selected_project_id')) {
            $data = $data->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }

        return Excel::download(new FuelConsumptionExport($data), $fileName);
    }
}
