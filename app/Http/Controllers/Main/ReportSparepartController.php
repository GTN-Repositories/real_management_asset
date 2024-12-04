<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportSparepartController extends Controller
{
    //
    public function index()
    {
        return view('main.report_sparepart.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('code', function ($data) {
                return $data->code ?? '-';
            })
            ->addColumn('name', function ($data) {
                return $data->name ?? '-';
            })
            ->addColumn('stock', function ($data) {
                return $data->stock ?? '-';
            })
            ->addColumn('price', function ($data) {
                return 'Rp.' . number_format($data->price, 0, ',', '.') ?? '-';
            })
            ->addColumn('size', function ($data) {
                return $data->size ?? '-';
            })
            ->addColumn('brand', function ($data) {
                return $data->brand ?? '-';
            })
            ->addColumn('oum_id', function ($data) {
                return $data->oum->name ?? '-';
            })
            ->addColumn('category_id', function ($data) {
                return $data->category->name ?? '-';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'code',
            'name',
            'stock',
            'price',
            'size',
            'brand',
            'oum_id',
            'category_id',
        ];

        $keyword = $request->keyword ?? "";

        $data = Item::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $data->whereBetween('created_at', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
        }

        // Apply predefined filters
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'hari ini':
                    $data->whereDate('date', Carbon::today());
                    break;
                case 'minggu ini':
                    $data->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulan ini':
                    $data->whereRaw('MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'bulan kemarin':
                    $data->whereRaw('MONTH(date) = MONTH(NOW()) - 1 AND YEAR(date) = YEAR(NOW())');
                    break;
                case 'tahun ini':
                    $data->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $data->whereYear('date', Carbon::now()->subYear()->year);
                    break;
            }
        }

        return $data;
    }
}
