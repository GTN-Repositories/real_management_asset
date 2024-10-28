<?php

namespace App\Http\Controllers;

use App\Models\ManagementProject;
use Closure;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected function atomic(Closure $callback)
    {
        return DB::transaction($callback);
    }

    protected function projectId()
    {
        $project_id = session()->get('project_id');

        if ($project_id) {
            return [$project_id];
        } else {
            return ManagementProject::pluck('id');
        }
    }
}
