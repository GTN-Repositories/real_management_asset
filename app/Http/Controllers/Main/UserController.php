<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission::user read', ['only' => ['index', 'data', 'getData']]),
            new Middleware('permission::user create', ['only' => ['create', 'store']]),
            new Middleware('permission::user edit', ['only' => ['edit', 'update']]),
            new Middleware('permission::user delete', ['only' => ['destroy']]),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('main.user.index');
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
            ->addColumn('idRelation', function ($data) {
                if ($data->hasRole('driver')) {
                    return $data->id ?? null;
                }
                return null;
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('email', function ($data) {
                return $data->email ?? null;
            })
            ->addColumn('phone', function ($data) {
                return $data->phone ?? null;
            })
            ->addColumn('created', function ($data) {
                return $data->created_at ? $data->created_at->format('d M Y') : null;
            })
            ->addColumn('action', function ($data) {
                $encryptedId = Crypt::encrypt($data->id);
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" data-id="' . $encryptedId . '" onclick="editData(this)"><i class="ti ti-pencil"></i></a>';
                $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Hapus Data" data-id="' . $encryptedId . '" onclick="deleteData(this)"><i class="ti ti-trash"></i></a>';
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
            'email',
            'phone',
            'created_at',
        ];

        $keyword = $request->keyword;

        $data = User::orderBy('id', 'desc')
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
        $roles = Role::orderBy('name', 'asc')->get();
        return view('main.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'roles' => 'required|array',
            'roles.*' => 'string',
        ]);

        try {
            return $this->atomic(function () use ($validatedData) {
                $validatedData['password'] = Hash::make($validatedData['password']);
                $user = User::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                    'phone' => $validatedData['phone'],
                ]);
                if (!empty($validatedData['roles'])) {
                    $roleIds = array_map(fn($encryptedId) => Crypt::decrypt($encryptedId), $validatedData['roles']);
                    $user->syncRoles($roleIds);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'User berhasil ditambahkan!',
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
        $decryptedId = Crypt::decrypt($id);
        $user = User::findOrFail($decryptedId);
        $roles = Role::orderBy('name', 'asc')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $encryptedUserId = Crypt::encrypt($user->id);
        return view('main.user.edit', compact('user', 'roles', 'userRoles', 'encryptedUserId'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string',
            'roles' => 'required|array',
            'roles.*' => 'string',
        ]);

        try {
            return $this->atomic(function () use ($validatedData, $id) {
                $user = User::findOrFail(Crypt::decrypt($id));
                if ($validatedData['email'] !== $user->email) {
                    unset($validatedData['email']);
                }
                if (!empty($validatedData['password'])) {
                    $validatedData['password'] = Hash::make($validatedData['password']);
                    $user->update([
                        'name' => $validatedData['name'],
                        'password' => $validatedData['password'],
                        'phone' => $validatedData['phone'],
                    ]);
                } else {
                    $user->update([
                        'name' => $validatedData['name'],
                        'phone' => $validatedData['phone'],
                    ]);
                }
                if (!empty($validatedData['roles'])) {
                    $roleIds = array_map(fn($encryptedId) => Crypt::decrypt($encryptedId), $validatedData['roles']);
                    $user->syncRoles($roleIds);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'User berhasil diperbarui!',
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
        $userId = Crypt::decrypt($id);
        try {
            $user = User::findOrFail($userId);
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'User gagal dihapus! ' . $th->getMessage(),
            ]);
        }
    }


    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                $delete = User::whereIn('id', $ids)->delete();

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
