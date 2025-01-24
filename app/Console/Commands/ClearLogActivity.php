<?php

namespace App\Console\Commands;

use App\Models\LogActivity;
use Illuminate\Console\Command;

class ClearLogActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-log-activity';

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
        $data = LogActivity::get();

        foreach ($data as $item) {
            $item->delete();
        }

        $this->info('Log Activity has been cleared');
    }
}
