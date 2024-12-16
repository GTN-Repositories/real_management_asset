<?php

namespace App\Http\Controllers;

use App\Models\FuelConsumption;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportManPowerController extends Controller
{
    //
    public function index()
    {
        return view('main.report_manpower.index');
    }

    public function getHoursData(Request $request)
    {
        $query = FuelConsumption::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(hours) as total_hours')
            ->groupBy('month')
            ->orderBy('month', 'asc');

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [
                Carbon::parse($request->startDate)->startOfDay(),
                Carbon::parse($request->endDate)->endOfDay()
            ]);
        } else {
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
        }

        // Apply predefined filter if provided
        if ($request->filled('predefinedFilter')) {
            switch ($request->predefinedFilter) {
                case 'tahun ini':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
                case 'tahun kemarin':
                    $query->whereYear('date', Carbon::now()->subYear()->year);
                    break;
                case 'bulan ini':
                    $query->whereMonth('date', Carbon::now()->month)
                        ->whereYear('date', Carbon::now()->year);
                    break;
                case 'bulan kemarin':
                    $query->whereMonth('date', Carbon::now()->subMonth()->month)
                        ->whereYear('date', Carbon::now()->subMonth()->year);
                    break;
            }
        }

        if (session('selected_project_id')) {
            $query->whereHas('management_project', function ($q) {
                $q->where('id', Crypt::decrypt(session('selected_project_id')));
            });
        }

        $hoursData = $query->get();

        return response()->json([
            'months' => $hoursData->pluck('month')->toArray(),
            'hours' => $hoursData->pluck('total_hours')->toArray(),
        ]);
    }

    public function getDataProjectHours(Request $request)
    {
        $query = FuelConsumption::join('management_projects', 'fuel_consumptions.management_project_id', '=', 'management_projects.id')
            ->selectRaw('management_projects.id, management_projects.name, SUM(fuel_consumptions.hours) as total_hours')
            ->groupBy('management_projects.id', 'management_projects.name');

        $data = $query->get();

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('total_hours', function ($row) {
                return $row->total_hours;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }
}
