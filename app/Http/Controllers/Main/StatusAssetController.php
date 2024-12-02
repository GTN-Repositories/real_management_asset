<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\StatusAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class StatusAssetController extends Controller
{
    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {
                return $data->created_at ?? "kosong";
            })
            ->addColumn('status_before', function ($data) {
                return $data->status_before ?? "kosong";
            })
            ->addColumn('status_after', function ($data) {
                return $data->status_after ?? "kosong";
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'status_before',
            'status_after',
            'created_at',
        ];

        $keyword = $request->search['value'] ?? "";
        $assetId = Crypt::decrypt($request->asset_id);

        $data = StatusAsset::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->where('asset_id', $assetId);
            
        return $data;
    }
}
