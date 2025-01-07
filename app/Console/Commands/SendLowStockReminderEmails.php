<?php

namespace App\Console\Commands;

use App\Mail\LowStockReminderEmail;
use App\Models\GeneralSetting;
use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLowStockReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-low-stock-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat email untuk stok barang yang mendekati atau di bawah batas minimum.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $general = GeneralSetting::where('group', 'reminder')->where('key', 'low_stock_reminder_period')->orderBy('id', 'desc')->first();
        $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');

        if ($general) {
            $reminderPeriod = $general->value;
        } else {
            $reminderPeriod = 30;
            Log::info('Reminder period for low stock not found. Using default value: ' . $reminderPeriod);
        }

        if ($general->status == 'active') {
            $item = Item::lowStock()->get();

            foreach ($item as $stock) {
                // Log::info('Sending email reminder for low stock item: ' . $stock);
                Mail::to($generalEmailSmtp)->send(new LowStockReminderEmail($stock));
            }

            $this->info('Pengingat email untuk stok rendah telah dikirim.');
        }
    }
}
