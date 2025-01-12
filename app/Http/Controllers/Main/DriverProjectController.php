<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ManagementProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DriverProjectController extends Controller
{
    public function index()
    {
        return view('main.driver.index');
    }

    public function data()
    {
        // Get user roles
        $userRoles = auth()->user()->roles->pluck('name')->toArray();

        // Get regular projects
        $projects = ManagementProject::select('id', 'name', 'asset_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($project) {
                $assetIds = $project->asset_id;
                $asset = Asset::whereIn('id', $assetIds)->take(4)->get();

                foreach ($asset as $key => $value) {
                    $value['ids'] = Crypt::decrypt($value['id']);
                }
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'assets' => $asset,
                ];
            });

        // Add All Projects card
        $allAssets = Asset::select('id', 'name')->take(4)->get();
        foreach ($allAssets as $key => $value) {
            $value['ids'] = Crypt::decrypt($value['id']);
        }
        $allProjectsCard = [
            'id' => 'all',
            'name' => 'All Projects',
            'assets' => $allAssets
        ];

        // Add All Projects card at the beginning
        $projects = collect([$allProjectsCard])->concat($projects);

        return response()->json($projects);
    }

    public function selectProject(Request $request)
    {
        $projectId = $request->input('project_id');

        if ($projectId === 'all') {
            session()->forget('selected_project_id');
        } else {
            session(['selected_project_id' => $projectId]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Project selected successfully!'
        ]);
    }
}
