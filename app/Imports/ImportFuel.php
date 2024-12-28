<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\FuelConsumption;
use App\Models\Ipb;
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
        $asset_id = explode('AST - ', $row['asset_id'])[1] ?? $row['asset_id'];
        if ($project) {
            $fuel = FuelConsumption::create([
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

        $field['management_project_id'] = Crypt::decrypt($project->id);
        $field['usage_liter'] = $row['liter'];
        $field['date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']);

        $lastBalance = Ipb::where('management_project_id', Crypt::decrypt($project->id))
            ->orderBy('id', 'desc')
            ->value('balance');
        $unitprice = Ipb::where('management_project_id', Crypt::decrypt($project->id))
            ->orderBy('id', 'desc')
            ->value('unit_price');

        $lastBalance = $lastBalance ?? 0;
        $field['issued_liter'] = 0;
        $field['balance'] = $lastBalance - $field['usage_liter'];
        $field['unit_price'] = $unitprice;
        $field['fuel_id'] = Crypt::decrypt($fuel->id);
        Ipb::create($field);
    }
}
