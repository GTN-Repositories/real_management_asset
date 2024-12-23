<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Loadsheet;
use Illuminate\Console\Command;

class Overdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:overdue';

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
        //
        Asset::where('status', 'UnderMaintenance')
            ->whereRaw('DATE_SUB(NOW(), INTERVAL 3 DAY) <= updated_at')
            ->get()
            ->each(function ($asset) {
                $asset->update(['status' => 'Overdue']);
            });

        InspectionSchedule::where('status', 'UnderMaintenance')
            ->whereRaw('DATE_SUB(NOW(), INTERVAL 3 DAY) <= updated_at')
            ->get()
            ->each(function ($inspection) {
                $inspection->update(['status' => 'Overdue']);
            });

        Loadsheet::select('asset_id')
            ->groupBy('asset_id')
            ->havingRaw('COUNT(*) = 1')
            ->whereRaw('DATE_SUB(NOW(), INTERVAL 7 DAY) >= MAX(updated_at)')
            ->get()
            ->each(function ($loadsheet) {
                Asset::where('id', $loadsheet->asset_id)->update(['status' => 'OnHold']);
            });
    }
}
