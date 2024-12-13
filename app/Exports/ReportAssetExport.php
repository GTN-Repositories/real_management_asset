<?php

namespace App\Exports;

use App\Models\ManagementProject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportAssetExport implements FromView, WithDrawings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $assets = $this->data->map(function ($asset) {
            $asset->managementProjects = ManagementProject::where('asset_id', Crypt::decrypt($asset->id))->get();
            return $asset;
        });

        return view('main.report_asset.excel', [
            'assets' => $assets,
        ]);
    }

    public function drawings()
    {
        $drawings = [];

        foreach ($this->data as $index => $asset) {
            if ($asset->image) {
                $drawing = new Drawing();
                $drawing->setName('Asset Image');
                $drawing->setDescription('Image of asset');
                $drawing->setPath(storage_path('app/public/' . $asset->image));
                $drawing->setHeight(20);
                $drawing->setCoordinates('AE' . ($index + 3));
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}
