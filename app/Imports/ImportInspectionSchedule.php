<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\InspectionSchedule;
use App\Models\ManagementProject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportInspectionSchedule implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        try {
            if ((isset($row['pic']) && $row['pic'] != '') && (isset($row['nama_project']) && $row['nama_project'] != '') && (isset($row['pic']) && $row['pic'] != '')) {
                $employee = Employee::where('name', $row['pic'] ?? '')->first();
                $asset_id = isset($row['asset_id']) ? explode('AST - ', $row['asset_id'])[1] ?? '' : '';
                $project = ManagementProject::where('name', $row['nama_project'] ?? '')->first();

                if ($employee && $project && $asset_id) {
                    $data = [
                        'name' => $row['name'],
                        'type' => $row['type'],
                        'asset_id' => $asset_id,
                        'management_project_id' => Crypt::decrypt($project->id),
                        'note' => $row['note'],
                        'status' => 'UnderInspection',
                        'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'] ?? ''),
                        'employee_id' => Crypt::decrypt($employee->id),
                        'urgention' => $row['urgention'],
                        'location' => $row['location'],
                        'estimate_finish' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['estimate_finish'] ?? ''),
                    ];
    
                    // dd($data);

                    return new InspectionSchedule($data);
                }
            }

        } catch (\Throwable $th) {
            dd($row, $th);
        }
    }
}
