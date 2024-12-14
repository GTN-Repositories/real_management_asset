<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProjectTemplateExport implements FromView
{
    public function view(): View
    {
        return view('main.management_project.template');
    }
}
