<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Maintenance;
use App\Models\StatusAsset;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class StatusAssetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:status-asset-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $asset = Asset::where('status', 'UnderMaintenance')->pluck('id');
        $ringan = [
            'max' => 1
        ];
        $sedang = [
            'min' => 1,
            'max' => 2
        ];
        $berat = [
            'min' => 2,
        ];

        foreach ($asset as $key => $asset_id) {
                $maintenance = Maintenance::whereHas('inspection_schedule', function ($q) use ($asset_id) {
                                            $q->where('asset_id', Crypt::decrypt($asset_id));
                                        })
                                        ->where('status', '!=', 'RFU')
                                        ->orderBy('date', 'desc')->first();
            $daysDiff = round(Carbon::parse($maintenance->date)->diffInDays(now()));

            // CONDITION RINGAN, SEDANG, BERAT
            if ($daysDiff <= $ringan['max']) {
                StatusAsset::create([
                    'asset_id' => Crypt::decrypt($asset_id),
                    'status_before' => 'UnderMaintenance',
                    'status_after' => 'Ringan',
                    'type' => 'maintenance'
                ]);
            } elseif ($daysDiff > $sedang['min'] && $daysDiff <= $sedang['max']) {
                StatusAsset::create([
                    'asset_id' => Crypt::decrypt($asset_id),
                    'status_before' => 'Ringan',
                    'status_after' => 'Sedang',
                    'type' => 'maintenance'
                ]);
            } elseif ($daysDiff > $berat['min']) {
                StatusAsset::create([
                    'asset_id' => Crypt::decrypt($asset_id),
                    'status_before' => 'Sedang',
                    'status_after' => 'Berat',
                    'type' => 'maintenance'
                ]);
            }
        }

        
        // $this->syncAllDate();
    }

    public function syncAllDate()
    {
        $asset = Asset::pluck('id');

        $ringan = ['max' => 1];
        $sedang = ['min' => 1, 'max' => 2];
        $berat = ['min' => 2];

        foreach ($asset as $key => $asset_id) {
                $underMaintenance = Maintenance::whereHas('inspection_schedule', function ($q) use ($asset_id) {
                    $q->where('asset_id', Crypt::decrypt($asset_id));
                })
                ->where('status', '!=', 'RFU')
                // ->whereBetween('date', [$startDate, $endDate]) // Memfilter data inspeksi dari Nov 2025 sampai sekarang
                ->orderBy('date', 'desc')
                ->get();

            foreach ($underMaintenance as $ckey => $value) {
                if ($value->start_maintenance && $value->end_maintenance) {
                    $daysDiff = round(Carbon::parse($value->end_maintenance)->diffInDays(now()));
                } else {
                    $daysDiff = round(Carbon::parse($value->date)->diffInDays($value->estimate_finish));
                }

                // Menentukan status berdasarkan jumlah hari sejak inspeksi terakhir
                if ($daysDiff <= $ringan['max']) {
                    StatusAsset::create([
                        'asset_id' => Crypt::decrypt($asset_id),
                        'status_before' => 'UnderMaintenance',
                        'status_after' => 'Ringan',
                        'type' => 'maintenance',
                        'created_at' => $value->date
                    ]);
                } elseif ($daysDiff > $sedang['min'] && $daysDiff <= $sedang['max']) {
                    $createSedang = [
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'UnderMaintenance',
                            'status_after' => 'Ringan',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays(1)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'Ringan',
                            'status_after' => 'Sedang',
                            'type' => 'maintenance',
                            'created_at' => $value->date
                        ]
                        ];
                    StatusAsset::insert($createSedang);
                } elseif ($daysDiff > $berat['min']) {
                    $countBerat = $daysDiff - 2;
                    $createBerat = [
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'UnderMaintenance',
                            'status_after' => 'Ringan',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays($daysDiff -2)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'Ringan',
                            'status_after' => 'Sedang',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays($daysDiff - 1)
                        ],
                        
                    ];

                    for ($i=1; $i < $countBerat; $i++) {
                        $createBerat[] = [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'Sedang',
                            'status_after' => 'Berat',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays($daysDiff - $i)
                        ];
                    }
                    StatusAsset::insert($createBerat);
                }
            }

            $active = Maintenance::whereHas('inspection_schedule', function ($q) use ($asset_id) {
                    $q->where('asset_id', Crypt::decrypt($asset_id));
                })
                ->where('status', 'Active')
                ->orderBy('date', 'desc')
                ->get();

            foreach ($active as $ckey => $value) {
                if ($value->start_maintenance && $value->end_maintenance) {
                    $daysDiff = round(Carbon::parse($value->end_maintenance)->diffInDays(now()));
                } else {
                    $daysDiff = round(Carbon::parse($value->date)->diffInDays($value->estimate_finish));
                }

                if ($daysDiff <= $ringan['max']) {
                    $createActive = [
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'UnderMaintenance',
                            'status_after' => 'Ringan',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays(1)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'UnderMaintenance',
                            'status_after' => 'Active',
                            'type' => 'maintenance',
                            'created_at' => $value->date
                        ]
                    ];
                    StatusAsset::insert($createActive);
                }
            }

            $finish = Maintenance::whereHas('inspection_schedule', function ($q) use ($asset_id) {
                    $q->where('asset_id', Crypt::decrypt($asset_id));
                })
                ->whereIn('status', ['Finish', 'RFU'])
                ->orderBy('date', 'desc')
                ->get();

            foreach ($finish as $ckey => $value) {
                if ($value->start_maintenance && $value->end_maintenance) {
                    $daysDiff = round(Carbon::parse($value->end_maintenance)->diffInDays(now()));
                } else {
                    $daysDiff = round(Carbon::parse($value->date)->diffInDays($value->estimate_finish));
                }

                if ($daysDiff <= $ringan['max']) {
                    $createActive = [
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'UnderMaintenance',
                            'status_after' => 'Ringan',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays(1)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'RFU',
                            'status_after' => 'Active',
                            'type' => 'maintenance',
                            'created_at' => $value->date
                        ]
                    ];

                    StatusAsset::insert($createActive);
                }
            }
            
            $finish = Maintenance::whereHas('inspection_schedule', function ($q) use ($asset_id) {
                $q->where('asset_id', Crypt::decrypt($asset_id));
            })
            ->where('status', 'Scrap')
            ->orderBy('date', 'desc')
            ->get();

            foreach ($finish as $ckey => $value) {
                if ($value->start_maintenance && $value->end_maintenance) {
                    $daysDiff = round(Carbon::parse($value->end_maintenance)->diffInDays(now()));
                } else {
                    $daysDiff = round(Carbon::parse($value->date)->diffInDays($value->estimate_finish));
                }

                if ($daysDiff <= $ringan['max']) {
                    $createScrap = [
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'UnderMaintenance',
                            'status_after' => 'Ringan',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays(3)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'Ringan',
                            'status_after' => 'Sedang',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays(2)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'Sedang',
                            'status_after' => 'Berat',
                            'type' => 'maintenance',
                            'created_at' => Carbon::parse($value->date)->subDays(1)
                        ],
                        [
                            'asset_id' => Crypt::decrypt($asset_id),
                            'status_before' => 'Scrap',
                            'status_after' => 'Active',
                            'type' => 'maintenance',
                            'created_at' => $value->date
                        ]
                    ];
                    StatusAsset::insert($createScrap);
                }
            }
        }
    }
}
