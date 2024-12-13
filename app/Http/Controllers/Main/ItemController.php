<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\CategoryItem;
use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index()
    {
        return view('main.item.index');
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
            ->addColumn('item_id', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('part', function ($data) {
                return $data->part ?? null;
            })
            ->addColumn('image', function ($data) {
                $img = '<img src="' . asset('storage/images/item/' . $data->image) . '" class="img-fluid rounded" width="50px" height="50px" />';
                return $img ?? null;
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->addColumn('category', function ($data) {
                return $data->category->name ?? null;
            })
            ->addColumn('code', function ($data) {
                return $data->code ?? null;
            })
            ->addColumn('status', function ($data) {
                return $data->status ?? null;
            })
            ->addColumn('size', function ($data) {
                return $data->size ?? null;
            })
            ->addColumn('brand', function ($data) {
                return $data->brand ?? null;
            })
            ->addColumn('color', function ($data) {
                $color = '<div class="rounded" style="background-color: ' . $data->color . '; width: 50px; height: 50px;"></div>';
                return $data->color ? $color : null;
            })
            ->addColumn('stock', function ($data) {
                return $data->stock ?? null;
            })
            ->addColumn('no_invoice', function ($data) {
                return $data->no_invoice ?? null;
            })
            ->addColumn('supplier_addrees', function ($data) {
                return $data->supplier_addrees ?? null;
            })
            ->addColumn('supplier_name', function ($data) {
                return $data->supplier_name ?? null;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0);" class="btn btn-info btn-sm me-1" title="Show Data" onclick="showData(\'' . $data->id . '\')"><i class="ti ti-eye"></i></a>';
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
            'part',
            'image',
            'name',
            'category_id',
            'code',
            'status',
            'size',
            'brand',
            'color',
            'stock',
            'oum_id',
            'created_at',
            'no_invoice',
            'supplier_name',
            'supplier_addrees',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = Item::orderBy('created_at', 'asc')
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


    public function create()
    {
        $data = CategoryItem::all();
        return view('main.item.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $request) {
                $data['price'] = isset($data['price']) && $data['price'] != '-' ? str_replace('.', '', $data['price']) : null;

                $imageName = null;
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('storage/images/item/'), $imageName);
                    $data['image'] = $imageName;
                }

                $siteIdEncrypted = Crypt::decrypt($data['category_id']);

                $data['category_id'] = $siteIdEncrypted;

                $data['oum_id'] = Crypt::decrypt($data['oum_id']);

                $data = Item::create($data);

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
        $data = Item::findByEncryptedId($id);
        $itemStock = ItemStock::where('item_id', Crypt::decrypt($data->id))->get();
        return view('main.item.show', compact('data', 'itemStock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Item::findByEncryptedId($id);
        $relation = CategoryItem::all();
        return view('main.item.edit', compact('data', 'relation'));
    }

    public function editStock()
    {
        return view('main.item.stock');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id, $request) {
                $data['price'] = isset($data['price']) && $data['price'] != '-' ? str_replace('.', '', $data['price']) : null;

                $item = Item::findByEncryptedId($id);
                if ($request->hasFile('image')) {
                    if ($item && $item->image) {
                        $oldImagePath = public_path('storage/images/item/' . $item->image);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $image = $request->file('image');
                    $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('storage/images/item/'), $imageName);
                    $data['image'] = $imageName;
                } else {
                    $data['image'] = $item->image ?? null;
                }


                if (isset($data['category_id'])) {
                    try {
                        $data['category_id'] = Crypt::decrypt($data['category_id']);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $data['category_id'] = $data['category_id'];
                    }
                }

                try {
                    $data['oum_id'] = Crypt::decrypt($data['oum_id']);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data['oum_id'] = $data['oum_id'];
                }

                $item->update($data);

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
            $data = Item::findByEncryptedId($id);
            if ($data->image && file_exists(public_path('storage/images/item/' . $data->image))) {
                unlink(public_path('storage/images/item/' . $data->image));
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

                $delete = Item::whereIn('id', $decryptedIds)->delete();

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

    public function createStock(Request $request)
    {
        try {
            return $this->atomic(function () use ($request) {
                $data = $request->all();
                $data['item_id'] = Crypt::decrypt($data['item_id']);
                $data['request_by'] = Auth::user()->id;

                ItemStock::create($data);
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan! ' . $th->getMessage(),
            ]);
        }
    }

    public function approveStock(Request $request, $id)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $id) {
                $data['aproved_by'] = Auth::user()->id;
                $data['status'] = $data['status'];

                $create = ItemStock::findByEncryptedId($id);

                if ($data['status'] === 'approved') {
                    if ($create->metode === 'increase') {
                        $item = Item::findOrFail($create->item_id);
                        $item->stock += (int) $create->stock;
                        $item->save();
                    } else if ($create->metode === 'decrease') {
                        $item = Item::findOrFail($create->item_id);
                        $item->stock -= (int) $create->stock;
                        $item->save();
                    }
                }
                if ($data['status'] === 'rejected') {
                    if ($create->metode === 'increase') {
                        $item = Item::findOrFail($create->item_id);
                        $item->stock -= (int) $create->stock;
                        $item->save();
                    } else if ($create->metode === 'decrease') {
                        $item = Item::findOrFail($create->item_id);
                        $item->stock += (int) $create->stock;
                        $item->save();
                    }
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
