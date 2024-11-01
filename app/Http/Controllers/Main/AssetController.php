<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

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
            ->addColumn('relationId', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('image', function ($data) {
                return $data->image ? '<img src="' . asset('storage/' . $data->image) . '" alt="Image" width="50" height="50"/>' : "kosong";
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? "kosong";
            })
            ->addColumn('serial_number', function ($data) {
                return $data->serial_number ?? "kosong";
            })
            ->addColumn('model_number', function ($data) {
                return $data->model_number ?? "kosong";
            })
            ->addColumn('manager', function ($data) {
                return $data->manager ?? "kosong";
            })
            ->addColumn('category', function ($data) {
                return $data->category ?? "kosong";
            })
            ->addColumn('assets_location', function ($data) {
                return $data->assets_location ?? "kosong";
            })
            ->addColumn('cost', function ($data) {
                return $data->cost ?? "kosong";
            })
            ->addColumn('purchase_date', function ($data) {
                return $data->purchase_date ?? "kosong";
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at ?? "kosong";
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
            'image',
            'name',
            'serial_number',
            'model_number',
            'manager',
            'assets_location',
            'category',
            'cost',
            'purchase_date',
            'created_at',
        ];

        $keyword = $request->search['value'] ?? "";
        $category = $request->category;
        $assets_location = $request->assets_location;
        $manager = $request->manager;

        $data = Asset::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if ($category) {
            $data->where('category', $category);
        }
        if ($assets_location) {
            $data->where('assets_location', $assets_location);
        }
        if ($manager) {
            $data->where('manager', $manager);
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
                if (isset($data['image'])) {
                    $data['image'] = $data['image']->store('assets', 'public');
                }
                $data = Asset::create($data);

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
        $data = Asset::findByEncryptedId($id);

        return view('main.unit.edit', compact('data'));
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
                if (!isset($data['image']) || !$data['image']) {
                    $data['image'] = $asset->image;
                } else {
                    if ($asset->image && Storage::disk('public')->exists($asset->image)) {
                        Storage::disk('public')->delete($asset->image);
                        $data['image'] = $data['image']->store('assets', 'public');
                    }
                }
                $data = $asset->update($data);

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
}
