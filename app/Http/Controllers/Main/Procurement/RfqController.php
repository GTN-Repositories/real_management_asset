<?php

namespace App\Http\Controllers\Main\Procurement;

use App\Http\Controllers\Controller;
use App\Models\RequestOrder;
use App\Models\RequestOrderDetail;
use App\Models\VendorComparation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class RfqController extends Controller
{
    public function index()
    {
        return view('main.procurement.rfq.index');
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
            ->addColumn('code', function ($data) {
                return $data->code ?? null;
            })
            ->addColumn('total_item', function ($data) {
                return $data->total_item ?? null;
            })
            ->addColumn('total_price', function ($data) {
                return number_format($data->total_price, 0, ',', '.') ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->addColumn('warehouse', function ($data) {
                return $data->warehouse->name ?? null;
            })
            ->addColumn('status', function ($data) {
                return $data->status ?? null;
            })
            ->addColumn('created_by', function ($data) {
                return $data->createdBy->name ?? null;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('Y-m-d H:i') ?? null;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('employee-edit')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="' . route('procurement.rfq.show', $data->id) . '" class="btn-edit-data btn-sm me-1 shadow me-2" title="Lihat Data"><i class="ti ti-eye"></i></a>';
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

    public function vendorComparationData(Request $request)
    {
        $data = VendorComparation::where('request_order_id', $request->request_order_id)->get();

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
                return $data->vendor?->name ?? null;
            })
            ->addColumn('price', function ($data) {
                return number_format($data->price) ?? null;
            })
            ->addColumn('notes', function ($data) {
                return $data->notes ?? null;
            })
            ->addColumn('attachment', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="' . asset($data->attachment) . '" download class="btn-edit-data btn-sm me-1 shadow me-2" title="Lihat Data">
                            <i class="ti ti-download"></i>
                         </a>';
                $btn .= '</div>';
            
                return $btn;
            })
            
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if (auth()->user()->hasPermissionTo('employee-edit')) {
                    if (!auth()->user()->hasRole('Read only')) {
                        $btn .= '<a href="' . route('procurement.rfq.edit', $data->id) . '" class="btn-edit-data btn-sm me-1 shadow me-2" title="Lihat Data"><i class="ti ti-pencil"></i></a>';
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

    public function vendorComparation(Request $request)
    {
        $data = VendorComparation::where('request_order_id', $request->request_order_id)
                                    ->when($request->id, function ($query) use ($request) {
                                        $query->where('id', decrypt($request->id));
                                    })
                                    ->first();

        return response()->json($data);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'code',
            'total_item',
            'total_price',
            'date',
            'warehouse_id',
            'status',
            'created_by',
            'created_at',
        ];

        $keyword = $request->search['value'] ?? "";

        $startDate = $request->startDate ?? null;
        $endDate = $request->endDate ?? null;

        $data = RequestOrder::orderBy('date', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });
        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        }

        return $data;
    }

    public function create(Request $request)
    {
        $ro = RequestOrder::findByEncryptedId($request->request_order_id);

        return view('main.procurement.rfq.create', compact('ro'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $request) {
                $data['price'] = str_replace('.', '', $data['price']);

                if ($request->hasFile('attachment')) {
                    $file = $request->file('attachment')->store('rfq', 'public');
                    $data['attachment'] = $file;
                }
                $data['request_order_id'] = Crypt::decrypt($data['request_order_id']);
                $data['vendor_id'] = Crypt::decrypt($data['vendor_id']);
                $data['note'] = $data['note'] ?? null;

                $data = VendorComparation::create($data);

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
    public function show($id)
    {
        $backlog = RequestOrder::findByEncryptedId($id);
        $item = RequestOrderDetail::where('request_order_id', Crypt::decrypt($backlog->id))->get();

        return view('main.procurement.rfq.show', compact('backlog', 'item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = RequestOrder::findByEncryptedId($id);

        return view('main.procurement.rfq.edit', compact('data'));
    }

    public function editItem($id)
    {
        $data = RequestOrderDetail::findByEncryptedId($id);

        return view('main.procurement.rfq.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $data = RequestOrder::findByEncryptedId($id)->update($data);

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

    public function updateItem(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $price = str_replace('.', '', $data['price']);
                $data['total_price'] = $data['qty'] * $price;

                $data['price'] = str_replace('.', '', $data['price']);
                $data['vendor_comparation_id'] = Crypt::decrypt($data['vendor_comparation_id']);
                $requestOrderDetail = RequestOrderDetail::findByEncryptedId($id);
                $requestOrderDetail->update($data);

                $requestOrderDetailCheck = RequestOrderDetail::where('request_order_id', $requestOrderDetail['request_order_id'])->get();
                $requestOrder = RequestOrder::find($requestOrderDetail['request_order_id']);
                $requestOrder->total_price = $requestOrderDetailCheck->sum('total_price');
                $requestOrder->total_item = $requestOrderDetailCheck->count();
                $requestOrder->save();

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
            $data = RequestOrder::findByEncryptedId($id);
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

    public function destroyItem($id)
    {
        try {
            $data = RequestOrderDetail::findByEncryptedId($id);
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

    public function sendRfq($id)
    {
        $data = RequestOrder::findByEncryptedId($id);
        $data->status = 102;
        $data->save();

        return response([
            'status' => true,
            'message' => 'Data berhasil dikirim!',
        ]);
    }
}
