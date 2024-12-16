<?php

namespace App\Console\Commands;

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
        \App\Models\Asset::where('status', 'UnderMaintenance')
            ->get()
            ->each(function ($asset) {
                $asset->update(['status' => 'Overdue']);
            });

        \App\Models\InspectionSchedule::where('status', 'UnderMaintenance')
            ->get()
            ->each(function ($inspection) {
                $inspection->update(['status' => 'Overdue']);
            });
    }
}
