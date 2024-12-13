<?php

namespace App\Exports;

use App\Models\ManagementProject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AssetExport implements FromView, WithDrawings
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

        return view('main.unit.excel', [
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
                $path = storage_path('app/public/' . $asset->image);
                if (file_exists($path)) {
                    $drawing->setPath($path);
                } else {
                    continue;
                }
                $drawing->setHeight(20);
                $drawing->setCoordinates('C' . ($index + 3));
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}
