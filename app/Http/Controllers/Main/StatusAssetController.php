<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\InspectionComment;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\MaintenanceSparepart;
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
        $inspection_ids = $data->pluck('id')->toArray();

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
                $maintenance_ids = Maintenance::where('inspection_schedule_id', Crypt::decrypt($data->id))->pluck('id')->toArray();
                $maintenance_ids_decrypted = [];
                foreach ($maintenance_ids as $key => $value) {
                    $maintenance_ids_decrypted[$key] = Crypt::decrypt($value);
                }
                $sparepart_out = MaintenanceSparepart::whereIn('maintenance_id', $maintenance_ids_decrypted)->get();

                $item = '<ul>';
                foreach ($sparepart_out as $value) {
                    $item_name = Item::find($value->item_id)->name ?? null;
                    if ($value->type == 'Replacing') {
                        $item .= '<li> Replacing From : ' . $item_name . ' - ' . $value->quantity . '</li>';
                    }else if ($value->type == 'Stock') {
                        $item .= '<li> Stock : ' . $item_name . ' - ' . $value->quantity . '</li>';
                    }
                }
                $item .= '</ul>';

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
            'asset_id',
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
