<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\InspectionSchedule;
use App\Models\Maintenance;
use App\Models\StatusAsset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ReportMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        $dataByDate = $this->dataByDate($daysInMonth, $month, $year);
        
        return view('main.report_maintenance.index', compact('daysInMonth', 'month', 'year', 'dataByDate'));
    }


    public function chart()
    {
        // Ambil semua data dari InspectionSchedule
        $schedules = InspectionSchedule::all();

        // Kelompokkan berdasarkan asset_id dan hitung jumlah kemunculan setiap asset_id
        $topAssets = $schedules->groupBy('asset_id') // Kelompokkan berdasarkan asset_id
            ->map(function ($group) {
                return $group->count(); // Hitung jumlah item dalam setiap grup
            })
            ->sortDesc() // Urutkan dari yang terbesar ke yang terkecil
            ->take(5); // Ambil 5 teratas

        $data = [];
        $color = ['#8979FF', '#FF928A', '#3CC3DF', '#FFAE4C', '#537FF1'];
        $color_index = 0;
        foreach ($topAssets as $key => $value) {
            $inspection = InspectionSchedule::where('asset_id', $key);

            $data_array = [];
            foreach (range(1, 12) as $month) {
                $data_array[] = $inspection->whereMonth('date', $month)->count() ?? 0;
            }
            
            $data[] = [
                'label' => 'AST - '. $key,
                'data' => $data_array,
                'borderColor' => $color[$color_index],
                'tension' => 0.4 // Linear mode
            ];

            $color_index++;
        }

        return response()->json($data);
    }

    // public function dataByDate($daysInMonth, $month, $year)
    // {
    //     $schedules = InspectionSchedule::get()->groupBy('asset_id')->sortDesc();

    //     $data = [];
    //     $color = [
    //         'Ringan' => '#00BD2C',
    //         'Sedang' => '#FABE29',
    //         'Berat' => '#FF0004',
    //         'Aktif' => '#248FD6',
    //         'RFU' => '#7F2DE8',
    //         'Scrap' => '#666666',
    //         'Uncertain' => '#FFFFFF'
    //     ];
        
    //     foreach ($schedules as $key => $value) {
    //         $inspection = InspectionSchedule::where('asset_id', $key);

    //         $data_date = [];
    //         foreach (range(1, $daysInMonth) as $day) {
    //             $date = $year.'-'.$month.'-'.$day;
    //             $data_date[] = $inspection->whereDate('date', $date)->orderBy('created_at', 'desc')->first()->urgention ?? 'Uncertain';
    //         }

    //         $data[] = [
    //             'asset_id' => $key,
    //             'data' => $data_date,
    //         ];
    //     }

    //     return $data;
    // }

    public function dataByDate($daysInMonth, $month, $year)
    {
        $schedules = StatusAsset::get()->groupBy('asset_id')->sortDesc();

        $data = [];
        
        foreach ($schedules as $key => $value) {
            $data_date = null;
            foreach (range(1, $daysInMonth) as $day) {
                $date = Carbon::parse($year.'-'.$month.'-'.$day)->format('Y-m-d');
                $data_date[] = StatusAsset::where('asset_id', $key)->whereDate('created_at', $date)->where('type', 'maintenance')->orderBy('created_at', 'desc')->get();
            }
            
            $data[] = [
                'asset_id' => $key,
                'data' => $data_date,
            ];
        }

        return $data;
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('asset', function ($data) {
                return 'AST-' . Crypt::decrypt($data->first()->asset->id) . ' - ' . ($data->first()->asset->name ?? null). ' - ' . ($data->first()->asset->serial_number ?? null);
            })
            ->addColumn('date', function ($data) {
                return $data->first()->date ?? null;
            })
            ->addColumn('last_problem', function ($data) {
                return $data->first()->note ?? null;
            })
            ->addColumn('total_problem', function ($data) {
                return $data->count() ?? null;
            })
            ->addColumn('last_duration', function ($data) {
                return $data->first()->created_at->diffForHumans() ?? null;
            })
            ->addColumn('status', function ($data) {
                return $data->first()->urgention ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $keyword = $request->search['value'] ?? '';
        $data = InspectionSchedule::orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy('asset_id');

        return $data;
    }
}
