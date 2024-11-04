<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ManagementProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverProjectController extends Controller
{
    //
    public function index()
    {
        return view('main.driver.index');
    }

    public function data()
    {
        $data = ManagementProject::select('name', 'asset_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->name,
                    'assets' => Asset::whereIn('id', $group->pluck('asset_id'))->get(),
                ];
            })
            ->values();

        return response()->json($data);
    }

    public function selectProject(Request $request)
    {
        $managerName = $request->input('manager_name'); // Ambil manager name dari request
        session(['selected_manager_name' => $managerName]); // Simpan manager name ke session

        return response()->json(['status' => 'success', 'message' => 'Project selected successfully!']);
    }
}
