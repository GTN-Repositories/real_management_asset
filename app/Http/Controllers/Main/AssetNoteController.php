<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AssetNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AssetNoteController extends Controller
{
    //
    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('note', function ($data) {
                return $data->note ?? "kosong";
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at ?? "kosong";
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'note',
            'created_at',
        ];

        $keyword = $request->search['value'] ?? "";
        $assetId = Crypt::decrypt($request->asset_id);

        $data = AssetNote::orderBy('created_at', 'asc')
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
