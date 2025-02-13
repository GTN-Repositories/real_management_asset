<?php

namespace App\Http\Controllers\Main;

use App\Exports\LoadsheetExport;
use App\Http\Controllers\Controller;
use App\Imports\ImportLoadsheet;
use App\Models\Currency;
use App\Models\Loadsheet;
use App\Models\ManagementProject;
use App\Models\SoilType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class LoadsheetController extends Controller
{
    //
    public function index()
    {
        return view('main.loadsheet.index');
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
            ->addColumn('management_project_id', function ($data) {
                return $data->management_project_id . ' - ' . $data->management_project->name ?? '-';
            })
            ->addColumn('asset_id', function ($data) {
                return ($data->asset_id ?? '') . ' - ' . ($data->asset->name ?? '');
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? '-';
            })
            ->addColumn('location', function ($data) {
                return $data->location ?? '-';
            })
            ->addColumn('soil_type_id', function ($data) {
                return optional($data->soilType)->name ?? '-';
            })
            ->addColumn('bpit', function ($data) {
                return $data->bpit ?? '-';
            })
            ->addColumn('lose_factor', function ($data) {
                return $data->lose_factor ?? '-';
            })
            ->addColumn('kilometer', function ($data) {
                return $data->kilometer ? number_format($data->kilometer, 0, ',', '.') : '-';
            })
            ->addColumn('loadsheet', function ($data) {
                return $data->loadsheet ? number_format($data->loadsheet, 0, ',', '.') : '-';
            })
            ->addColumn('perload', function ($data) {
                return $data->perload ? number_format($data->perload, 0, ',', '.') : '-';
            })
            ->addColumn('cubication', function ($data) {
                return $data->cubication ? number_format($data->cubication, 0, ',', '.') : '-';
            })
            ->addColumn('price', function ($data) {
                $calculation_method = $data->management_project->calculation_method;

                if (isset($calculation_method) && $calculation_method == 'Tonase') {
                    $exchange_rate = Currency::where('code', 'USD')->first()->exchange;
                    $result = $data->loadsheet * $data->kilometer * (0.117 * $exchange_rate);
                    return number_format($result, 2, ',', '.');
                } else if (isset($calculation_method) && $calculation_method == 'Kubic') {
                    return number_format($data->cubication * $data->soilType->value, 2, ',', '.');
                }
            })
            ->addColumn('billing_status', function ($data) {
                return $data->billing_status ?? '-';
            })  
            ->addColumn('remarks', function ($data) {
                return $data->remarks ?? '-';
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('loadsheet-edit')) {
                    # code...
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                    }
                }
                if (auth()->user()->hasPermissionTo('loadsheet-delete')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                    }
                }
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('loadsheetDashboard', function ($data) {
                $loadsheet = $data->loadsheet ?? null;
                if (session('selected_project_id')) {
                    $selectedProjectId = Crypt::decrypt(session('selected_project_id'));
                    if ($data->management_project_id == $selectedProjectId) {
                        return $loadsheet;
                    }
                }
                return $loadsheet;
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
            'date',
            'location',
            'soil_type_id',
            'bpit',
            'kilometer',
            'loadsheet',
            'perload',
            'cubication',
            'price',
            'billing_status',
            'remarks',
            'lose_factor',
        ];

        $keyword = $request->search['value'] ?? '';
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

        $data = Loadsheet::orderBy('id', 'desc')
            ->select($columns)
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
        return view('main.loadsheet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                $data['asset_id'] = Crypt::decrypt($data['asset_id']);
                $data['employee_id'] = Crypt::decrypt($data['employee_id']);
                $data['soil_type_id'] = isset($data['soil_type_id']) ? Crypt::decrypt($data['soil_type_id']) : null;

                $data['hours'] = isset($data['hours']) && $data['hours'] != '-' ? str_replace('.', '', $data['hours']) : null;
                $data['kilometer'] = isset($data['kilometer']) && $data['kilometer'] != '-' ? str_replace('.', '', $data['kilometer']) : null;
                // $data['loadsheet'] = isset($data['loadsheet']) && $data['loadsheet'] != '-' ? str_replace('.', '', $data['loadsheet']) : null;
                $data['loadsheet'] = isset($data['loadsheet']) && $data['loadsheet'] != '-' 
                                    ? (strpos($data['loadsheet'], ',') !== false 
                                        ? str_replace(',', '.', $data['loadsheet']) 
                                        : str_replace('.', '', $data['loadsheet'])) 
                                    : null;

                $data['perload'] = isset($data['perload']) && $data['perload'] != '-' ? str_replace('.', '', $data['perload']) : null;

                $data['lose_factor'] = (float)str_replace(',', '.', $data['lose_factor']);
                $data['cubication'] = ($data['loadsheet'] * $data['perload']) * $data['lose_factor'];
                // $data['cubication'] = ($data['loadsheet'] * $data['perload']) * $data['factor_lose'];

                $soilType = SoilType::find($data['soil_type_id']);
                $data['price'] = (int)($data['cubication'] * $soilType->value);

                $data = Loadsheet::create($data);

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
    public function edit($id)
    {
        $data = Loadsheet::findByEncryptedId($id);

        return view('main.loadsheet.edit', compact('data'));
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
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data['management_project_id'] = $data['management_project_id'];
                }
                try {
                    $data['asset_id'] = Crypt::decrypt($data['asset_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data['asset_id'] = $data['asset_id'];
                }

                try {
                    $data['employee_id'] = Crypt::decrypt($data['employee_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data['employee_id'] = $data['employee_id'];
                }

                if (isset($data['soil_type_id'])) {
                    try {
                        $data['soil_type_id'] = Crypt::decrypt($data['soil_type_id']);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $data['soil_type_id'] = $data['soil_type_id'];
                    }
                }

                $data['hours'] = isset($data['hours']) && $data['hours'] != '-' ? str_replace('.', '', $data['hours']) : null;
                $data['kilometer'] = isset($data['kilometer']) && $data['kilometer'] != '-' ? str_replace('.', '', $data['kilometer']) : null;
                $data['loadsheet'] = isset($data['loadsheet']) && $data['loadsheet'] != '-' ? str_replace('.', '', $data['loadsheet']) : null;
                $data['perload'] = isset($data['perload']) && $data['perload'] != '-' ? str_replace('.', '', $data['perload']) : null;
                $data['price'] = isset($data['price']) && $data['price'] != '-' ? str_replace('.', '', $data['price']) : null;

                $data['lose_factor'] = (float)str_replace(',', '.', $data['lose_factor']);
                $data['cubication'] = ($data['loadsheet'] * $data['perload']) * $data['lose_factor'];

                $soilType = SoilType::find($data['soil_type_id']);
                $data['price'] = (int)($data['cubication'] * $soilType->value);

                $data = Loadsheet::findByEncryptedId($id)->update($data);

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
            $data = Loadsheet::findByEncryptedId($id);
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

                $delete = Loadsheet::whereIn('id', $decryptedIds)->delete();

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
        return view('main.loadsheet.import');
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
            Excel::import(new ImportLoadsheet, $file);

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
        $fileName = 'Loadsheet' . now()->format('Ymd_His') . '.xlsx';
        $data = Loadsheet::all();

        return Excel::download(new LoadsheetExport($data), $fileName);
    }

    public function sumTotalLoadsheet(Request $request)
    {
        $data = Loadsheet::query();

        if (session('selected_project_id')) {
            $data->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $total = number_format($data->sum('loadsheet'));

        return response()->json([
            'status' => true,
            'data' => $total,
        ]);
    }

    public function productivityByHours(Request $request)
    {
        $data = Loadsheet::query();

        if (session('selected_project_id')) {
            $data->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $total = number_format($data->sum('hours'));

        return response()->json([
            'status' => true,
            'data' => $total,
        ]);
    }
}
