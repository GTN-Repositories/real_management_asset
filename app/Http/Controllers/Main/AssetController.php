<?php

namespace App\Http\Controllers\Main;

use App\Exports\AssetExport;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetNote;
use App\Models\CostumField;
use App\Models\LogActivity;
use App\Models\ManagementProject;
use App\Models\RecordInsurance;
use App\Models\RecordRent;
use App\Models\RecordTax;
use App\Models\StatusAsset;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AssetController extends Controller
{
    public function index()
    {
        return view('main.unit.index');
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
            ->addColumn('noDecryptId', function ($data) {
                return 'AST - ' . Crypt::decrypt($data->id) ?? null;
            })
            ->addColumn('relationId', function ($data) {
                return Crypt::decrypt($data->id) ?? null;
            })
            ->addColumn('image', function ($data) {
                return $data->image ? '<img src="' . asset('storage/' . $data->image) . '" alt="Image" width="50" height="50"/>' : "-";
            })
            ->addColumn('nameWithNumber', function ($data) {
                return Crypt::decrypt($data->id) . '-' . $data->name . " - " . $data->license_plate ?? "-";
            })
            ->addColumn('management_project', function ($data) {
                if ($data->management_project_id) {
                    return 'PRJ - '. $data->management_project_id .' '. ($data->management_project->name ?? '-');
                } else {
                    return '-';
                }
            })
            ->addColumn('category', function ($data) {
                return $data->category;
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('unit', function ($data) {
                return $data->unit;
            })
            ->addColumn('type', function ($data) {
                return $data->type;
            })
            ->addColumn('license_plate', function ($data) {
                return $data->license_plate;
            })
            ->addColumn('classification', function ($data) {
                return $data->classification;
            })
            ->addColumn('serial_number', function ($data) {
                return $data->serial_number;
            })
            ->addColumn('chassis_number', function ($data) {
                return $data->chassis_number;
            })
            ->addColumn('machine_number', function ($data) {
                return $data->machine_number;
            })
            ->addColumn('nik', function ($data) {
                return $data->nik;
            })
            ->addColumn('color', function ($data) {
                return $data->color;
            })
            ->addColumn('owner', function ($data) {
                return $data->manager;
            })
            ->addColumn('assets_location', function ($data) {
                return $data->assets_location;
            })
            ->addColumn('pic', function ($data) {
                return $data->pics->name ?? $data->pic;
            })
            ->addColumn('status', function ($data) {
                return $data->status;
            })
            ->addColumn('cost', function ($data) {
                return $data->cost;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('asset-show')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-info btn-sm me-1" title="Detail Data" onclick="detailData(\'' . $data->id . '\')"><i class="ti ti-eye"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('asset-edit')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('asset-delete')) {
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
            'asset_number',
            'image',
            'name',
            'category',
            'manager',
            'unit',
            'type',
            'license_plate',
            'classification',
            'chassis_number',
            'machine_number',
            'nik',
            'color',
            'owner',
            'cost',
            'assets_location',
            'pic',
            'status',
            'serial_number',
            'created_at',
            'management_project_id',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = Asset::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    $query->where(function ($q) use ($keyword, $columns) {
                        foreach ($columns as $column) {
                            $q->orWhere($column, 'LIKE', '%' . $keyword . '%');
                        }
                    });
                }
            });

        if ($request->status) {
            $status = $request->status;
            $data->whereIn('status', $status);
        }

        if ($request->assets_location) {
            $assets_location = $request->assets_location;
            $data->whereIn('assets_location', $assets_location);
        }

        if ($request->category) {
            $category = $request->category;
            $data->whereIn('category', $category);
        }

        if ($request->pic) {
            $pic = $request->pic;
            $data->whereIn('manager', $pic);
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
        return view('main.unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $lastAsset = Asset::latest('id')->first();
                $lastNumber = $lastAsset ? intval(str_replace('ast-', '', $lastAsset->asset_number)) : 0;

                // if (isset($data['assets_location'])) {
                //     $data['assets_location'] = Crypt::decrypt($data['assets_location']);
                // }
                // if (isset($data['manager'])) {
                //     $data['manager'] = Crypt::decrypt($data['manager']);
                // }
                // if (isset($data['category'])) {
                //     $data['category'] = Crypt::decrypt($data['category']);
                // }

                if (isset($data['pic'])) {
                    $data['pic'] = Crypt::decrypt($data['pic']);
                }

                if (isset($data['management_project_id'])) {
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                }

                $data['status'] = "Idle";
                $data['ast_id'] = 'ast-' . ($lastNumber + 1);

                if (isset($data['image'])) {
                    $data['image'] = $data['image']->store('assets', 'public');
                }
                if (isset($data['file_reminder'])) {
                    $data['file_reminder'] = $data['file_reminder']->store('assets', 'public');
                }
                if (isset($data['asuransi'])) {
                    $data['asuransi'] = $data['asuransi']->store('assets', 'public');
                }
                if (isset($data['file_tax'])) {
                    $data['file_tax'] = $data['file_tax']->store('assets', 'public');
                }

                $data['manager'] = $data['manager'];

                $asset = Asset::create($data);

                if (isset($data['management_project_id'])) {
                    $project = ManagementProject::find($data['management_project_id']);
                    $assignAsset = true;
                    foreach ($project->asset_id as $key => $value) {
                        if ($value == $asset->id) {
                            $assignAsset = false;
                            break;
                        }
                    }

                    if ($assignAsset) {
                        $asset_id_project = $project->asset_id;
                        $asset_id_project[] = Crypt::decrypt($asset->id);
                        $project->asset_id = $asset_id_project;
                        $project->save();
                    }
                }

                $customFieldNames = $data['custom_field_name'] ?? [];
                $customFieldValues = $data['custom_field_value'] ?? [];
                $customFieldTypes = $data['custom_field_type'] ?? [];

                foreach ($customFieldNames as $index => $customFieldName) {
                    $customFieldValue = $customFieldValues[$index] ?? null;
                    $customFieldType = $customFieldTypes[$index] ?? null;

                    if ($customFieldName !== null && $customFieldValue !== null && $customFieldType !== null) {
                        CostumField::create([
                            'asset_id' => Crypt::decrypt($asset->id),
                            'nama_field' => $customFieldName,
                            'nilai_field' => $customFieldValue,
                            'tipe_field' => $customFieldType,
                        ]);
                    }
                }


                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            dd($th);
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
        $asset = Asset::findByEncryptedId($id);
        $decryptedId = Crypt::decrypt($asset->id);

        $projects = ManagementProject::whereJsonContains('asset_id', $decryptedId)->get();
        $notes = AssetNote::where('asset_id', $decryptedId)->get();
        $logs = LogActivity::where('asset_id', $decryptedId)->get();

        $path = public_path('storage/qr_codes/');

        $encryptedId = Crypt::encrypt($decryptedId);
        $qrCodeFile = $path . $encryptedId . '.png';

        if (!file_exists($qrCodeFile)) {
            $qrCode = new QrCode(route('asset.show', $asset->id));
            $writer = new PngWriter();

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $result = $writer->write($qrCode);
            $result->saveToFile($qrCodeFile);
        }

        return view('main.unit.show', compact('asset', 'projects', 'notes', 'logs', 'encryptedId'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Asset::findByEncryptedId($id);
        $customFields = CostumField::where('asset_id', Crypt::decrypt($data->id))->get();
        $managementProject = ManagementProject::find($data->management_project_id);
        $data->management_project_ids = $data->management_project_id;
        $data->management_project_name = $managementProject->name ?? null;

        return view('main.unit.edit', compact('data', 'customFields'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $asset = Asset::findByEncryptedId($id);
                $statusBefore = $asset->status;

                if (!isset($data['image']) || !$data['image']) {
                    $data['image'] = $asset->image;
                } else {
                    if ($asset->image && Storage::disk('public')->exists($asset->image)) {
                        Storage::disk('public')->delete($asset->image);
                        $data['image'] = $data['image']->store('assets', 'public');
                    } else {
                        $data['image'] = $data['image']->store('assets', 'public');
                    }
                }

                if (!isset($data['stnk']) || !$data['stnk']) {
                    $data['stnk'] = $asset->stnk;
                } else {
                    if ($asset->stnk && Storage::disk('public')->exists($asset->stnk)) {
                        Storage::disk('public')->delete($asset->stnk);
                        $data['stnk'] = $data['stnk']->store('assets', 'public');
                    } else {
                        $data['stnk'] = $data['stnk']->store('assets', 'public');
                    }
                }

                if (isset($data['management_project_id']) && $data['management_project_id'] != $asset->management_project_id) {
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);

                    $project = ManagementProject::find($data['management_project_id']);
                    $assignAsset = true;
                    foreach ($project->asset_id as $key => $value) {
                        if ($value == $asset->id) {
                            $assignAsset = false;
                            break;
                        }
                    }

                    if ($assignAsset) {
                        $asset_id_project = $project->asset_id;
                        $asset_id_project[] = Crypt::decrypt($asset->id);
                        $project->asset_id = $asset_id_project;
                        $project->save();
                    }

                    // REMOVE FROM OLD PROJECT
                    $oldProject = ManagementProject::find($asset->management_project_id);
                    $asset_id_project = $oldProject->asset_id;
                    $asset_id_project = array_diff($asset_id_project, [$asset->id]);
                    $oldProject->asset_id = $asset_id_project;
                    $oldProject->save();
                }

                if (!isset($data['asuransi']) || !$data['asuransi']) {
                    $data['asuransi'] = $asset->asuransi;
                } else {
                    if ($asset->asuransi && Storage::disk('public')->exists($asset->asuransi)) {
                        Storage::disk('public')->delete($asset->asuransi);
                        $data['asuransi'] = $data['asuransi']->store('assets', 'public');
                    } else {
                        $data['asuransi'] = $data['asuransi']->store('assets', 'public');
                    }
                }

                if (!isset($data['file_tax']) || !$data['file_tax']) {
                    $data['file_tax'] = $asset->file_tax;
                } else {
                    if ($asset->file_tax && Storage::disk('public')->exists($asset->file_tax)) {
                        Storage::disk('public')->delete($asset->file_tax);
                        $data['file_tax'] = $data['file_tax']->store('assets', 'public');
                    } else {
                        $data['file_tax'] = $data['file_tax']->store('assets', 'public');
                    }
                }

                if (isset($data['pic'])) {
                    try {
                        $data['pic'] = Crypt::decrypt($data['pic']);
                    } catch (\Exception $e) {
                        $data['pic'] = $data['pic'];
                    }
                }

                $data['manager'] = $data['manager'] ?? null;

                $result = $asset->update($data);

                $latestRecord = RecordInsurance::where('asset_id', Crypt::decrypt($asset->id))->latest()->first();
                $latestDate = $latestRecord ? $latestRecord->date : null;

                if ($latestDate !== $data['asuransi_date'] && $data['insurance_cost'] !== null) {
                    $distance = 0;
                    if ($latestDate) {
                        $distance = (int) date_diff(date_create($data['asuransi_date']), date_create($latestDate))->format('%m');
                    }

                    $summary = $distance > 0 ? $distance * $data['insurance_cost'] : $data['insurance_cost'];

                    RecordInsurance::create([
                        'asset_id' => Crypt::decrypt($asset->id),
                        'summary' => $summary,
                        'insurance' => $data['insurance_cost'],
                        'date' => $data['asuransi_date'],
                    ]);
                }

                $latestRecord = RecordTax::where('asset_id', Crypt::decrypt($asset->id))->latest()->first();
                $latestDate = $latestRecord ? $latestRecord->date : null;

                if ($latestDate !== $data['tax_period'] && $data['tax_cost'] !== null) {
                    $distance = 0;
                    if ($latestDate) {
                        $distance = (int) date_diff(date_create($data['tax_period']), date_create($latestDate))->format('%m');
                    }
                    $summary = $distance > 0 ? $distance * $data['tax_cost'] : $data['tax_cost'];

                    RecordTax::create([
                        'asset_id' => Crypt::decrypt($asset->id),
                        'summary' => $summary,
                        'tax' => $data['tax_cost'],
                        'date' => $data['tax_period'],
                    ]);
                }

                $latestRecord = RecordRent::where('asset_id', Crypt::decrypt($asset->id))->latest()->first();
                $latestDate = $latestRecord ? $latestRecord->date : null;

                if ($latestDate !== $data['contract_period'] && $data['cost'] !== null) {
                    $distance = 0;
                    if ($latestDate) {
                        $distance = (int) date_diff(date_create($data['contract_period']), date_create($latestDate))->format('%m');
                    }
                    $summary = $distance > 0 ? $distance * $data['cost'] : $data['cost'];

                    RecordRent::create([
                        'asset_id' => Crypt::decrypt($asset->id),
                        'summary' => $summary,
                        'rent' => $data['cost'],
                        'date' => $data['contract_period'],
                    ]);
                }

                if ($statusBefore !== $asset->status) {
                    StatusAsset::create([
                        'asset_id' => Crypt::decrypt($asset->id),
                        'status_before' => $statusBefore,
                        'status_after' => $asset->status,
                    ]);
                }

                $customFieldNames = $data['custom_field_name'] ?? [];
                $customFieldValues = $data['custom_field_value'] ?? [];
                $customFieldTypes = $data['custom_field_type'] ?? [];

                $costumFieldsBefore = CostumField::where('asset_id', Crypt::decrypt($asset->id))->get();
                $costumFieldsBeforeIds = $costumFieldsBefore->map(function ($field) {
                    return Crypt::decrypt($field->id);
                })->toArray();
                foreach ($customFieldNames as $index => $customFieldName) {
                    $customFieldValue = $customFieldValues[$index] ?? null;
                    $customFieldType = $customFieldTypes[$index] ?? null;

                    if ($customFieldName !== null && $customFieldValue !== null && $customFieldType !== null) {
                        if (isset($costumFieldsBeforeIds[$index])) {
                            CostumField::where('id', $costumFieldsBeforeIds[$index])->update([
                                'nama_field' => $customFieldName,
                                'nilai_field' => $customFieldValue,
                                'tipe_field' => $customFieldType,
                            ]);
                            unset($costumFieldsBeforeIds[$index]);
                        } else {
                            CostumField::create([
                                'asset_id' => Crypt::decrypt($asset->id),
                                'nama_field' => $customFieldName,
                                'nilai_field' => $customFieldValue,
                                'tipe_field' => $customFieldType,
                            ]);
                        }
                    }
                }

                CostumField::whereIn('id', $costumFieldsBeforeIds)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui!',
                ]);
            });
        } catch (\Throwable $th) {
            dd($th);
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
            $data = Asset::findByEncryptedId($id);
            if ($data->image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
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

                foreach ($decryptedIds as $id) {
                    $asset = Asset::findOrFail($id);
                    if ($asset->image && Storage::disk('public')->exists($asset->image)) {
                        Storage::disk('public')->delete($asset->image);
                    }
                }

                $delete = Asset::whereIn('id', $decryptedIds)->delete();

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

    public function getStatusData()
    {
        try {
            // Get base query
            $baseQuery = Asset::query();

            // Add project filter
            if (session('selected_project_id')) {
                $managementProject = ManagementProject::find(Crypt::decrypt(session('selected_project_id')));

                if ($managementProject) {
                    $assetIds = $managementProject->asset_id;
                    $baseQuery->whereIn('id', $assetIds);
                }
            }

            // Clone queries for each count to avoid interference
            $operationalStatus = (clone $baseQuery)->select(
                DB::raw('COUNT(CASE WHEN status = "Idle" THEN 1 END) as idle'),
                DB::raw('COUNT(CASE WHEN status = "StandBy" THEN 1 END) as standby'),
                DB::raw('COUNT(CASE WHEN status = "UnderMaintenance" THEN 1 END) as underMaintenance'),
                DB::raw('COUNT(CASE WHEN status = "Active" THEN 1 END) as active'),
                DB::raw('COUNT(CASE WHEN status = "Finish" THEN 1 END) as finish')
            )->first();

            $active = $operationalStatus->active + $operationalStatus->finish;

            $maintenanceStatus = (clone $baseQuery)->select(
                DB::raw('COUNT(CASE WHEN status = "OnHold" THEN 1 END) as onHold'),
                DB::raw('COUNT(CASE WHEN status = "Finish" THEN 1 END) as finish'),
                DB::raw('COUNT(CASE WHEN status = "Scheduled" THEN 1 END) as scheduled'),
                DB::raw('COUNT(CASE WHEN status = "InProgress" THEN 1 END) as inProgress')
            )->first();

            $assetStatus = (clone $baseQuery)->select(
                DB::raw('COUNT(CASE WHEN status = "Damaged" THEN 1 END) as damaged'),
                DB::raw('COUNT(CASE WHEN status = "Fair" THEN 1 END) as fair'),
                DB::raw('COUNT(CASE WHEN status = "NeedsRepair" THEN 1 END) as needsRepair'),
                DB::raw('COUNT(CASE WHEN status = "Good" THEN 1 END) as good')
            )->first();

            $totalAssets = $baseQuery->count();

            // Rest of the response building code remains the same...

            $response = [
                // Operational Status
                'idle' => (int) $operationalStatus->idle,
                'standby' => (int) $operationalStatus->standby,
                'underMaintenance' => (int) $operationalStatus->underMaintenance,
                'active' => $active,

                // Maintenance Status
                'onHold' => (int) $maintenanceStatus->onHold,
                'finish' => (int) $maintenanceStatus->finish,
                'scheduled' => (int) $maintenanceStatus->scheduled,
                'inProgress' => (int) $maintenanceStatus->inProgress,

                // Asset Condition Status
                'damaged' => (int) $assetStatus->damaged,
                'fair' => (int) $assetStatus->fair,
                'needsRepair' => (int) $assetStatus->needsRepair,
                'good' => (int) $assetStatus->good,

                // Additional Statistics
                'total_assets' => $totalAssets,
                'percentages' => [
                    'operational' => [
                        'idle' => $totalAssets > 0 ? round(($operationalStatus->idle / $totalAssets) * 100, 1) : 0,
                        'standby' => $totalAssets > 0 ? round(($operationalStatus->standby / $totalAssets) * 100, 1) : 0,
                        'underMaintenance' => $totalAssets > 0 ? round(($operationalStatus->underMaintenance / $totalAssets) * 100, 1) : 0,
                        'active' => $totalAssets > 0 ? round(($active / $totalAssets) * 100, 1) : 0
                    ],
                    'maintenance' => [
                        'onHold' => $totalAssets > 0 ? round(($maintenanceStatus->onHold / $totalAssets) * 100, 1) : 0,
                        'finish' => $totalAssets > 0 ? round(($maintenanceStatus->finish / $totalAssets) * 100, 1) : 0,
                        'scheduled' => $totalAssets > 0 ? round(($maintenanceStatus->scheduled / $totalAssets) * 100, 1) : 0,
                        'inProgress' => $totalAssets > 0 ? round(($maintenanceStatus->inProgress / $totalAssets) * 100, 1) : 0
                    ],
                    'condition' => [
                        'damaged' => $totalAssets > 0 ? round(($assetStatus->damaged / $totalAssets) * 100, 1) : 0,
                        'fair' => $totalAssets > 0 ? round(($assetStatus->fair / $totalAssets) * 100, 1) : 0,
                        'needsRepair' => $totalAssets > 0 ? round(($assetStatus->needsRepair / $totalAssets) * 100, 1) : 0,
                        'good' => $totalAssets > 0 ? round(($assetStatus->good / $totalAssets) * 100, 1) : 0
                    ]
                ],
                'timestamp' => now()->toDateTimeString()
            ];

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error retrieving status data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAppreciationData(Request $request)
    {
        $assetId = $request->query('asset_id');

        $assetsQuery = Asset::select('purchase_date', 'cost', 'appreciation_rate', 'appreciation_period');

        if ($assetId) {
            $assetsQuery->where('id', $assetId);
        }

        $assets = $assetsQuery->get();

        $chartData = [];
        foreach ($assets as $asset) {
            $purchaseDate = \Carbon\Carbon::parse($asset->purchase_date);
            $data = [];
            $cost = $asset->cost;

            $rate = $asset->appreciation_rate;
            $period = $asset->appreciation_period;

            if ($rate !== null && $period !== null && $cost > 0) {
                for ($i = 0; $i <= $period; $i++) {
                    $value = $cost * pow(1 + ($rate / 100), $i);

                    if (is_finite($value)) {
                        $data[] = [
                            'date' => $purchaseDate->copy()->addMonths($i)->format('Y-m-d'),
                            'value' => $value
                        ];
                    }
                }
            }

            $chartData[] = [
                'label' => 'Asset on ' . $asset->purchase_date,
                'data' => $data
            ];
        }

        return response()->json($chartData);
    }

    public function getDepreciationData(Request $request)
    {
        $assetId = $request->query('asset_id');

        $assetsQuery = Asset::select('purchase_date', 'cost', 'residual_value', 'depreciation', 'depreciation_method');

        if ($assetId) {
            $assetsQuery->where('id', $assetId);
        }

        $assets = $assetsQuery->get();

        $chartData = [];
        foreach ($assets as $asset) {
            $purchaseDate = \Carbon\Carbon::parse($asset->purchase_date);
            $cost = $asset->cost;
            $residualValue = $asset->residual_value;
            $depreciationMonths = $asset->depreciation;
            $method = $asset->depreciation_method;
            $data = [];

            if ($depreciationMonths > 0 && $cost > $residualValue) {
                if ($method === 'Penyusutan Garis Lurus') {
                    $monthlyDepreciation = ($cost - $residualValue) / $depreciationMonths;
                    for ($i = 0; $i <= $depreciationMonths; $i++) {
                        $depreciatedValue = $cost - ($monthlyDepreciation * $i);
                        $data[] = [
                            'date' => $purchaseDate->copy()->addMonths($i)->format('Y-m-d'),
                            'value' => max($depreciatedValue, $residualValue)
                        ];
                    }
                } elseif ($method === 'Penyusutan Saldo Menurun') {
                    $rate = 1 - pow($residualValue / $cost, 1 / $depreciationMonths);
                    for ($i = 0; $i <= $depreciationMonths; $i++) {
                        $depreciatedValue = $cost * pow(1 - $rate, $i);
                        $data[] = [
                            'date' => $purchaseDate->copy()->addMonths($i)->format('Y-m-d'),
                            'value' => max($depreciatedValue, $residualValue)
                        ];
                    }
                }
            }

            $chartData[] = [
                'label' => 'Asset on ' . $asset->purchase_date,
                'data' => $data
            ];
        }

        return response()->json($chartData);
    }

    public function importForm()
    {
        return view('main.unit.import');
    }
    protected $statusMapping = [
        'Active' => 'Active',
        'Idle' => 'Idle',
        'StandBy' => 'StandBy',
        'Finish' => 'Finish',
        'Damaged' => 'Damaged',
        'Fair' => 'Fair',
        'UnderMaintenance' => 'UnderMaintenance',
        'Scheduled' => 'Scheduled',
        'InProgress' => 'InProgress',
        'NeedsRepair' => 'NeedsRepair',
        'Good' => 'Good',
        'OnHold' => 'OnHold'
    ];

    protected function mapStatus($status)
    {
        return $this->statusMapping[$status] ?? 'Idle';
    }

    public function import(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded!'
                ], 400);
            }

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = $worksheet->toArray();
            $startRow = 1;
            $failed = [];
            $success = 0;

            $headers = array_slice($rows[$startRow - 1], 1);

            for ($i = $startRow; $i < count($rows); $i++) {
                $row = array_slice($rows[$i], 0);

                if (empty(array_filter($row))) {
                    continue;
                }

                $data = [
                    'asset_number' => $row[1],
                    'category' => $row[2],
                    'name' => $row[3],
                    'unit' => $row[4],
                    'type' => $row[5],
                    'license_plate' => $row[6],
                    'classification' => $row[7],
                    'chassis_number' => $row[8],
                    'machine_number' => $row[9],
                    'nik' => $row[10],
                    'color' => $row[11],
                    'manager' => $row[12],
                    'assets_location' => $row[14],
                    'status' => $this->mapStatus($row[16]),
                ];

                try {
                    DB::beginTransaction();

                    Asset::create($data);

                    DB::commit();
                    $success++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $failed[] = [
                        'row' => $i + 1,
                        'errors' => [$e->getMessage()],
                        'data' => $data
                    ];
                }
            }

            $message = "Import completed. Successfully imported $success records.";
            if (count($failed) > 0) {
                $message .= " Failed to import " . count($failed) . " records.";
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'failed_rows' => $failed,
                'success_count' => $success
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header sesuai format yang diminta
        $headers = [
            'No',
            'No Aset',
            'Kategori',
            'Merek',
            'Unit',
            'Type',
            'No Polisi',
            'Klasifikasi',
            'No Rangka',
            'No Mesin',
            'NIK',
            'Warna',
            'Pemilik'
        ];

        $additionalHeaders = [
            'Project',
            'Lokasi',
            'PIC',
            'Status'
        ];

        // Menulis header di B4 hingga O4
        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 2, 4, $header); // Kolom B adalah index 2
        }

        // Menulis header tambahan dari AG4 hingga AJ4
        foreach ($additionalHeaders as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 32, 4, $header); // Kolom AG adalah index 33
        }

        // Menyembunyikan kolom P hingga AF
        for ($col = 15; $col <= 31; $col++) { // Kolom P = 16, AF = 32
            $sheet->getColumnDimensionByColumn($col)->setVisible(false);
        }

        // Menyimpan template ke dalam file Excel
        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path('app/public/asset_import_template.xlsx');
        $writer->save($filePath);

        return response()->download($filePath, 'asset_import_template.xlsx');
    }


    public function updateFiles(Request $request)
    {
        $data = Asset::findByEncryptedId($request->id);
        $data->kategori = $request->kategori;
        return view('main.unit.update-files', compact('data'));
    }

    public function note(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $data['asset_id'] = Crypt::decrypt($id);

                AssetNote::create($data);

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

    public function download(string $encryptedId)
    {
        $decryptedId = Crypt::decrypt($encryptedId);

        $path = public_path('storage/qr_codes/' . $encryptedId . '.png');

        if (file_exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    public function exportExcel(Request $request)
    {
        $data = Asset::all();

        $name = 'AssetReport';
        $name .= '_' . $request->startDate . '_to_' . $request->endDate;

        return Excel::download(new AssetExport($data), $name . '.xlsx');
    }
}
