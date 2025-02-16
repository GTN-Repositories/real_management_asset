<?php

namespace App\Http\Controllers\Main\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\RequestOrder;
use App\Models\RequestOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ProcessPoController extends Controller
{
    public function index()
    {
        return view('main.procurement.process_po.index');
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
                        $btn .= '<a href="' . route('procurement.process-po.show', $data->id) . '" class="btn-edit-data btn-sm me-1 shadow me-2" title="Edit Data"><i class="ti ti-eye"></i></a>';
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

    public function create()
    {
        return view('main.procurement.process_po.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $countRo = RequestOrder::count() + 1;
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

                $requestOrder = RequestOrder::create($createRo);
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

                    $requestOrderDetail = RequestOrderDetail::create($createRod);
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
        $backlog = RequestOrder::findByEncryptedId($id);
        $item = RequestOrderDetail::where('request_order_id', Crypt::decrypt($backlog->id))->get();

        return view('main.procurement.process_po.show', compact('backlog', 'item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = RequestOrder::findByEncryptedId($id);

        return view('main.procurement.process_po.edit', compact('data'));
    }

    public function editItem($id)
    {
        $data = RequestOrderDetail::findByEncryptedId($id);

        return view('main.procurement.process_po.edit', compact('data'));
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

    public function sendPo(Request $request, $id)
    {
        try {
            return $this->atomic(function () use ($request, $id) {
                $ro = RequestOrder::findByEncryptedId($id);
                $ro->status = $request->status;
                $ro->reason = $request->reason;
                $ro->save();
    
                if ($request->status == 104) {
                    $this->storePo($ro);
                }
    
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

    public function storePo($ro)
    {
        $data = RequestOrder::findByEncryptedId($ro->id);

        try {
            return $this->atomic(function () use ($data) {
                $countRo = PurchaseOrder::count() + 1;
                $code = "PO-".now()->format('ymd')."-".sprintf('%04d', $countRo);
                $warehouse_id = $data['warehouse_id'];

                $totalPrice = $data->total_price;
                $totalItem = $data->total_item;

                $createPo = [
                    'request_order_id' => decrypt($data->id),
                    'code' => $code,
                    'total_item' => $totalItem,
                    'total_price' => $totalPrice,
                    'date' => $data['date'],
                    'warehouse_id' => $warehouse_id,
                    'created_by' => Auth::user()->id,
                    'status' => 104
                ];

                $purchaseOrder = PurchaseOrder::create($createPo);
                $purchase_order_id = Crypt::decrypt($purchaseOrder->id);
                
                $requestOrderDetail = RequestOrderDetail::where('request_order_id', decrypt($data->id))->get();

                foreach ($requestOrderDetail as $key => $value) {
                    $createPod = [
                        'purchase_order_id' => $purchase_order_id,
                        'item_id' => $value->item_id,
                        'warehouse_id' => $value->warehouse_id,
                        'qty' => $value->qty,
                        'price' => $value->price,
                        'total_price' => $value->total_price,
                        'request_order_detail_id' => decrypt($value->id)
                    ];

                    $purchaseOrderDetail = PurchaseOrderDetail::create($createPod);
                }

                return true;
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
