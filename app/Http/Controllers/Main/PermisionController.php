<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission::permission read', ['only' => ['index', 'data', 'getData']]),
            new Middleware('permission::permission create', ['only' => ['create', 'store']]),
            new Middleware('permission::permission edit', ['only' => ['edit', 'update']]),
            new Middleware('permission::permission delete', ['only' => ['destroy']]),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('main.permission.index');
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
                if (auth()->user()->hasPermissionTo('permission-edit')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" data-id="' . $encryptedId . '" onclick="editData(this)"><i class="ti ti-pencil"></i></a>';
                }
                if (auth()->user()->hasPermissionTo('permission-delete')) {
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Hapus Data" data-id="' . $encryptedId . '" onclick="deleteData(this)"><i class="ti ti-trash"></i></a>';
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

        $data = Permission::orderBy('id', 'desc')
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
        return view('main.permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data['user_id'] = Auth::user()->id;
                $data['guard_name'] = 'web';
                Permission::create($data);
                $roles = Role::whereIn('name', ['superadmin', 'admin'])->get();
                foreach ($roles as $role) {
                    $role->givePermissionTo(Permission::latest()->first());
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
        $data = Permission::findOrFail($decryptedId);

        return view('main.permission.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = Permission::find($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Diubah!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Diubah!',
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
                $deleted = Permission::where('id', $decryptedId)
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
                $delete = Permission::whereIn('id', $ids)->delete();

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
