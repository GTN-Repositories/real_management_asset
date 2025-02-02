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
            $maintenance = InspectionSchedule::where('asset_id', Crypt::decrypt($asset_id))->where('status', '!=', 'RFU')->orderBy('date', 'desc')->first();
            $daysDiff = Carbon::now()->diffInDays(Carbon::parse($maintenance->date));

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
    }
}
