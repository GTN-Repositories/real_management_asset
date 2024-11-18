<?php

namespace App\Http\Controllers\Main;

use App\Models\LogActivity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class LogActivityController extends Controller
{
    //
    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('page', function ($data) {
                return Str::limit($data->page ?? "kosong", 50);
            })
            ->addColumn('ip_address', function ($data) {
                return $data->ip_address ?? "kosong";
            })
            ->addColumn('user_agent', function ($data) {
                return $data->user_agent ?? "kosong";
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'page',
            'ip_address',
            'user_agent',
        ];

        $keyword = $request->search['value'] ?? "";
        $assetId = Crypt::decrypt($request->asset_id);

        $data = LogActivity::orderBy('created_at', 'asc')
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
