<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('main.menu.index');
    }
    public function useMenu()
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        dd($menus);
        return view('main.layout.sidebar', compact('menus'));
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
            ->addColumn('nama', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('icon', function ($data) {
                $img = '<img src="' . asset('storage/images/menu/' . $data->icon) . '" class="img-fluid rounded" width="50px" height="50px" />';
                return $img;
            })
            ->addColumn('urutan', function ($data) {
                return $data->order ?? null;
            })
            ->addColumn('url', function ($data) {
                return $data->route ?? null;
            })
            ->addColumn('action', function ($data) {
                $encryptedId = Crypt::encrypt($data->id);
                $btn = '<div class="d-flex">';
                if (!auth()->user()->hasRole('Read only')) {
                    if (auth()->user()->hasPermissionTo('menu-edit')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data" data-id="' . $encryptedId . '" onclick="editData(this)"><i class="ti ti-pencil"></i></a>';
                    }
                    if (auth()->user()->hasPermissionTo('menu-delete')) {
                        $btn .= '<a href="javascript:void(0);" class="btn-delete-data btn-sm shadow" title="Hapus Data" data-id="' . $encryptedId . '" onclick="deleteData(this)"><i class="ti ti-trash"></i></a>';
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
            'name',
            'icon',
            'order',
            'route',
        ];

        $keyword = $request->keyword;

        $data = Menu::orderBy('id', 'desc')
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
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lastOrder = Menu::max('order') ?? 0;
        $nextOrder = $lastOrder + 1;
        $menu = Menu::all();

        return view('main.menu.create', compact('nextOrder', 'menu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'order' => 'required|integer|min:0',
            'route' => 'required|string|max:100',
            'parent_id' => 'nullable|string',
        ]);

        try {
            $imageName = null;
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $imageName = Str::random(10) . '.' . $icon->getClientOriginalExtension();
                $icon->move(public_path('storage/images/menu/'), $imageName);
            }
            $decryptedparentId = Crypt::decryptString($request->input('parent_id'));
            $parentId = $decryptedparentId == 0 ? null : $decryptedparentId;
            return $this->atomic(function () use ($request, $imageName, $parentId) {
                $menuData = $request->except(['icon', 'parent_id']);
                $menuData['user_id'] = Auth::id();
                $menuData['parent_id'] = $parentId;

                if ($imageName) {
                    $menuData['icon'] = $imageName;
                }
                Menu::create($menuData);
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan!',
                'error' => $th->getMessage(),
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
        $decryptedId = Crypt::decryptString($id);
        $thisId = unserialize($decryptedId);
        $data = Menu::find($thisId);
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Menu tidak ditemukan!'], 404);
        }
        $encryptedId = Crypt::encryptString($data->id);
        $menus = Menu::all();
        return view('main.menu.edit', compact('data', 'encryptedId', 'menus'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'order' => 'required|integer|min:0',
            'route' => 'required|string|max:100',
            'parent_id' => 'nullable|string',
        ]);

        try {
            $decryptedParentId = Crypt::decryptString($request->input('parent_id'));
            $menu = Menu::findOrFail(Crypt::decryptString($id));

            // Gunakan transaksi atomik
            return $this->atomic(function () use ($request, $menu, $decryptedParentId) {
                // Simpan nama icon lama
                $imageName = $menu->icon;

                // Jika ada icon baru, hapus icon lama dan simpan yang baru
                if ($request->hasFile('icon')) {
                    // Hapus file icon lama jika ada
                    if ($menu->icon && file_exists(public_path('storage/images/menu/' . $menu->icon))) {
                        unlink(public_path('storage/images/menu/' . $menu->icon));
                    }

                    // Upload file icon baru
                    $icon = $request->file('icon');
                    $imageName = Str::random(10) . '.' . $icon->getClientOriginalExtension();
                    $icon->move(public_path('storage/images/menu/'), $imageName);
                }

                // Siapkan data untuk update
                $menuData = $request->except(['icon', 'parent_id']);
                $menuData['user_id'] = Auth::id();
                $menuData['parent_id'] = $decryptedParentId == 0 ? null : $decryptedParentId;
                $menuData['icon'] = $imageName;

                // Update data menu
                $menu->update($menuData);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal diupdate!',
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $itemId = Crypt::decryptString($id);
        $decryptedId = unserialize($itemId);
        try {
            $menu = Menu::findOrFail($decryptedId);
            return $this->atomic(function () use ($menu) {
                if ($menu->icon && file_exists(public_path('storage/images/menu/' . $menu->icon))) {
                    unlink(public_path('storage/images/menu/' . $menu->icon));
                }
                $menu->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal dihapus!',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
