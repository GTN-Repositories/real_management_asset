<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ManagementProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ManagementProjectController extends Controller
{
    public function index()
    {
        return view('main.management_project.index');
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
            ->addColumn('managementRelationId', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('asset_id', function ($data) {
                if (is_array($data->asset_id) && count($data->asset_id) > 0) {
                    $assetNames = Asset::whereIn('id', $data->asset_id)->pluck('name')->toArray();
                    $assetNumbers = Asset::whereIn('id', $data->asset_id)->pluck('asset_number')->toArray();
                    return implode(', ', array_slice($assetNames, 0, 2)) . (count($assetNames) > 2 ? ', ...' : '') . ' - ' . implode(', ', array_slice($assetNumbers, 0, 2)) . (count($assetNumbers) > 2 ? ', ...' : '');
                }
                return null;
            })
            ->addColumn('start_date', function ($data) {
                return $data->start_date ?? null;
            })
            ->addColumn('end_date', function ($data) {
                return $data->end_date ?? null;
            })
            ->addColumn('calculation_method', function ($data) {
                return $data->calculation_method ?? null;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('management-edit')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('management-delete')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                }
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
            'name',
            'asset_id',
            'start_date',
            'end_date',
            'calculation_method',
        ];

        $keyword = $request->search['value'] ?? "";

        $data = ManagementProject::orderBy('created_at', 'asc')
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

    public function getAssetsByProject(Request $request)
    {
        $projectId = Crypt::decrypt($request->projectId);

        $project = ManagementProject::find($projectId);

        if (!$project) {
            return response()->json([], 404);
        }

        $assetIds = $project->asset_id;
        if (!is_array($assetIds)) {
            return response()->json([], 200);
        }

        $assets = Asset::whereIn('id', $assetIds)->get()->mapWithKeys(function ($asset) {
            return [$asset->id => $asset->name . ' - ' . $asset->asset_number];
        })->toArray();

        return response()->json($assets);
    }

    public function create()
    {
        return view('main.management_project.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $dateRangeInput = $data['date_range'];
                [$startDate, $endDate] = explode(' - ', $dateRangeInput);

                $start_date = trim($startDate);
                $end_date = trim($endDate);
                $decryptedAssetIds = [];
                foreach ($data['asset_id'] as $encryptedAssetId) {
                    $decryptedAssetIds[] = Crypt::decrypt($encryptedAssetId);
                }
                $projectData = [
                    'name' => $data['name'],
                    'asset_id' => $decryptedAssetIds,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'calculation_method' => $data['calculation_method'],
                ];
                ManagementProject::create($projectData);
                Asset::whereIn('id', $decryptedAssetIds)->update(['status' => 'Active']);

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
        $data = ManagementProject::findByEncryptedId($id);
        $data->assets = $data->getAssetsAttribute();

        return view('main.management_project.edit', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $decryptedAssetIds = [];

                foreach ($data['asset_id'] as $assetId) {
                    try {
                        $decryptedAssetIds[] = (int) Crypt::decrypt($assetId);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $decryptedAssetIds[] = (int) $assetId;
                    }
                }

                $project = ManagementProject::findByEncryptedId($id);
                $previousAssetIds = $project->asset_id;

                $assetsToIdle = array_diff($previousAssetIds, $decryptedAssetIds);

                $assetsToActivate = array_diff($decryptedAssetIds, $previousAssetIds);

                $projectData = [
                    'name' => $data['name'],
                    'asset_id' => $decryptedAssetIds,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'calculation_method' => $data['calculation_method'],
                ];
                $project->update($projectData);

                Asset::whereIn('id', $assetsToIdle)->update(['status' => 'Idle']);

                Asset::whereIn('id', $assetsToActivate)->update(['status' => 'Active']);

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
            $data = ManagementProject::findByEncryptedId($id);

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

                $delete = ManagementProject::whereIn('id', $decryptedIds)->delete();

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
