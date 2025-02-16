<?php

namespace App\Http\Controllers\Main\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\RequestOrder;
use App\Models\RequestOrderDetail;
use App\Models\UploadInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class UploadInvoiceController extends Controller
{
    public function index()
    {
        return view('main.procurement.upload_invoice.index');
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
                        $btn .= '<a href="' . route('procurement.upload-invoice.show', $data->id) . '" class="btn-edit-data btn-sm me-1 shadow me-2" title="Lihat Data"><i class="ti ti-eye"></i></a>';
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

    public function create(Request $request)
    {
        $ro = RequestOrder::findByEncryptedId($request->request_order_id);

        return view('main.procurement.upload_invoice.create', compact('ro'));
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

        return view('main.procurement.upload_invoice.show', compact('backlog', 'item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = RequestOrder::findByEncryptedId($id);

        return view('main.procurement.upload_invoice.edit', compact('data'));
    }

    public function editItem($id)
    {
        $data = RequestOrderDetail::findByEncryptedId($id);
        $uploadInvoice = UploadInvoice::where('request_order_id', $data->request_order_id)->get();

        return view('main.procurement.upload_invoice.edit', compact('data', 'uploadInvoice'));
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
            return $this->atomic(function () use ($data, $id, $request) {
                foreach ($data['name'] as $key => $value) {
                    $updateItem = $value['id'][$key] ?? null;
                    if ($updateItem != null) {
                        $uploadInvoice = UploadInvoice::findByEncryptedId($updateItem);
    
                        if (isset($data['attachment'][$key])) {
                            $file = $request->file('attachment')->store('upload-invoice', 'public');
                            $data['attachment'] = $file;
                        }
    
                        $uploadInvoice->update([
                            'name' => $data['name'][$key],
                            'attachment' => $data['attachment'][$key],
                            'note' => $data['note'][$key],
                        ]);
                    } else {
                        $create = [
                            'name' => $data['name'][$key],
                            'note' => $data['note'][$key],
                            'request_order_id' => $data['request_order_id'],
                        ];

                        if (isset($data['attachment'][$key])) {
                            $file = $data['attachment'][$key]->store('upload-invoice', 'public');
                            $create['attachment'] = $file;
                        }
                        $create['user_id'] = Auth::user()->id;

                        $UploadInvoice = UploadInvoice::create($create);
                    }
                }

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

    public function sendInvoice($id)
    {
        $data = RequestOrder::findByEncryptedId($id);
        $data->status = 103;
        $data->save();
        
        return response([
            'status' => true,
            'message' => 'Data berhasil dikirim!',
        ]);
    }
}
