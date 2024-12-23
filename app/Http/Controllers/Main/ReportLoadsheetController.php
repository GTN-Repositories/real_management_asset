<?php

namespace App\Http\Controllers\Main;

use App\Exports\AssetLoadsheetExport;
use App\Exports\ProjectLoadsheetExport;
use App\Exports\ReportLoadsheetExport;
use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use App\Models\Loadsheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportLoadsheetController extends Controller
{
    //
    public function index()
    {
        return view('main.report_loadsheet.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('loadsheets')
            ->join('management_projects', 'loadsheets.management_project_id', '=', 'management_projects.id')
            ->selectRaw('management_projects.name as project_name, SUM(loadsheets.loadsheet) as total_loadsheet')
            ->groupBy('management_projects.name');

        $data = $query->get();

        return datatables()->of($data)
            ->addColumn('project_name', function ($row) {
                return $row->project_name;
            })
            ->addColumn('total_loadsheet', function ($row) {
                return $row->total_loadsheet;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function dataAsset()
    {
        $query = Loadsheet::join('assets', 'loadsheets.asset_id', '=', 'assets.id')
            ->selectRaw('assets.id, assets.name, assets.asset_number, SUM(loadsheets.loadsheet) as total_loadsheet')
            ->groupBy('assets.id', 'assets.name', 'assets.asset_number');

        $data = $query->get();

        return datatables()->of($data)
            ->addColumn('id', function ($row) {
                return Crypt::decrypt($row->id);
            })
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('asset_number', function ($row) {
                return $row->asset_number;
            })
            ->addColumn('total_loadsheet', function ($row) {
                return $row->total_loadsheet;
            })
            ->addColumn('liter', function ($row) {
                $totalLiter = FuelConsumption::where('asset_id', Crypt::decrypt($row->id))->sum('liter');
                return number_format($totalLiter, 0, ',', '.') ?? "kosong";
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }
}
