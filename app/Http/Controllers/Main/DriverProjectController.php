<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ManagementProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverProjectController extends Controller
{
    public function index()
    {
        return view('main.driver.index');
    }

    public function data()
    {
        $data = ManagementProject::select('id', 'name', 'asset_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($project) {
                $assetIds = $project->asset_id;
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'assets' => Asset::whereIn('id', $assetIds)->get(),
                ];
            });

        return response()->json($data);
    }

    public function selectProject(Request $request)
    {
        $projectId = $request->input('project_id');
        session(['selected_project_id' => $projectId]);

        return response()->json(['status' => 'success', 'message' => 'Project selected successfully!']);
    }
}
