<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AssetReminder;
use App\Models\Loadsheet;
use App\Models\RecordInsurance;
use App\Models\RecordRent;
use App\Models\RecordTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ExpensesController extends Controller
{
    //
    public function index()
    {
        return view('main.expenses.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('asset_id', function ($data) {
                return 'AST-' . Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate ?? null;
            })
            ->addColumn('insurance', function ($data) {
                return $data->insurance ?? null;
            })
            ->addColumn('summary', function ($data) {
                return $data->summary ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'insurance',
            'summary',
            'date',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = RecordInsurance::selectRaw('asset_id, MAX(date) as date, SUM(summary) as summary, MAX(insurance) as insurance')
            ->groupBy('asset_id')
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        return $data;
    }

    public function dataTax(Request $request)
    {
        $data = $this->getDataTax($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('asset_id', function ($data) {
                return 'AST-' . Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate ?? null;
            })
            ->addColumn('tax', function ($data) {
                return $data->tax ?? null;
            })
            ->addColumn('summary', function ($data) {
                return $data->summary ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataTax(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'tax',
            'summary',
            'date',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = RecordTax::selectRaw('asset_id, MAX(date) as date, SUM(summary) as summary,MAX(tax) as tax')
            ->groupBy('asset_id')
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        return $data;
    }

    public function dataRent(Request $request)
    {
        $data = $this->getDataRent($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('asset_id', function ($data) {
                return 'AST-' . Crypt::decrypt($data->asset->id) . ' - ' . $data->asset->name . ' - ' . $data->asset->license_plate ?? null;
            })
            ->addColumn('rent', function ($data) {
                return $data->rent ?? null;
            })
            ->addColumn('summary', function ($data) {
                return $data->summary ?? null;
            })
            ->addColumn('date', function ($data) {
                return $data->date ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataRent(Request $request)
    {
        $columns = [
            'id',
            'asset_id',
            'rent',
            'summary',
            'date',
        ];

        $keyword = $request->search['value'] ?? '';

        $data = RecordRent::selectRaw('asset_id, MAX(date) as date, SUM(summary) as summary,MAX(rent) as rent')
            ->groupBy('asset_id')
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
