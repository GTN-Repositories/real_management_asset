<?php

namespace App\Imports;

use App\Models\ManagementProject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportManageProject implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new ManagementProject([
            'asset_id'           => array_map('intval', explode(',', $row['asset_id'])), // Konversi ke JSON
            'employee_id'        => json_encode(array_map('intval', explode(',', $row['employee_id']))), // Konversi ke JSON
            'name'               => $row['name'],
            'start_date'         => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date']),
            'end_date'           => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date']), 
            'calculation_method' => $row['calculation_method'],
            'location'           => $row['location'],
            'value_project'      => $row['value_project'],
        ]);
    }
}
