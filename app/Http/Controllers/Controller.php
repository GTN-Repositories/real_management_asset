<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected function atomic(Closure $callback)
    {
        return DB::transaction($callback);
    }
}
