<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
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

        // $dataByDate = $this->getMaintenanceSchedule();

        // Hitung tanggal awal dan akhir
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Ambil semua asset
        $assets = Asset::get();

        $dataByDate = [];

        foreach ($assets as $asset) {
            $assetId = decrypt($asset->id);
            $dataByDate[$assetId] = [
                'name' => $asset->name,
                'code' => $asset->code,
                'days' => [],
            ];

            // Inisialisasi semua hari dengan status 'Aktif'
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
                $assetId = decrypt($asset->id);
                $dataByDate[$assetId]['days'][$dayKey] = [
                    'DS' => 'Aktif',
                    'NS' => 'Aktif',
                ];
            }

            // Proses maintenance untuk asset ini
            $assetMaintenance = Maintenance::where('asset_id', decrypt($asset->id))
                                            ->where(function($query) use ($startDate, $endDate) {
                                                $query->whereBetween('date', [$startDate, $endDate])
                                                    ->orWhereBetween('finish_at', [$startDate, $endDate]);
                                            })
                                            ->get();
            foreach ($assetMaintenance as $maintenance) {
                $currentDay = Carbon::parse($maintenance->date)->copy();

                while ($currentDay <= $maintenance->finish_at) {
                    $dayKey = $currentDay->format('d');
                    $shift = ($currentDay->hour >= 6 && $currentDay->hour < 18) ? 'DS' : 'NS';

                    $status = 'Aktif';
                    $maintenanceDate = Carbon::parse($maintenance->date);
                    $maintenanceFinish = Carbon::parse($maintenance->finish_at);
                    
                    if ($maintenanceDate->format('Y-m-d') == now()->format('Y-m-d')){
                        $diffInDays = $maintenanceDate->diffInDays($maintenanceFinish);
                    }else{
                        $diffInDays = $maintenanceDate->diffInDays($currentDay);
                    }
                    
                    if ($diffInDays <= 1) {
                        $status = 'Ringan';
                    } elseif ($diffInDays > 1 && $diffInDays <= 2) {
                        $status = 'Sedang';
                    } elseif ($diffInDays > 2) {
                        $status = 'Berat';
                    }

                    // Update status hanya jika maintenance berlaku untuk shift ini
                    if ($status !== 'Aktif') {
                        $assetId = decrypt($asset->id);
                        $dataByDate[$assetId]['days'][$dayKey][$shift] = $status;
                    }

                    // Progress per 12 jam untuk mencakup kedua shift
                    $currentDay->addHours(12);
                }
            }
        }

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
    //     $schedules = StatusAsset::get()->groupBy('asset_id')->sortDesc();

    //     $data = [];
        
    //     foreach ($schedules as $key => $value) {
    //         $data_date = null;
    //         foreach (range(1, $daysInMonth) as $day) {
    //             $date = Carbon::parse($year.'-'.$month.'-'.$day)->format('Y-m-d');
    //             $data_date[] = StatusAsset::where('asset_id', $key)->whereDate('created_at', $date)->where('type', 'maintenance')->orderBy('created_at', 'desc')->get();
    //         }
            
    //         $data[] = [
    //             'asset_id' => $key,
    //             'data' => $data_date,
    //         ];
    //     }

    //     return $data;
    // }


    public function getMaintenanceSchedule()
    {
        // $data = Maintenance::select(
        //     'date',
        //     'status',
        //     'finish_at',
        //     'detail_problem',
        //     'urgention',
        //     'inspection_schedule_id',
        // )->get();

        // $schedule = [];
        
        // foreach ($data as $maintenance) {
        //     $inspectionShedjule = InspectionSchedule::find($maintenance->inspection_schedule_id);
        //     $assetId = 'AST-' . ($inspectionShedjule->asset_id ?? null);
        //     $date = Carbon::parse($maintenance->date)->format('Y-m-d');
        //     $shift = Carbon::parse($maintenance->date)->hour < 18 ? 'DS' : 'NS';

        //     if (!isset($schedule[$assetId])) {
        //         $schedule[$assetId] = [];
        //     }

        //     if (!isset($schedule[$assetId][$shift])) {
        //         $schedule[$assetId][$shift] = [];
        //     }

        //     $status = 'Aktif'; // Default status
        //     if ($maintenance->finish_at) {
        //         $start = Carbon::parse($maintenance->date);
        //         $finish = Carbon::parse($maintenance->finish_at);
        //         $diffDays = $finish->diffInDays($start);

        //         if ($diffDays <= 1) {
        //             $status = 'Ringan';
        //         } elseif ($diffDays == 2) {
        //             $status = 'Sedang';
        //         } else {
        //             $status = 'Berat';
        //         }
        //     }

        //     $schedule[$assetId][$shift][$date] = $status;
        // }

        // return $schedule;

        $startDate = Carbon::create(2023, 1, 1); // Contoh bulan Januari
        $endDate = Carbon::create(2023, 1, 31);
        
        $maintenances = Maintenance::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(['asset_id', function ($item) {
                return $item->date->format('d');
            }, 'shift']);

        return $maintenances;
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
