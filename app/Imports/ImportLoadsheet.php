<?php

namespace App\Imports;

use App\Models\Loadsheet;
use App\Models\ManagementProject;
use App\Models\SoilType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportLoadsheet implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        $project = ManagementProject::where('name', $row['nama_project'])->first();
        if ($project) {
            return new Loadsheet([
                'management_project_id' => Crypt::decrypt($project->id),
                'asset_id' => $row['asset_id'],
                'employee_id' => $row['nama_karyawan'],
                'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']),
                'hours' => $row['hours'],
                'type' => $row['type'],
                'location' => $row['location'],
                'soil_type_id' => optional(SoilType::where('name', $row['soil_type_id'])->first())->id ? Crypt::decrypt(optional(SoilType::where('name', $row['soil_type_id'])->first())->id) : null,
                'kilometer' => $row['kilometer'],
                'loadsheet' => $row['loadsheet'],
                'perload' => $row['perload'],
                'lose_factor' => $row['lose_factor'],
                'cubication' => $row['cubication'],
                'price' => $row['price'],
                'billing_status' => $row['billing_status'],
                'remarks' => $row['remarks'],
                'bpit' => $row['bpit'],
            ]);
        }
    }
}
