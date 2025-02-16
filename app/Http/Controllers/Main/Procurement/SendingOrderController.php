<?php

namespace App\Http\Controllers\Main\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\RequestOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SendingOrderController extends Controller
{
    public function index()
    {
        return view('main.procurement.sending_order.index');
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
            ->addColumn('request_order_code', function ($data) {
                return $data->requestOrder?->code ?? null;
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
                        $btn .= '<a href="' . route('procurement.sending-order.show', $data->id) . '" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data"><i class="ti ti-eye"></i></a>';
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
            'request_order_id',
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

        $data = PurchaseOrder::orderBy('date', 'asc')
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

    public function create()
    {
        return view('main.procurement.sending_order.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $countRo = PurchaseOrder::count() + 1;
                $code = "RO-".now()->format('ymd')."-".sprintf('%04d', $countRo);
                $warehouse_id = Crypt::decrypt($data['warehouse_id']);

                $totalPrice = array_sum(array_map(function ($price, $quantity) {
                    $price = (int)str_replace('.', '', $price); // Hapus simbol titik
                    return $price * $quantity; // Kalikan harga dengan qty
                }, $data['price'], $data['quantity']));

                $createRo = [
                    'code' => $code,
                    'total_item' => count($data['item_id']),
                    'total_price' => $totalPrice,
                    'date' => $data['date'],
                    'warehouse_id' => $warehouse_id,
                    'created_by' => Auth::user()->id,
                ];

                $requestOrder = PurchaseOrder::create($createRo);
                $request_order_id = Crypt::decrypt($requestOrder->id);
                
                foreach ($data['item_id'] as $key => $item) {
                    $item = Crypt::decrypt($item);
                    $qty = $data['quantity'][$key];
                    $price = str_replace('.', '', $data['price'][$key]);
                    $total_price = $qty * $price;

                    $createRod = [
                        'request_order_id' => $request_order_id,
                        'item_id' => $item,
                        'warehouse_id' => $warehouse_id,
                        'qty' => $qty,
                        'price' => $price,
                        'total_price' => $total_price,
                    ];

                    $requestOrderDetail = PurchaseOrderDetail::create($createRod);
                }

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
        $backlog = PurchaseOrder::findByEncryptedId($id);
        $item = PurchaseOrderDetail::where('purchase_order_id', Crypt::decrypt($backlog->id))->get();

        return view('main.procurement.sending_order.show', compact('backlog', 'item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = PurchaseOrder::findByEncryptedId($id);

        return view('main.procurement.sending_order.edit', compact('data'));
    }

    public function editItem($id)
    {
        $data = PurchaseOrderDetail::findByEncryptedId($id);

        return view('main.procurement.sending_order.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $data = PurchaseOrder::findByEncryptedId($id)->update($data);

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
                $requestOrderDetail = PurchaseOrderDetail::findByEncryptedId($id);
                $requestOrderDetail->update($data);

                $requestOrderDetailCheck = PurchaseOrderDetail::where('purchase_order_id', $requestOrderDetail['purchase_order_id'])->get();
                $requestOrder = PurchaseOrder::find($requestOrderDetail['purchase_order_id']);
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
            $data = PurchaseOrder::findByEncryptedId($id);
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
            $data = PurchaseOrderDetail::findByEncryptedId($id);
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

    public function sendPo($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $po = PurchaseOrder::findByEncryptedId($id);
                $po->status = 105;
                $po->save();

                $ro = RequestOrder::find($po->request_order_id);
                $ro->status = 105;
                $ro->save();
    
                return response([
                    'status' => true,
                    'message' => 'Data berhasil dikirim!',
                ]);
            });
        } catch (\Throwable $th) {
            return response([
                'status' => false,
                'message' => 'Data gagal dikirim! ' . $th->getMessage(),
            ]);
        }
    }
}
