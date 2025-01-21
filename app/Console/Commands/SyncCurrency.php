<?php

namespace App\Console\Commands;

use App\Http\Controllers\Main\CurrencyController;
use Illuminate\Console\Command;

class SyncCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-currency';

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
        $con = new CurrencyController();
        $con->syncCurrency();
    }
}
