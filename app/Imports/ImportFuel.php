<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\FuelConsumption;
use App\Models\ManagementProject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportFuel implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     */
    public function model(array $row)
    {
        $project = ManagementProject::where('name', $row['nama_project'])->first();
        $employee = Employee::where('name', $row['employee'])->first();
        $asset_id = explode('AST - ', $row['asset_id'])[1];
        if ($project) {
            return new FuelConsumption([
                'management_project_id' => Crypt::decrypt($project->id),
                'asset_id' => $asset_id,
                'user_id' => Crypt::decrypt($employee->id) ?? $row['employee_id'],
                'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']),
                'liter' => $row['liter'],
                // 'price' => $row['price'],
                'category' => $row['category'],
                'lasted_km_asset' => $row['lasted_km_asset'],
                // 'loadsheet' => $row['loadsheet'],
                'hours' => $row['hours'],
            ]);
        }
    }
}

