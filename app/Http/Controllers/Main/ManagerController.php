<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AssetManager;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    //
    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                return $data->id ?? null;
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = AssetManager::orderBy('id', 'desc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return $data;
    }
}
