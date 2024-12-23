<?php

namespace App\Imports;

use App\Models\Employee;
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
        try {
            $project = ManagementProject::where('name', $row['nama_project'] ?? '')->first();
            $asset_id = isset($row['asset_id']) ? explode('AST - ', $row['asset_id'])[1] ?? '' : '';
            $employee = Employee::where('name', $row['nama_karyawan'] ?? '')->first();

            $soil_type_id = optional(SoilType::where('name', $row['soil_type_id'] ?? '')->first())->id ? Crypt::decrypt(optional(SoilType::where('name', $row['soil_type_id'] ?? '')->first())->id) : null;
            $cubication = ($row['total_load'] ?? 0 * $row['perload'] ?? 0) * $row['lose_factor'] ?? 0;
            $soilType = SoilType::find($soil_type_id);
            $price = (int)($cubication * ($soilType ? $soilType->value : 0));

            if ($project) {
                $data = [
                    'management_project_id' => Crypt::decrypt($project->id),
                    'asset_id' => $asset_id,
                    'employee_id' => Crypt::decrypt($employee->id) ?? $row['employee_id'],
                    'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'] ?? ''),
                    'hours' => $row['hours'] ?? 0,
                    'type' => $row['jenis_pekerjaan'] ?? '',
                    'location' => $row['location'] ?? '',
                    'soil_type_id' => $soil_type_id,
                    'kilometer' => $row['kilometer'] ?? '',
                    'loadsheet' => $row['total_load'] ?? 0,
                    'perload' => $row['perload'] ?? 0,
                    'lose_factor' => $row['lose_factor'] ?? 0,
                    'cubication' => $cubication,
                    'price' => $price,
                    'billing_status' => $row['billing_status'] ?? '',
                    'remarks' => $row['remarks'] ?? '',
                    'bpit' => $row['bpit'] ?? '',
                ];

                // dd($data);
                return new Loadsheet($data);
            }
        } catch (\Throwable $th) {
            dd($row, $row['asset_id'], $th);
        }
    }
}
