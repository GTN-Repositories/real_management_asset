<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\InspectionComment;
use App\Models\InspectionSchedule;
use App\Models\Item;
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

    public function dataSparepartHistory(Request $request)
    {
        $data = $this->getDataSparepartHistory($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                return 'INS - '. Crypt::decrypt($data->id) ?? "-";
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? "kosong";
            })
            ->addColumn('item_stock', function ($data) {
                $item_id = json_decode($data->item_id) ?? [];
                $kanibal = json_decode($data->kanibal_stock) ?? [];
                $stock = json_decode($data->item_stock) ?? [];
                $item = '';

                // foreach ($item_id as $key => $value) {
                //     $item .= Item::find($value)->name ?? null;
                // }

                foreach ($kanibal as $key => $value) {
                    $item_name = Item::find($key)->name ?? null;
                    $qty = $value;
                    $item .= 'Kanibal : ' . $item_name . ' - ' . $qty . '<br>';
                }

                foreach ($stock as $key => $value) {
                    $item_name = Item::find($key)->name ?? null;
                    $qty = $value;
                    $item .= 'Stock : ' . $item_name . ' - ' . $qty . '<br>';
                }

                return $item;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataSparepartHistory(Request $request)
    {
        $columns = [
            'id',
            'name',
            'item_id',
            'asset_id',
            'item_stock',
            'kanibal_stock',
            'management_project_id',
            'date',
        ];

        $keyword = $request->search['value'] ?? "";
        $assetId = Crypt::decrypt($request->asset_id);

        $data = InspectionSchedule::orderBy('date', 'desc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->where('asset_id', $assetId);
            
        if (session('selected_project_id')) {
            $data->where('management_project_id', Crypt::decrypt(session('selected_project_id')));
        }
            
        return $data;
    }

    public function dataInspectionComment(Request $request)
    {
        $data = $this->getDataInspectionComment($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {
                return $data->created_at ?? "-";
            })
            ->addColumn('inspection_schedule_id', function ($data) {
                return 'INS - '. $data->inspection_schedule_id ?? "-";
            })
            ->addColumn('comment', function ($data) {
                return $data->comment ?? "-";
            })
            ->addColumn('user_id', function ($data) {
                return $data->user->name ?? "-";
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataInspectionComment(Request $request)
    {
        $inspection_ids = $this->getDataSparepartHistory($request)->pluck('id')->toArray();

        foreach ($inspection_ids as $key => $value) {
            $inspection_ids[$key] = Crypt::decrypt($value);
        }

        $columns = [
            'id',
            'inspection_schedule_id',
            'comment',
            'created_at',
            'updated_at',
            'user_id',
            'time_note',
        ];

        $keyword = $request->search['value'] ?? "";

        $data = InspectionComment::orderBy('created_at', 'desc')
            ->select($columns)
            ->whereIn('inspection_schedule_id', $inspection_ids)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });
            
        return $data;
    }
    
}
