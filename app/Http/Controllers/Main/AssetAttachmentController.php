<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AssetAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AssetAttachmentController extends Controller
{
    //
    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('attachment', function ($data) {
                $img = '<img src="' . asset('storage/' . $data->attachment) . '" class="img-fluid rounded" width="100px" height="100px" />';
                return $img ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'attachment',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = AssetAttachment::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->where('asset_id', Crypt::decrypt($request->asset_id));

        return $data;
    }

    public function store(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $data['asset_id'] = Crypt::decrypt($id);
                $data['attachment'] = $data['attachment']->store('assets', 'public');

                $data = AssetAttachment::create($data);

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
