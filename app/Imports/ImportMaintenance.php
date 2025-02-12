<?php

namespace App\Imports;

use App\Models\Maintenance;
use App\Models\MaintenanceSparepart;
use App\Models\Werehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportMaintenance implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        try {
            if (isset($row['inspection_schedule_id']) && $row['inspection_schedule_id'] != '') {
                $inspection_schedule_id = isset($row['inspection_schedule_id']) ? explode('INS-', $row['inspection_schedule_id'])[1] ?? '' : '';
                
                if ($inspection_schedule_id) {
                    $data = [
                        'name' => $row['name'],
                        'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'] ?? ''),
                        'workshop' => $row['workshop'],
                        'inspection_schedule_id' => $inspection_schedule_id,
                        'employee_id' => json_encode([$row['mekanik']]),
                        'status' => $row['status'],
                        'code_delay' => $row['code_delay'],
                        'delay_reason' => $row['delay_reason'],
                        'estimate_finish' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['estimate_finish'] ?? ''),
                        'delay_hours' => $row['delay_hours'],
                        'start_maintenace' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_maintenace'] ?? ''),
                        'end_maintenace' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_maintenace'] ?? ''),
                        'deviasi' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['deviasi'] ?? ''),
                        'finish_at' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['finish_at'] ?? ''),
                        'hm' => $row['hm'],
                        'km' => $row['km'],
                        'location' => $row['location'],
                        'detail_problem' => $row['detail_problem'],
                        'action_to_do' => $row['action_to_do'],
                        'urgention' => $row['urgention'],
                        'pic' => $row['pic'],
                    ];
                    // dd($data);
                    $maintenance = Maintenance::create($data);

                    $warehouse = Werehouse::where('name', $row['warehouse'] ?? '')->first();

                    $createSparepart = false;
                    $success = '';
                    $i = 1;
                    while ($createSparepart == false) {
                        if (isset($row["item_id_$i"]) && $row["item_id_$i"] != '' && isset($row["qty_item_$i"]) && $row["qty_item_$i"] != '') {
                            $item_id = isset($row["item_id_$i"]) ? explode('SPR-', $row["item_id_$i"])[1] ?? '' : '';
                            $asset_id = isset($row["replacing_asset_id_$i"]) ? (int)explode('AST - ', $row["replacing_asset_id_$i"])[1] ?? null : null;
                            $qty = $row["qty_item_$i"];

                            if (isset($asset_id) && $asset_id != null) {
                                $type = 'Replacing';
                            } else {
                                $type = 'Stock';
                            }

                            $dataSparepart = [
                                'maintenance_id' => Crypt::decrypt($maintenance->id),
                                'warehouse_id' => Crypt::decrypt($warehouse->id),
                                'item_id' => $item_id,
                                'asset_id' => $asset_id,
                                'quantity' => $qty,
                                'type' => $type,
                            ];

                            $success = new MaintenanceSparepart($dataSparepart);

                            $i++;
                        } else {
                            $createSparepart = true;
                        }
                    }

                    return $success;
                }
            }

        } catch (\Throwable $th) {
            dd($row, $th);
        }
    }
}
