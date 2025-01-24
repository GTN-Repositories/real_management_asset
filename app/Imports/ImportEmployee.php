<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\JobTitle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportEmployee implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        try {
            if (isset($row['nama']) && $row['nama'] != '') {
                $employee = Employee::where('name', $row['nama'] ?? '')->first();

                if (!$employee) {
                    $job_id = JobTitle::where('name', $row['jabatan'] ?? '')->first()->id ?? null;
                    $data = [
                        'name' => $row['nama'],
                        'job_title_id' => Crypt::decrypt($job_id),
                    ];
    
                    // dd($data);
                    return new Employee($data);
                }
            }

        } catch (\Throwable $th) {
            dd($row, $th);
        }
    }
}
