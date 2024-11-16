<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use App\Models\ManagementProject;
use App\Models\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('main.monitoring.index');
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
            ->addColumn('asset_id', function ($data) {
                return $data->asset->name ?? null;
            })
            ->addColumn('longitude', function ($data) {
                return $data->longitude ?? null;
            })
            ->addColumn('latitude', function ($data) {
                return $data->latitude ?? null;
            })
            ->addColumn('fuel_status', function ($data) {
                $latestFuelConsumption = FuelConsumption::where('asset_id', $data->asset_id)->latest()->first();
                return number_format($latestFuelConsumption->liter ?? 0, 0, ',', '.') . ' liter' ?? null;
            })
            ->addColumn('asset_status', function ($data) {
                return $data->asset->status ?? null;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('monitoring-edit')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('monitoring-delete')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                }
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('literDashboard', function ($data) {
                return $data->liter ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'longitude',
            'latitude',
        ];

        $keyword = $request->search['value'] ?? "";
        // $project_id = $this->projectId();

        $data = Monitoring::orderBy('created_at', 'asc')
            ->select($columns)
            // ->whereIn($project_id)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if (session('selected_project_id')) {
            $managementProject = ManagementProject::find(Crypt::decrypt(session('selected_project_id')));

            if ($managementProject) {
                $assetIds = $managementProject->asset_id;
                $data->whereIn('asset_id', $assetIds);
            }
        }

        return $data;
    }


    public function create()
    {
        return view('main.monitoring.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data["asset_id"] = crypt::decrypt($data["asset_id"]);
                $data = Monitoring::create($data);

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
        $data = Monitoring::findByEncryptedId($id);

        return view('main.monitoring.edit', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $data["asset_id"] = crypt::decrypt($data["asset_id"]);
                $data = Monitoring::findByEncryptedId($id)->update($data);

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
            $data = Monitoring::findByEncryptedId($id);
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

                $delete = Monitoring::whereIn('id', $decryptedIds)->delete();

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
