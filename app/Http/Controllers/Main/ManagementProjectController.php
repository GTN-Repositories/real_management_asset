<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\ManagementProject;
use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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
                $btn .= '<a href="javascript:void(0);" class="btn btn-info btn-sm me-1" title="Detail Data" onclick="detailData(\'' . $data->id . '\')"><i class="ti ti-eye"></i></a>';
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

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $data->whereBetween('date', [$request->startDate, $request->endDate]);
        }
        return $data;
    }

    public function getAssetsByProject(Request $request)
    {
        try {
            $projectId = Crypt::decrypt($request->projectId);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $projectId = $request->projectId;
        }

        $project = ManagementProject::find($projectId);

        if (!$project) {
            return response()->json([], 404);
        }

        $assetIds = $project->asset_id;
        if (!is_array($assetIds)) {
            return response()->json([], 200);
        }

        $assets = Asset::whereIn('id', $assetIds)->get()->mapWithKeys(function ($asset) {
            return [$asset->id => $asset->license_plate . ' - ' . $asset->name . ' - ' . $asset->asset_number];
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

                $data['value_project'] = isset($data['value_project']) && $data['value_project'] != '-' ? str_replace('.', '', $data['value_project']) : null;

                $start_date = trim($startDate);
                $end_date = trim($endDate);

                $decryptedAssetIds = [];
                foreach ($data['asset_id'] as $encryptedAssetId) {
                    $decryptedAssetIds[] = Crypt::decrypt($encryptedAssetId);
                }

                $decryptedEmployeeIds = [];
                foreach ($data['employee_id'] as $encryptedEmployeeId) {
                    $decryptedEmployeeIds[] = Crypt::decrypt($encryptedEmployeeId);
                }

                $projectData = [
                    'name' => $data['name'],
                    'asset_id' => $decryptedAssetIds,
                    'employee_id' => json_encode($decryptedEmployeeIds),
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'calculation_method' => $data['calculation_method'],
                    'value_project' => $data['value_project'],
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
        $data = ManagementProject::findByEncryptedId($id);
        if (is_array($data->asset_id) && count($data->asset_id) > 0) {
            $assetNames = Asset::whereIn('id', $data->asset_id)->pluck('name')->toArray();
            $assetNumbers = Asset::whereIn('id', $data->asset_id)->pluck('asset_number')->toArray();
            $data['asset'] = implode(', ', array_slice($assetNames, 0, 2)) . (count($assetNames) > 2 ? ', ...' : '') . ' - ' . implode(', ', array_slice($assetNumbers, 0, 2)) . (count($assetNumbers) > 2 ? ', ...' : '');
        } else {
            $data['asset'] = '';
        }

        $petty_cash = PettyCash::where('project_id', Crypt::decrypt($data->id))->get();

        return view('main.management_project.detail', compact('data', 'petty_cash'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = ManagementProject::findByEncryptedId($id);

        $data->assets = $data->getAssetsAttribute();

        $data->employee_id = is_string($data->employee_id)
            ? json_decode($data->employee_id, true)
            : ($data->employee_id ?? []);

        $data->employees = Employee::whereIn('id', $data->employee_id)->get();

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
                $data['value_project'] = isset($data['value_project']) && $data['value_project'] != '-' ? str_replace('.', '', $data['value_project']) : null;

                $decryptedAssetIds = [];

                foreach ($data['asset_id'] as $assetId) {
                    try {
                        $decryptedAssetIds[] = (int) Crypt::decrypt($assetId);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $decryptedAssetIds[] = (int) $assetId;
                    }
                }

                $decryptedEmployeeIds = [];

                foreach ($data['employee_id'] as $employeeId) {
                    try {
                        $decryptedEmployeeIds[] = (int) Crypt::decrypt($employeeId);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $decryptedEmployeeIds[] = (int) $employeeId;
                    }
                }

                $project = ManagementProject::findByEncryptedId($id);
                $previousAssetIds = $project->asset_id;

                $assetsToIdle = array_diff($previousAssetIds, $decryptedAssetIds);

                $assetsToActivate = array_diff($decryptedAssetIds, $previousAssetIds);

                $projectData = [
                    'name' => $data['name'],
                    'asset_id' => $decryptedAssetIds,
                    'employee_id' => $decryptedEmployeeIds,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'calculation_method' => $data['calculation_method'],
                    'value_project' => $data['value_project'],
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

    public function todoRequestPettyCash()
    {
        $petty_cash = PettyCash::whereIn('status', [1, 3])->get();

        return view('main.management_project.request_petty_cash', compact('petty_cash'));
    }

    public function requestPettyCash(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $data['created_by'] = Auth::user()->id;
                $data['project_id'] = Crypt::decrypt($data['project_id']);
                $data['status'] = 1;

                $create = PettyCash::create($data);

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


    public function approvePettyCash(Request $request, $id)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $id) {
                $data['approved_by'] = Auth::user()->id;
                $data['status'] = $data['status'];

                $create = PettyCash::findByEncryptedId($id);


                if ($data['status'] == 2 && ($create->status == 1 || $create->status == 3)) {
                    $project = ManagementProject::find($create->project_id);
                    $project->petty_cash = $project->petty_cash + $create->amount;
                    $project->save();
                }

                if ($data['status'] == 3 && $create->status == 2) {
                    $project = ManagementProject::find($create->project_id);
                    $project->petty_cash = $project->petty_cash - $create->amount;
                    $project->save();
                }

                $create->update($data);

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
}
