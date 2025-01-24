<?php

namespace App\Http\Controllers\Main;

use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Imports\ImportEmployee;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    //
    public function index()
    {
        return view('main.employee.index');
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
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('management_project_id', function ($data) {
                return ($data->managementProject->name ?? null) ?? null;
            })
            ->addColumn('nameTitle', function ($data) {
                return ($data->name ?? null) . '-' . $data->jobTitle->name ?? null;
            })
            ->addColumn('job_title', function ($data) {
                return $data->jobTitle->name ?? null;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('employee-edit')) {
                    # code...
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                    }
                }
                if (auth()->user()->hasPermissionTo('employee-delete')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                    }
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
            'job_title_id',
            'management_project_id',
            'name',
        ];

        $keyword = $request->search['value'] ?? "";
        $job_title = $request->job_title_id ?? [];
        $jobTitleDecrypted = [];
        foreach ($job_title as $value) {
            $jobTitleDecrypted[] = Crypt::decrypt($value);
        }

        $management_project = $request->management_project_id ?? [];
        $management_project_decrypt = [];
        foreach ($management_project as $value) {
            try {
                //code...
                $management_project_decrypt[] = Crypt::decrypt($value);
            } catch (\Throwable $th) {
                //throw $th;
                $management_project_decrypt[] = $value;
            }
        }

        $data = Employee::orderBy('created_at', 'asc')
            ->select($columns)
            ->when($jobTitleDecrypted, function ($query) use ($jobTitleDecrypted) {
                if (count($jobTitleDecrypted) > 0) {
                    return $query->whereIn('job_title_id', $jobTitleDecrypted);
                }
            })
            ->when($management_project_decrypt, function ($query) use ($management_project_decrypt) {
                if (count($management_project_decrypt) > 0) {
                    return $query->whereIn('management_project_id', $management_project_decrypt);
                }
            })
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if (session('selected_project_id')) {
            $data->whereHas('managementProject', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        return $data;
    }


    public function create()
    {
        return view('main.employee.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data['job_title_id'] = Crypt::decrypt($data['job_title_id']);
                if (isset($data['management_project_id'])) {
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                }
                $data = Employee::create($data);

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
        $data = Employee::findByEncryptedId($id);

        return view('main.employee.edit', compact('data'));
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
                    //code...
                    $data['job_title_id'] = Crypt::decrypt($data['job_title_id']);
                } catch (\Throwable $th) {
                    //throw $th;
                    $data['job_title_id'] = $data['job_title_id'];
                }

                try {
                    //code...
                    $data['management_project_id'] = Crypt::decrypt($data['management_project_id']);
                } catch (\Throwable $th) {
                    //throw $th;
                    $data['management_project_id'] = $data['management_project_id'];
                }
                $data = Employee::findByEncryptedId($id)->update($data);

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
            $data = Employee::findByEncryptedId($id);
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

                $delete = Employee::whereIn('id', $decryptedIds)->delete();

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

    public function importForm()
    {
        return view('main.employee.import');
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
            Excel::import(new ImportEmployee, $file);

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
        $fileName = 'Employee' . now()->format('Ymd_His') . '.xlsx';
        $data = Employee::all();

        return Excel::download(new EmployeeExport($data), $fileName);
    }
}
