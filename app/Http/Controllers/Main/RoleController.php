<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission::role read', ['only' => ['index', 'data', 'getData']]),
            new Middleware('permission::role create', ['only' => ['create', 'store']]),
            new Middleware('permission::role edit', ['only' => ['edit', 'update']]),
            new Middleware('permission::role delete', ['only' => ['destroy']]),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('main.role.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $encryptedId = Crypt::encrypt($data->id);
                $checkbox = '<div class="custom-control custom-checkbox">
            <input class="custom-control-input checkbox" id="checkbox' . $encryptedId . '" type="checkbox" value="' . $encryptedId . '" />
            <label class="custom-control-label" for="checkbox' . $encryptedId . '"></label>
        </div>';

                return $checkbox;
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('created', function ($data) {
                return $data->created_at ? $data->created_at->format('d M Y') : null;
            })
            ->addColumn('action', function ($data) {
                $encryptedId = Crypt::encrypt($data->id);
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('role-edit')) {
                    $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" data-id="' . $encryptedId . '" onclick="editData(this)"><i class="ti ti-pencil"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('role-delete')) {
                    $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" data-id="' . $encryptedId . '" onclick="deleteData(this)"><i class="ti ti-trash"></i></a>';
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
            'created_at',
        ];

        $keyword = $request->keyword;

        $data = Role::orderBy('id', 'desc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return $data;
    }


    public function create()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        return view('main.role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string', // karena permission di-enkripsi sebagai string
        ]);

        try {
            return $this->atomic(function () use ($validatedData) {
                $validatedData['user_id'] = Auth::user()->id;

                $role = Role::create(['name' => $validatedData['name'], 'user_id' => $validatedData['user_id']]);
                if (!empty($validatedData['permissions'])) {
                    foreach ($validatedData['permissions'] as $encryptedPermissionId) {
                        $permissionId = Crypt::decrypt($encryptedPermissionId);
                        $role->givePermissionTo(Permission::findOrFail($permissionId));
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Tambahkan! ' . $th->getMessage(),
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
        $role = Role::findOrFail($decryptedId);
        $permissions = Permission::orderBy('name', 'asc')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('main.role.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
            'encryptedRoleId' => Crypt::encrypt($role->id)
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        try {
            return $this->atomic(function () use ($validatedData, $decryptedId) {
                $role = Role::findOrFail($decryptedId);

                $role->update(['name' => $validatedData['name']]);
                if (!empty($validatedData['permissions'])) {
                    $permissionIds = array_map(fn($encryptedId) => Crypt::decrypt($encryptedId), $validatedData['permissions']);
                    $role->syncPermissions($permissionIds);
                } else {
                    $role->syncPermissions([]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Diupdate!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal Diupdate! ' . $th->getMessage(),
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $decryptedId = Crypt::decrypt($id);
        try {
            return $this->atomic(function () use ($decryptedId) {
                // dd('akjvbaeouvbqaovb');
                $deleted = Role::where('id', $decryptedId)
                    ->delete();
                if ($deleted) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Data Berhasil Dihapus!',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data Gagal Dihapus!',
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal oiahgqeo9ugbeiognaepgnaepgaepignaeqpignaqepoqng!',
            ]);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                $delete = Role::whereIn('id', $ids)->delete();

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
