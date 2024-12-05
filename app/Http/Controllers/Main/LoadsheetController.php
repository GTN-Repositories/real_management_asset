<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Loadsheet;
use App\Models\SoilType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
                return $data->management_project->name ?? '-';
            })
            ->addColumn('asset_id', function ($data) {
                return ($data->asset->name ?? '') . ' ' . ($data->asset->license_plate ?? '') ?? '-';
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? '-';
            })
            ->addColumn('location', function ($data) {
                return $data->location ?? '-';
            })
            ->addColumn('soil_type_id', function ($data) {
                return $data->soilType->name ?? '-';
            })
            ->addColumn('bpit', function ($data) {
                return $data->bpit ?? '-';
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
                return $data->price ? number_format($data->price, 0, ',', '.') : '-';
            })
            ->addColumn('billing_status', function ($data) {
                return $data->billing_status ?? '-';
            })
            ->addColumn('remarks', function ($data) {
                return $data->remarks ?? '-';
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
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
        ];

        $keyword = $request->keyword ?? '';

        $data = Loadsheet::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

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
                $data['loadsheet'] = isset($data['loadsheet']) && $data['loadsheet'] != '-' ? str_replace('.', '', $data['loadsheet']) : null;
                $data['perload'] = isset($data['perload']) && $data['perload'] != '-' ? str_replace('.', '', $data['perload']) : null;

                $data['lose_factor'] = 0.7;
                $data['cubication'] = ($data['loadsheet'] * $data['perload']) * $data['lose_factor'];

                $soilType = SoilType::find($data['soil_type_id']);
                $data['price'] = (int)($data['cubication'] * $soilType->value);

                $data = Loadsheet::create($data);

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

                $data['lose_factor'] = 0.7;
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
}
