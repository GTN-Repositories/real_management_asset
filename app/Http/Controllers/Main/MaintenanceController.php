<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\ManagementProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    //
    public function index()
    {
        return view('main.inspection_schedule.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name ?? '-';
            })
            ->addColumn('type', function ($data) {
                return $data->type ?? '-';
            })
            ->addColumn('note', function ($data) {
                return $data->note ?? '-';
            })
            ->addColumn('managementProject', function ($data) {
                return $data->managementProject->name ?? '-';
            })
            ->addColumn('asset_id', function ($data) {
                return Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate ?? '-';
            })
            ->addColumn('status', function ($data) {
                return $data->status ?? '-';
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? '-';
            })
            ->addColumn('item_name', function ($data) {
                $itemIds = is_array(json_decode($data->item_id, true)) ? json_decode($data->item_id, true) : [];
                $items = Item::whereIn('id', $itemIds)->get()->pluck('name')->implode(', ');
                return $items;
            })
            ->addColumn('item_stock', function ($data) {
                $itemStocks = is_array(json_decode($data->item_stock, true)) ? json_decode($data->item_stock, true) : [];
                return array_sum($itemStocks);
            })
            ->addColumn('kanibal_stock', function ($data) {
                $kanibalStocks = is_array(json_decode($data->kanibal_stock, true)) ? json_decode($data->kanibal_stock, true) : [];
                return array_sum($kanibalStocks);
            })
            ->addColumn('asset_kanibal_name', function ($data) {
                $assetKanibalIds = is_array(json_decode($data->asset_kanibal_id, true)) ? json_decode($data->asset_kanibal_id, true) : [];
                $items = Item::whereIn('id', array_keys($assetKanibalIds))->get()->map(function ($item) use ($assetKanibalIds) {
                    $itemId = (string) Crypt::decrypt($item->id);
                    $item->assetKanibalName = isset($assetKanibalIds[$itemId])
                        ? $assetKanibalIds[$itemId] . ' - ' . Asset::find($assetKanibalIds[$itemId] ?? 0)->name . ' - ' . Asset::find($assetKanibalIds[$itemId] ?? 0)->license_plate
                        : '-';
                    return $item;
                });
                return $items->pluck('assetKanibalName')->implode(', ');
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'type',
            'note',
            'asset_id',
            'management_project_id',
            'status',
            'date',
            'item_id',
            'item_stock',
            'kanibal_stock',
            'asset_kanibal_id'
        ];

        $keyword = $request->keyword ?? '';

        $data = InspectionSchedule::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    $query->where(function ($q) use ($keyword, $columns) {
                        foreach ($columns as $column) {
                            $q->orWhere($column, 'LIKE', '%' . $keyword . '%');
                        }
                    });
                }
            });

        if (session('selected_project_id')) {
            $managementProject = ManagementProject::find(Crypt::decrypt(session('selected_project_id')));

            if ($managementProject) {
                $assetIds = $managementProject->asset_id;
                $data->whereIn('id', $assetIds);
            }
        }

        return $data;
    }
}
